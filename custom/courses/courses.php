<?php
/**
 * Name: Courses
 * Description: Lets you build MOOCs with the webpages feature
 * Version: 0.17
 * Author: Alexandre Hannud Abdo <abdo@member.fsf.org>
 * ToDo: most of it
 */

function courses_install(){
  $r=q("CREATE TABLE IF NOT EXISTS `coursevisits` (
  `coursevisits_id`    int(10) unsigned NOT NULL AUTO_INCREMENT,
  `coursevisits_xchan` char(255)        NOT NULL DEFAULT '',
  `coursevisits_time`  datetime         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `coursevisits_pagepath`  char(46)    NOT NULL DEFAULT '',
  `coursevisits_tag`   char(32)        NOT NULL DEFAULT '',
  PRIMARY KEY (`coursevisits_id`),
  UNIQUE (coursevisits_xchan,coursevisits_pagepath,coursevisits_tag)
  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");
  return;
}

function courses_uninstall() {
	return;
}

function courses_load() {
	register_hook('init_0','addon/courses/courses.php','courses_loader');
}

function courses_unload() {
	unregister_hook('init_0','addon/courses/courses.php','courses_loader');
}

function courses_loader() {};

// COURSE WIDGETS



function courses_register_visit($pagepath, $tag, $xchan = NULL, $datetime = NULL) {
	if ($datetime === NULL) $datetime = datetime_convert();
	if ($xchan === NULL) $xchan = get_observer_hash();
	// TODO: verificar que a página existe neste servidor
	$r=q("INSERT INTO `coursevisits` (coursevisits_xchan, coursevisits_time, coursevisits_pagepath, coursevisits_tag) values (%d, %d, %d, %d) ON DUPLICATE KEY UPDATE id=id",
		dbesc($xchan),
		dbesc($datetime),
		dbesc($pagepath),
		dbesc($tag)
	);
}

function courses_has_visited($pagepath, $tag, $xchan = NULL) {
	if ($xchan === NULL) $xchan = get_observer_hash();
	$r=q("select * from coursevisits where coursevisits_xchan = %d and coursevisits_pagepath = %d and coursevisits_tag = %d limit 1",
		dbesc($xchan),
		dbesc($pagepath),
		dbesc($tag)
	);
	return (is_array($r) and count($r)>0) ? true : false;
}

function courses_menu_attr($pagepath) {
	$o= ''; $o += ' ';
	if((!strpos($pagepath,':') === false) or substr_count($pagepath,'/') != 2) return '';

	list($module,$member,$page) = explode('/',$pagepath);
	if($module!='page') return '';

	$s = q("select channel_id from channel where channel_address = %d limit 1",
			dbesc($member)
		);
	$channel_id = $s[0]['channel_id'];
	$r = q("select sid from item inner join item_id on item_id.iid = item.id and item_id.uid = item.uid and item.uid = %d and item_id.service = 'BUILDBLOCK' and item_id.sid like '%s-seq-%%'",
			intval($channel_id),
			dbesc($page)
		);
	function n($x){ return end(explode('-',$x['sid']));}
	function cmp($a, $b){
		if (n($a)==n($b)) return 0;
		return (n($a) < n($b)) ? -1 : 1;
	}
	uasort($r, 'cmp');

	$data= [];
	foreach($r as $rr){
		$sid= $rr['sid'];
		$visited= courses_has_visited($pagepath,$sid);
		if($visited)
			$data[]= $sid;
		else {
			if(count($r) > 1)
				$data[]= $sid;
			break;
		}
	}
	$o += ' data="'.implode(' ',$data).'"';
	$o += ' class="' . (count($r) == count($data) ? 'menu-item-complete' : 'menu-item-incomplete') . '"';

	return $o;
}


/* ACTUAL WIDGETS BELOW */


function widget_coursetabs($arr){
	$o= '';

	$name= argv(2);
	function n($x){ return end(explode('-',$x['sid']));}

	$o .= widget_courserpost(['name'=>$name,]);

	$channel_id = comanche_get_channel_id();
	$r = q("select * from item inner join item_id on item_id.iid = item.id and item_id.uid = item.uid and item.uid = %d and item_id.service = 'BUILDBLOCK' and item_id.sid like '%s-seq-%%'",
			intval($channel_id),
			dbesc($name)
		);
	function cmp($a, $b){
		if (n($a)==n($b)) return 0;
		return (n($a) < n($b)) ? -1 : 1;
	}
	uasort($r, 'cmp');

	$o.='<div id="' . $name . '-seqtabs"><ul>';
	foreach($r as $rr){
		$title = $rr['title'] ? $rr['title'] : n($rr) ;
		$o .= '<li><a href="#' . $rr['sid'] . '">' . $title . '</a></li>';		
	}
	$o .= '</ul>';
	foreach($r as $rr){
		$o .= '<div id="' . $rr['sid'] . '">';
		$o .= prepare_text($rr['body'], $rr['mimetype']);
		$o .= '</div>';
	}
	$o .= '<div class="sequence-buttons">';
	$o .= '<button class="sequence-button sequence-button-previous" disabled="disabled">Voltar</button>';
	$o .= '<button class="sequence-button sequence-button-next">Seguir</button>';
	$o .= '</div>';

/*	$k = array_keys($r);
	for($i=0; $i < count($k); $i++){
		$kk = $k[$i];
		$o .= '<div id="' . $r[$kk]['sid'] . '">';
		$o .= prepare_text($r[$kk]['body'], $r[$kk]['mimetype']);
		$o .= '</div>';

		$o .= '<div class="seqprevnext">';
		$prevhref = (i==0) ? '' : $r[$k[$i-1]]['sid'];
		$nexthref = (i==count($k)-1) ? '' : $r[$k[$i+1]]['sid'];
		if($i!=0)
			$o .= '<a href="#'. $prevhref .'" class="seqprev">Voltar</span>';
		$o .= ' | ';
		if($i!=count($k-1))
			$o .= '<a href="#'. $nexthref .'" class="seqnext">Seguir</span>';
		$o .= '</div>';
	} */

	$o .= '</div>'; // -seqtabs
	return $o;
}

function widget_courserpost($arr){
	$a = get_app();
	$_SESSION['rpost'] = ["body"=>"Troque este texto pelo relato da sua experiência! Se preferir falar, clique no clip abaixo para anexar uma gravação de áudio ou vídeo a esta mensagem.", "title"=>"Minha experiência no módulo " . $arr['name'], "remote_return"=>$a->get_baseurl() . $_SERVER[REQUEST_URI] . '#' . $arr['name'] . '-seq-z', "source"=>"Curso de Atenção Plena",];
	require_once("mod/rpost.php");
	$rpost_div = '<div id="rpost-data" style="display:none">' . rpost_content($a) . '</div>';
	return $rpost_div;
}

function widget_coursemenu($arr){
	$o= '';

	$o += '<div id="accordion">';

	$deep = false;
	foreach($arr as $key => $value){
		$type = explode('_', $key);
		if ($type[0] == 'header' and $type[1] == 'title') { if($deep) $o += '</div>'; $o += '<h3>'.$value.'</h3><div>'; $deep=true;}
		if ($type[0] == 'item' and $type[1] == 'href') $o += '<p><a href="'.$value.'" '.courses_menu_attr($value).'>';
		if ($type[0] == 'item' and $type[1] == 'title') $o += $value.'</a></p>';
	}
	$o += '</div>';
	$deep = false;
	$o += '</div>';

	return $o;
}

// END COURSE WIDGETS
function courses_module() { return; }

function courses_init($a){
	if (! local_channel())
		return;
	if (! ( ($a->account['account_service_class'] === 'p2s') or ($a->account['account_service_class'] === 'p8s') ) )
		return;

	$pagepath=argv(2).'/'.argv(3).'/'.argv(4);
	$tag=argv(5);
	if (argv(1)=='visit'){
		courses_register_visit($pagepath, $tag);
		killme();
	}

	if (argv(1)=='hasvisited'){
		return courses_has_visited($pagepath, $tag);
	}
	killme();
}
