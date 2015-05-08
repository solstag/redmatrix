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
//	register_hook('init_0','addon/courses/courses.php','courses_loader');
}

function courses_unload() {
//	unregister_hook('init_0','addon/courses/courses.php','courses_loader');
}

function courses_loader() {};

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
/*
	if (argv(1)=='hasvisited'){
		return courses_has_visited($pagepath, $tag);
	}
*/
	killme();
}
