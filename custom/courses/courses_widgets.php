<?php
/**
 * @file custom/widgets.php
 *
 * @brief Widgets for course pages used togheter with the sequence template
 */

function widget_coursetabs($arr){
	$o = '';

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
	$o = '';

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

