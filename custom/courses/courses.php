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
	return;
//  register_hook('hook_name', 'addon/mascara/courses.php', 'courses_hook_name');
}

function courses_unload() {
	return;
//  unregister_hook('hook_name', 'addon/mascara/courses.php', 'courses_hook_name');
}

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

require_once('addon/courses/courses_widgets.php');

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
