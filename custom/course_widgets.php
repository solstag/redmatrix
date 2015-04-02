<?php
/**
 * @file custom/widgets.php
 *
 * @brief This file contains widgets to make course pages.
 */

function widget_coursetabs($arr){
	$o = '';
	$o .= widget_courserpost([]);

	$channel_id = comanche_get_channel_id();
	$name= argv(2);
	$r = q("select * from item inner join item_id on iid = item.id and item_id.uid = item.uid and item.uid = %d and service = 'BUILDBLOCK' and sid like '%s-seq-%%' limit 1",
			intval($channel_id),
			dbesc($name)
		);
	function n($x){ return end(explode('-',$x));}
	function cmp($a, $b){
		if (n($a)==n($b)) return 0;
		return (n($a) < n($b)) ? -1 : 1;
	}
	uasort($r, 'cmp');

	$o.='<div id="' . $name . '-seqtabs"><ul>';
	foreach($r as $rr){
		$title = $rr['title'] ? $rr['title'] : n($rr['sid']) ;
		$o .= '<li id="' . $rr['sid'] . '">' . $title . '</li>';		
	}
	$o .= '</ul>';
	foreach($r as $rr){
		$o .= '<div id="' . $rr['sid'] . '">';
		$o .= prepare_text($rr['body'], $rr['mimetype']);
		$o .= '</div>';
	}
	$o .= '</div>';
	return $o;
}

function widget_courserpost($arr){
	$a = get_app();
	$_SESSION['rpost'] = ["body"=>"Troque este texto pelo relato da sua experiência! Se preferir falar, anexe uma gravação de áudio sua, ou mesmo um vídeo.", "title"=>"Minha experiência no módulo", "remote_return"=>$a->get_baseurl() . $_SERVER[REQUEST_URI], "source"=>"Curso de Atenção Plena",];
	require_once("mod/rpost.php");
	$rpost_div = '<div id="rpost-data" style="display:none">' . rpost_content($a) . '</div>';
	return $rpost_div;
}
