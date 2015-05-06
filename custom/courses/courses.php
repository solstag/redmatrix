<?php
/**
 * Name: Courses
 * Description: Lets you build MOOCs with the webpages feature
 * Version: 0.17
 * Author: Alexandre Hannud Abdo <abdo@member.fsf.org>
 * ToDo: most of it
 */


function courses_load() {
//  register_hook('jot_tool', 'addon/mascara/mascara.php', 'courses_jot_tool');
}

function courses_unload() {
//  unregister_hook('jot_tool', 'addon/mascara/mascara.php', 'courses_jot_tool');
}

function courses_jot_tool(&$a, &$b) {
  if (! ($a->profile_uid and $a->profile_uid == local_channel() ) )
    return;
  if (! ($a->account['account_service_class'] === 'ppsus') )
    return;
  /**
   * load css
   */ 
  $a->page['htmlhead'] .= '<link rel="stylesheet" href="'.$a->get_baseurl().'/addon/mascara/mascara.css" type="text/css" media="screen" />' . "\r\n";
  /**
   * load js
   */ 
  $a->page['htmlhead'] .= '<script src="'.$a->get_baseurl().'/addon/mascara/mascara.js"></script>' . "\r\n";

  /**
   * load chosen
   */
  $a->page['htmlhead'] .= '<script src="'.$a->get_baseurl().'/addon/mascara/chosen/chosen.jquery.min.js"></script>' . "\r\n";
  $a->page['htmlhead'] .= '<link rel="stylesheet" href="'.$a->get_baseurl().'/addon/mascara/chosen/chosen.min.css" type="text/css" media="screen" />' . "\r\n";
}

function courses_module() { return; }

function courses_init($a){
  if (! ($a->account['account_service_class'] === 'ppsus') )
    killme();

  if (argc()<2)
    killme();
  if (argv(1)=='form'){
    killme();
  }
  if (argv(1)=='connect'){
    killme();
  }
  killme();
}
