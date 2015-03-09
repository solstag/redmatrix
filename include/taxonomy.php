<?php /** @file */

// post categories and "save to file" use the same item.file table for storage.
// We will differentiate the different uses by wrapping categories in angle brackets
// and save to file categories in square brackets.
// To do this we need to escape these characters if they appear in our tag. 

function file_tag_encode($s) {
	return str_replace(array('<','>','[',']'),array('%3c','%3e','%5b','%5d'),$s);
}

function file_tag_decode($s) {
	return str_replace(array('%3c','%3e','%5b','%5d'),array('<','>','[',']'),$s);
}

function file_tag_file_query($table,$s,$type = 'file') {

	if($type == 'file')
		$termtype = TERM_FILE;
	else
		$termtype = TERM_CATEGORY;

	return sprintf(" AND " . (($table) ? dbesc($table) . '.' : '') . "id in (select term.oid from term where term.type = %d and term.term = '%s' and term.uid = " . (($table) ? dbesc($table) . '.' : '') . "uid ) ",
		intval($termtype),
		protect_sprintf(dbesc($s))
	);
}

function term_query($table,$s,$type = TERM_UNKNOWN) {

	return sprintf(" AND " . (($table) ? dbesc($table) . '.' : '') . "id in (select term.oid from term where term.type = %d and term.term = '%s' and term.uid = " . (($table) ? dbesc($table) . '.' : '') . "uid ) ",
		intval($type),
		protect_sprintf(dbesc($s))
	);
}


function store_item_tag($uid,$iid,$otype,$type,$term,$url = '') {
	if(! $term) 
		return false;

	$r = q("select * from term 
		where uid = %d and oid = %d and otype = %d and type = %d 
		and term = '%s' and url = '%s' ",
		intval($uid),
		intval($iid),
		intval($otype),
		intval($type),
		dbesc($term),
		dbesc($url)
	);
	if($r)
		return false;

	$r = q("insert into term (uid, oid, otype, type, term, url)
		values( %d, %d, %d, %d, '%s', '%s') ",
		intval($uid),
		intval($iid),
		intval($otype),
		intval($type),
		dbesc($term),
		dbesc($url)
	);

	return $r;
}


function get_terms_oftype($arr,$type) {
	$ret = array();
	if(! (is_array($arr) && count($arr)))
		return $ret;

	if(! is_array($type))
		$type = array($type);

	foreach($type as $t)
		foreach($arr as $x)
			if($x['type'] == $t)
				$ret[] = $x;

	return $ret;
}

function format_term_for_display($term) {
	$s = '';
	if($term['type'] == TERM_HASHTAG)
		$s .= '#';
	elseif($term['type'] == TERM_MENTION)
		$s .= '@';
	else
		return $s;

	if($term['url']) 
		$s .= '<a href="' . $term['url'] . '">' . htmlspecialchars($term['term'], ENT_COMPAT,'UTF-8') . '</a>';
	else 
		$s .= htmlspecialchars($term['term'], ENT_COMPAT,'UTF-8');
	return $s;
}

// Tag cloud functions - need to be adpated to this database format


function tagadelic($uid, $count = 0, $authors = '', $flags = 0, $restrict = 0, $type = TERM_HASHTAG) {

	require_once('include/security.php');

	if(! perm_is_allowed($uid,get_observer_hash(),'view_stream'))
		return array();

	$sql_options = item_permissions_sql($uid);
	$count = intval($count);

	if($flags)
		$sql_options .= " and ((item_flags & " . intval($flags) . ") = " . intval($flags) . ") ";

	if($authors) {
		if(! is_array($authors))
			$authors = array($authors);

		stringify_array_elms($authors,true);
		$sql_options .= " and author_xchan in (" . implode(',',$authors) . ") "; 
	}

	// Fetch tags
	$r = q("select term, count(term) as total from term left join item on term.oid = item.id
		where term.uid = %d and term.type = %d 
		and otype = %d and item_restrict = %d
		$sql_options
		group by term order by total desc %s",
		intval($uid),
		intval($type),
		intval(TERM_OBJ_POST),
		intval($restrict),
		((intval($count)) ? "limit $count" : '')
	);

	if(! $r)
		return array();

	// Find minimum and maximum log-count.
	$tags = array();
	$min = 1e9;
	$max = -1e9;

	$x = 0;
	foreach($r as $rr) {
		$tags[$x][0] = $rr['term'];
		$tags[$x][1] = log($rr['total']);
		$tags[$x][2] = 0;
		$min = min($min,$tags[$x][1]);
		$max = max($max,$tags[$x][1]);
		$x ++;
	}

	usort($tags,'tags_sort');

	$range = max(.01, $max - $min) * 1.0001;

	for($x = 0; $x < count($tags); $x ++) {
		$tags[$x][2] = 1 + floor(9 * ($tags[$x][1] - $min) / $range);
	}

	return $tags;
}


