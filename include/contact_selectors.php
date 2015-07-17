<?php /** @file */


function contact_profile_assign($current) {

	$o = '';

	$o .= "<select id=\"contact-profile-selector\" name=\"profile_assign\" class=\"form-control\"/>\r\n";

	$r = q("SELECT profile_guid, profile_name FROM `profile` WHERE `uid` = %d",
		intval($_SESSION['uid']));

	if($r) {
		foreach($r as $rr) {
			$selected = (($rr['profile_guid'] == $current) ? " selected=\"selected\" " : "");
			$o .= "<option value=\"{$rr['profile_guid']}\" $selected >{$rr['profile_name']}</option>\r\n";
		}
	}
	$o .= "</select>\r\n";
	return $o;
}

/* unused currently

function contact_reputation($current) {

	$o = '';
	$o .= "<select id=\"contact-reputation-selector\" name=\"reputation\" />\r\n";

	$rep = array(
		0 => t('Unknown | Not categorized'),
		1 => t('Block immediately'),
		2 => t('Shady, spammer, self-marketer'),
		3 => t('Known to me, but no opinion'),
		4 => t('OK, probably harmless'),
		5 => t('Reputable, has my trust')
	);

	foreach($rep as $k => $v) {
		$selected = (($k == $current) ? " selected=\"selected\" " : "");
		$o .= "<option value=\"$k\" $selected >$v</option>\r\n";
	}
	$o .= "</select>\r\n";
	return $o;
}

*/

function contact_poll_interval($current, $disabled = false) {

	$dis = (($disabled) ? ' disabled="disabled" ' : '');
	$o = '';
	$o .= "<select id=\"contact-poll-interval\" name=\"poll\" $dis />" . "\r\n";

	$rep = array(
		0 => t('Frequently'),
		1 => t('Hourly'),
		2 => t('Twice daily'),
		3 => t('Daily'),
		4 => t('Weekly'),
		5 => t('Monthly')
	);

	foreach($rep as $k => $v) {
		$selected = (($k == $current) ? " selected=\"selected\" " : "");
		$o .= "<option value=\"$k\" $selected >$v</option>\r\n";
	}
	$o .= "</select>\r\n";
	return $o;
}


function network_to_name($s) {

	$nets = array(
		NETWORK_DFRN     => t('Friendica'),
		NETWORK_OSTATUS  => t('OStatus'),
		NETWORK_FEED     => t('RSS/Atom'),
		NETWORK_MAIL     => t('Email'),
		NETWORK_DIASPORA => t('Diaspora'),
		NETWORK_FACEBOOK => t('Facebook'),
		NETWORK_ZOT      => t('Zot!'),
		NETWORK_LINKEDIN => t('LinkedIn'),
		NETWORK_XMPP     => t('XMPP/IM'),
		NETWORK_MYSPACE  => t('MySpace'),
	);

	call_hooks('network_to_name', $nets);

	$search  = array_keys($nets);
	$replace = array_values($nets);

	return str_replace($search,$replace,$s);

}
