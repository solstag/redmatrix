<?php

require_once('include/identity.php');
require_once('include/permissions.php');


function new_channel_init(&$a) {

	$cmd = ((argc() > 1) ? argv(1) : '');


	if($cmd === 'autofill.json') {
		require_once('library/urlify/URLify.php');
		$result = array('error' => false, 'message' => '');
		$n = trim($_REQUEST['name']);

		$x = strtolower(URLify::transliterate($n));

		$test = array();

		// first name
		if(strpos($x,' '))
			$test[] = legal_webbie(substr($x,0,strpos($x,' ')));
		if($test[0]) {
			// first name plus first initial of last
			$test[] = ((strpos($x,' ')) ? $test[0] . legal_webbie(trim(substr($x,strpos($x,' '),2))) : '');
			// first name plus random number
			$test[] = $test[0] . mt_rand(1000,9999);
		}
		// fullname
		$test[] = legal_webbie($x);
		// fullname plus random number
		$test[] = legal_webbie($x) . mt_rand(1000,9999);

		json_return_and_die(check_webbie($test));
	}

	if($cmd === 'checkaddr.json') {
		require_once('library/urlify/URLify.php');
		$result = array('error' => false, 'message' => '');
		$n = trim($_REQUEST['nick']);

		$x = strtolower(URLify::transliterate($n));

		$test = array();

		$n = legal_webbie($x);
		if(strlen($n)) {
			$test[] = $n;
			$test[] = $n . mt_rand(1000,9999);
		}

		for($y = 0; $y < 100; $y ++)
			$test[] = 'id' . mt_rand(1000,9999);

		json_return_and_die(check_webbie($test));
	}


}


function new_channel_post(&$a) {

	$arr = $_POST;

	if(($arr['account_id'] = get_account_id()) === false) {
		notice( t('Permission denied.') . EOL );
		return;
	}

	$result = create_identity($arr);

	if(! $result['success']) {
		notice($result['message']);
		return;
	}

	$newuid = $result['channel']['channel_id'];

	change_channel($result['channel']['channel_id']);

	if(! strlen($next_page = get_config('system','workflow_channel_next')))
		$next_page = 'settings';
	
	goaway(z_root() . '/' . $next_page);

}







function new_channel_content(&$a) {

	if(! get_account_id()) {
		notice( t('Permission denied.') . EOL);
		return;
	}

	$name         = ((x($_REQUEST,'name'))         ? $_REQUEST['name']         :  "" );
	$nickname     = ((x($_REQUEST,'nickname'))     ? $_REQUEST['nickname']     :  "" );
	$privacy_role = ((x($_REQUEST,'permissions_role')) ? $_REQUEST['permissions_role'] :  "" );

	$o = replace_macros(get_markup_template('new_channel.tpl'), array(

		'$title'        => t('Add a Channel'),
		'$desc'         => t('A channel is your own collection of related web pages. A channel can be used to hold social network profiles, blogs, conversation groups and forums, celebrity pages, and much more. You may create as many channels as your service provider allows.'),

		'$label_name'   => t('Channel Name'),
		'$help_name'    => t('Examples: "Bob Jameson", "Lisa and her Horses", "Soccer", "Aviation Group" '),
		'$label_nick'   => t('Choose a short nickname'),
		'$nick_desc'    => t('Your nickname will be used to create an easily remembered channel address (like an email address) which you can share with others.'),
		'$label_import' => t('Or <a href="import">import an existing channel</a> from another location'),
		'$name'         => $name,
		'$help_role'    => t('Please choose a channel type (such as social networking or community forum) and privacy requirements so we can select the best permissions for you'),
		'$role' => array('permissions_role' , t('Channel Type'), ($privacy_role) ? $privacy_role : 'social', '<a href="help/roles" target="_blank">'.t('Read more about roles').'</a>',get_roles()),
		'$nickname'     => $nickname,
		'$submit'       => t('Create')
	));

	return $o;

}