function tags_sort($a,$b) {
	if(strtolower($a[0]) == strtolower($b[0]))
		return 0;

	return((strtolower($a[0]) < strtolower($b[0])) ? -1 : 1);
}


function dir_tagadelic($count = 0) {

	$count = intval($count);

	// Fetch tags
	$r = q("select xtag_term, count(xtag_term) as total from xtag
		group by xtag_term order by total desc %s",
		((intval($count)) ? "limit $count" : '')
	);

	if(! $r)
		return array();

	// Find minimum and maximum log-count.
	$tags = array();
	$min = 1e9;
	$max = -1e9;

	$x = 0;
	foreach($r as $rr) {
		$tags[$x][0] = $rr['xtag_term'];
		$tags[$x][1] = log($rr['total']);
		$tags[$x][2] = 0;
		$min = min($min,$tags[$x][1]);
		$max = max($max,$tags[$x][1]);
		$x ++;
	}

	usort($tags,'tags_sort');

	$range = max(.01, $max - $min) * 1.0001;

	for($x = 0; $x < count($tags); $x ++) {
		$tags[$x][2] = 1 + floor(9 * ($tags[$x][1] - $min) / $range);
	}

	return $tags;
}


function tagblock($link,$uid,$count = 0,$authors = '',$flags = 0,$restrict = 0,$type = TERM_HASHTAG) {
	$o = '';

	$r = tagadelic($uid,$count,$authors,$flags,$restrict,$type);

	if($r) {
		$o = '<div class="tagblock widget"><h3>' . t('Tags') . '</h3><div class="tags" align="center">';
		foreach($r as $rr) { 
		  $o .= '<span class="tag'.$rr[2].'">#</span><a href="'.$link .'/' . '?f=&tag=' . urlencode($rr[0]).'" class="tag'.$rr[2].'">'.$rr[0].'</a> ' . "\r\n";
		}
		$o .= '</div></div>';
	}

	return $o;
}


function wtagblock($uid,$count = 0,$authors = '',$flags = 0,$restrict = 0,$type = TERM_HASHTAG) {
	$o = '';

	$r = tagadelic($uid,$count,$authors,$flags,$restrict,$type);

	if($r) {
		$c = q("select channel_address from channel where channel_id = %d limit 1",
			intval($uid)
		);
	
		$o = '<div class="tagblock widget"><h3>' . t('Tags') . '</h3><div class="tags" align="center">';
		foreach($r as $rr) { 
		  $o .= '<span class="tag' . $rr[2] . '">#</span><a href="channel/' . $c[0]['channel_address'] . '?f=&tag=' . urlencode($rr[0]).'" class="tag'.$rr[2].'">'.$rr[0].'</a> ' . "\r\n";
		}
		$o .= '</div></div>';
	}

	return $o;
}


function catblock($uid,$count = 0,$authors = '',$flags = 0,$restrict = 0,$type = TERM_CATEGORY) {
	$o = '';

	$r = tagadelic($uid,$count,$authors,$flags,$restrict,$type);

	if($r) {
		$c = q("select channel_address from channel where channel_id = %d limit 1",
			intval($uid)
		);
	
		$o = '<div class="tagblock widget"><h3>' . t('Categories') . '</h3><div class="tags" align="center">';
		foreach($r as $rr) { 
			$o .= '<a href="channel/' . $c[0]['channel_address']. '?f=&cat=' . urlencode($rr[0]).'" class="tag'.$rr[2].'">'.$rr[0].'</a> ' . "\r\n";
		}
		$o .= '</div></div>';
	}

	return $o;
}


