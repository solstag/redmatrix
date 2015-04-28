<?php
/**
 * @file custom/widgets.php
 *
 * @brief Widgets for course pages used togheter with the sequence template
 */

function widget_coursetabs($arr){
	$o = '';

	$channel_id = comanche_get_channel_id();
	$name= argv(2);

	$o .= widget_courserpost(['name'=>$name,]);

	$r = q("select * from item inner join item_id on iid = item.id and item_id.uid = item.uid and item.uid = %d and service = 'BUILDBLOCK' and sid like '%s-seq-%%'",
			intval($channel_id),
			dbesc($name)
		);
	function n($x){ return end(explode('-',$x['sid']));}
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
	$_SESSION['rpost'] = ["body"=>"Troque este texto pelo relato da sua experiência! Se preferir falar, anexe uma gravação de áudio sua, ou mesmo um vídeo.", "title"=>"Minha experiência no módulo " . $arr['name'], "remote_return"=>$a->get_baseurl() . $_SERVER[REQUEST_URI] . '#' . $arr['name'] . '-seq-z', "source"=>"Curso de Atenção Plena",];
	require_once("mod/rpost.php");
	$rpost_div = '<div id="rpost-data" style="display:none">' . rpost_content($a) . '</div>';
	return $rpost_div;
}
