<?php

require_once('include/zot.php');

function probe_content(&$a) {

	$o .= '<h3>Probe Diagnostic</h3>';

	$o .= '<form action="probe" method="get">';
	$o .= 'Lookup address: <input type="text" style="width: 250px;" name="addr" value="' . $_GET['addr'] .'" />';
	$o .= '<input type="submit" name="submit" value="Submit" /></form>'; 

	$o .= '<br /><br />';

	if(x($_GET,'addr')) {
		$channel = $a->get_channel();
		$addr = trim($_GET['addr']);
		$do_import = ((intval($_GET['import']) && is_site_admin()) ? true : false);
		$res = zot_finger($addr,$channel,false);
		$o .= '<pre>';
		if($res['success'])
			$j = json_decode($res['body'],true);
		else {
			$o .= sprintf( t('Fetching URL returns error: %1$s'),$res['error'] . "\r\n\r\n");
			$o .= "<strong>https connection failed. Trying again with auto failover to http.</strong>\r\n\r\n";
			$res = zot_finger($addr,$channel,true);
			if($res['success'])
				$j = json_decode($res['body'],true);
			else
				$o .= sprintf( t('Fetching URL returns error: %1$s'),$res['error'] . "\r\n\r\n");

		}
		if($do_import && $j)
			$x = import_xchan($j);
		if($j && $j['permissions'] && $j['permissions']['iv'])
			$j['permissions'] = json_decode(crypto_unencapsulate($j['permissions'],$channel['channel_prvkey']),true);
		$o .= str_replace("\n",'<br />',print_r($j,true));
		$o .= '</pre>';
	}
	return $o;
}