function dir_tagblock($link,$r) {
	$o = '';

	if(! $r)
		$r = get_app()->data['directory_keywords'];

	if($r) {
		$o = '<div class="dirtagblock widget"><h3>' . t('Keywords') . '</h3><div class="tags" align="center">';
		foreach($r as $rr) { 
			$o .= '<a href="'.$link .'/' . '?f=&keywords=' . urlencode($rr['term']).'" class="tag'.$rr['normalise'].'" rel="nofollow" >'.$rr['term'].'</a> ' . "\r\n";
		}
		$o .= '</div></div>';
	}

	return $o;
}



	/** 
	 * verbs: [0] = first person singular, e.g. "I want", [1] = 3rd person singular, e.g. "Bill wants" 
	 * We use the first person form when creating an activity, but the third person for use in activities
	 * FIXME: There is no accounting for verb gender for languages where this is significant. We may eventually
	 * require obj_verbs() to provide full conjugations and specify which form to use in the $_REQUEST params to this module.
	 */

function obj_verbs() {
	$verbs = array(
		'has' => array( t('have'), t('has')),
		'wants' => array( t('want'), t('wants')),
		'likes' => array( t('like'), t('likes')),
		'dislikes' => array( t('dislike'), t('dislikes')),
	);

	$arr = array('verbs' => $verbs);
	call_hooks('obj_verbs', $arr);

	return	$arr['verbs'];
}


function obj_verb_selector($current = '') {
	$verbs = obj_verbs();
	$o = '<select class="obj-verb-selector" name="verb">';
	foreach($verbs as $k => $v) {
		$selected = (($k == $current) ? ' selected="selected" ' : '');
		$o .= '<option value="' . urlencode($k) . '"' . $selected . '>' . $v[1] . '</option>';
	}
	$o .= '</select>';

	return $o;
}

function get_things($profile_hash,$uid) {

	$sql_extra = (($profile_hash) ? " and obj_page = '" . $profile_hash . "' " : '');

	$r = q("select * from obj left join term on obj_obj = term_hash where term_hash != '' and uid = %d and obj_type = %d $sql_extra order by obj_verb, term",
		intval($uid),
		intval(TERM_OBJ_THING)
	);

	$things = $sorted_things = null;

	$profile_hashes = array();

	if($r) {

		// if no profile_hash was specified (display on profile page mode), match each of the things to a profile name 
		// (list all my things mode). This is harder than it sounds.

		foreach($r as $rr) {
			$rr['profile_name'] = '';
			if(! in_array($rr['term_hash'],$profile_hashes))
				$profile_hashes[] = $rr['term_hash'];
		}
		stringify_array_elms($profile_hashes);
		if(! $profile_hash) {
			$exp = explode(',',$profile_hashes);
			$p = q("select profile_guid as hash, profile_name as name from profile where profile_guid in ( $exp ) ");
			if($p) {
				foreach($r as $rr) {
					foreach($p as $pp) { 
						if($rr['obj_page'] == $pp['hash']) {
							$rr['profile_name'] == $pp['name'];
						}
					}
				}
			}
 		}

		$things = array();

		// Use the system obj_verbs array as a sort key, since we don't really
		// want an alphabetic sort. To change the order, use a plugin to
		// alter the obj_verbs() array or alter it in code. Unknown verbs come
		// after the known ones - in no particular order. 

		$v = obj_verbs();
		foreach($v as $k => $foo)
			$things[$k] = null;
		foreach($r as $rr) {

			$l = q("select xchan_name, xchan_url from likes left join xchan on likee = xchan_hash where
				target_type = '%s' and target_id = '%s' and channel_id = %d",
				dbesc(ACTIVITY_OBJ_THING),
				dbesc($rr['term_hash']),
				intval($uid)
			);

			for($x = 0; $x < count($l); $x ++) 
				$l[$x]['xchan_url'] = zid($l[$x]['xchan_url']);

			if(! $things[$rr['obj_verb']])
				$things[$rr['obj_verb']] = array();

			$things[$rr['obj_verb']][] = array('term' => $rr['term'],'url' => $rr['url'],'img' => $rr['imgurl'], 'profile' => $rr['profile_name'],'term_hash' => $rr['term_hash'], 'likes' => $l,'like_count' => count($l),'like_label' => tt('Like','Likes',count($l),'noun'));
		} 
		$sorted_things = array();
		if($things) {
			foreach($things as $k => $v) {
				if(is_array($things[$k])) {
					$sorted_things[$k] = $v;
				}
			}
		}
	}
//logger('things: ' . print_r($sorted_things,true));

	return $sorted_things;
}