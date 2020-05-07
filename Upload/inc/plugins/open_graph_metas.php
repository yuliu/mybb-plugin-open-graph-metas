<?php
/**
 * MyBB 1.8 plugin: Open Graph Metas
 * Website: https://github.com/yuliu/mybb-plugin-open-graph-metas
 * License: https://github.com/yuliu/mybb-plugin-open-graph-metas/blob/master/LICENSE
 * Copyright Yu 'noyle' Liu, All Rights Reserved
 *
 * The Open Graph protocol: https://ogp.me/
 */

// Make sure we can't access this file directly from the browser.
if(!defined('IN_MYBB'))
{
	die('This file cannot be accessed directly.');
}

// Cut-off length for long description text.
define('OPEN_GRAPH_METAS_DESC_MAX_LENGTH', 250);

// Default logo, if not blank, will be used as fallback for og:image.
define('OPEN_GRAPH_METAS_DEFAULT_LOGO', '');

// Default description, if not blank, will be used as fallback for og:description.
define('OPEN_GRAPH_METAS_DEFAULT_DESC', '');

// Facebook AppID.
define('OPEN_GRAPH_METAS_FB_APPID', '');

// Use attachment (image type) as og:image for threads: 0 - none and fallback will be used, 1 - full attachment if possible, fallback otherwise, 2 - thumbnail if possible, fallback otherwise.
define('OPEN_GRAPH_METAS_THREAD_IMAGE_USE_ATTACHMENT', 2);

// Use attachment (image type) as og:image for posts: 0 - none and fallback will be used, 1 - full attachment if possible, fallback otherwise, 2 - thumbnail if possible, fallback otherwise.
define('OPEN_GRAPH_METAS_POST_IMAGE_USE_ATTACHMENT', 2);

// Fallback image for og:image for threads: 0 - fallback to default logo, 1 - poster's avatar
define('OPEN_GRAPH_METAS_THREAD_IMAGE_FALLBACK', 1);

// Fallback image for og:image for posts: 0 - fallback to default logo, 1 - poster's avatar
define('OPEN_GRAPH_METAS_POST_IMAGE_FALLBACK', 1);

// Extra available image types, comma separated file extensions. jpg,jpeg,gif,bmp,png are already included.
define('OPEN_GRAPH_METAS_EXTRA_IMG_TYPES', '');

// Maximum image dimensions.
define('OPEN_GRAPH_METAS_IMG_MAX_DIMS', '500|500');

// Minimum image dimensions.
define('OPEN_GRAPH_METAS_IMG_MIN_DIMS', '200|200');

/**
 * Internal constant defines, modifying them is not needed.
 */

function open_graph_metas_info()
{
	return array(
		'name'          => 'Open Graph Metas',
		'description'   => 'A very primitive MyBB plugin for showing Open Graph metas for various forum modules.',
		'website'       => 'https://github.com/yuliu/mybb-plugin-open-graph-metas',
		'author'        => 'Yu \'noyle\' Liu',
		'authorsite'    => 'https://github.com/yuliu/mybb-plugin-open-graph-metas',
		'version'       => '0.2',
		'compatibility' => '18*',
		'codename'      => 'noyle_open_graph_metas',
	);
}

/**
 * Helper function for formatting and cutting long text for Open Graph description.
 *
 * @param string $in Description text to be formatted.
 *
 * @return string Text formatted.
 */
function open_graph_metas_helper_func_format_description($in)
{
	$out = htmlspecialchars_uni($in);
	if(defined('OPEN_GRAPH_METAS_DESC_MAX_LENGTH') && my_strlen($out) > OPEN_GRAPH_METAS_DESC_MAX_LENGTH)
	{
		$out = my_substr($out, 0, OPEN_GRAPH_METAS_DESC_MAX_LENGTH)."...";
	}
	return $out;
}

/**
 * Helper function for generating site-wide consistent URL.
 *
 * @param string $in Module's partial URL without board URL.
 *
 * @return string Full linkable URL.
 */
function open_graph_metas_helper_func_get_full_url($in)
{
	global $mybb;
	return $mybb->settings['bburl'].'/'.$in;
}

/**
 * Helper function for getting default description, if set, Board name will be used otherwise.
 *
 * @return string Default description.
 */
function open_graph_metas_helper_func_get_default_desc()
{
	if(defined('OPEN_GRAPH_METAS_DEFAULT_DESC') && !empty(OPEN_GRAPH_METAS_DEFAULT_DESC))
	{
		return OPEN_GRAPH_METAS_DEFAULT_DESC;
	}
	global $mybb;
	return $mybb->settings['bbname'];
}

/**
 * Helper function for generating forum logo for Open Graph image.
 * If no default logo is set for this plugin, the default logo in theme settings will be used.
 *
 * @return mixed|string The full image URL.
 */
function open_graph_metas_helper_func_get_default_logo()
{
	if(defined('OPEN_GRAPH_METAS_DEFAULT_LOGO') && !empty(OPEN_GRAPH_METAS_DEFAULT_LOGO))
	{
		return OPEN_GRAPH_METAS_DEFAULT_LOGO;
	}
	global $theme;
	return $theme['logo'];
}

/**
 * Helper function for printing Open Graph metas.
 *
 * @param string $title og:title
 * @param string $url og:url
 * @param string $desc og:description
 * @param string $image og:image
 * @param string $type og:type. If none given, type "website" will be used.
 * @param array $extra Extra Open Graph properties. The element key for property should include "og:", the value is for content.
 *
 * @return string Final output.
 */
function open_graph_metas_helper_func_output_og_metas($title = '', $url = '', $description = '', $image = '', $type = '', $extra = array())
{
	global $mybb;
	$output = "\n<meta property=\"og:site_name\" content=\"{$mybb->settings['bbname']}\" />";
	if(defined('OPEN_GRAPH_METAS_FB_APPID') && !empty(OPEN_GRAPH_METAS_FB_APPID))
	{
		$output .= "\n<meta property=\"fb:app_id\" content=\"".OPEN_GRAPH_METAS_FB_APPID."\" />";
	}
	if(!empty($title))
	{
		$output .= "\n<meta property=\"og:title\" content=\"{$title}\" />";
	}
	if(!empty($url))
	{
		$output .= "\n<meta property=\"og:url\" content=\"{$url}\" />";
	}
	if(!empty($description))
	{
		$output .= "\n<meta property=\"og:description\" content=\"{$description}\" />";
	}
	if(!empty($image))
	{
		$output .= "\n<meta property=\"og:image\" content=\"{$image}\" />";
	}
	if(!empty($type))
	{
		$output .= "\n<meta property=\"og:type\" content=\"{$type}\" />";
	}
	else
	{
		$output .= "\n<meta property=\"og:type\" content=\"website\" />";
	}
	if(!is_array($extra))
	{
		$extra = array($extra);
	}
	if(!empty($extra))
	{
		foreach($extra as $key => $value)
		{
			$output .= "\n<meta property=\"{$key}\" content=\"{$value}\" />";
		}
	}

	if($mybb->settings['tplhtmlcomments'] == 1)
	{
		$output = "\n<!-- start: plugin_open_graph_metas_og -->{$output}\n<!-- end: plugin_open_graph_metas_og -->";
	}
	return $output;
}

/**
 * Helper function for generating attachment link. No permission are checked in this function.
 *
 * @param int $aid Attachment ID.
 * @param bool $use_thumbnail If true, return the attachment's thumbnail link.
 *
 * @return bool|string The full link to the attachment if correct attachment ID is given, false otherwise.
 */
function open_graph_metas_helper_func_get_attachment_link($aid, $use_thumbnail = false)
{
	global $mybb;
	if(empty($aid) || (int)$aid < 0)
	{
		return false;
	}
	if($use_thumbnail)
	{
		return $mybb->settings['bburl'].'/attachment.php?thumbnail='.(int)$aid;
	}
	else
	{
		return $mybb->settings['bburl'].'/attachment.php?aid='.(int)$aid;
	}
}

/**
 * Helper function for getting the first usable attachment/thumbnail from posts with given post IDs.
 *
 * @param int   $fid Forum ID.
 * @param array $pids IDs of posts for query.
 * @param bool  $use_thumbnail Whether to use thumbnail.
 *
 * @return bool|int The ID of the attachment/thumbnail found if any, otherwise false.
 */
function open_graph_metas_helper_func_get_first_usable_attachment_id($fid, $pids = array(), $use_thumbnail = false)
{
	global $mybb, $db, $attachcache, $cache;
	$fid = (int)$fid;
	if(empty($fid) || empty($pids) || $mybb->settings['enableattachments'] != 1)
	{
		return false;
	}

	// Attachment viewing permission checking.
	$forum = get_forum($fid);
	$forumpermissions = forum_permissions($fid);
	if($forumpermissions['canview'] == 0 || $forumpermissions['canviewthreads'] == 0 || ($forumpermissions['candlattachments'] == 0 && !$use_thumbnail))
	{
		return false;
	}

	// Working on posts' pids for fetching attachments from.
	if(!is_array($pids))
	{
		$pids = array($pids);
	}
	$attachments = array();
	if(isset($attachcache))
	{
		$attachments = $attachcache;
	}

	$pids_query = array();
	foreach($pids as $pid)
	{
		if(!array_key_exists($pid, $attachments))
		{
			$pids_query[] = $pid;
		}
	}
	if(!empty($pids_query))
	{
		$query = $db->simple_select("attachments", "*", "pid IN (".implode(',', $pids_query).")");
		while($att = $db->fetch_array($query))
		{
			$attachments[$att['pid']][$att['aid']] = $att;
		}
		unset($pids_query);
	}

	// Setup image types.
	$image_types = 'jpg,jpeg,gif,bmp,png';
	if(defined('OPEN_GRAPH_METAS_IMG_TYPES') && !empty(OPEN_GRAPH_METAS_IMG_TYPES))
	{
		$image_types .= ','.OPEN_GRAPH_METAS_IMG_TYPES;
	}
	$image_types = array_unique(array_filter(array_map('trim', explode(',', $image_types))));

	// Loop all attachments.
	$ret_attachment_id = false;
	foreach($attachments as $pid =>$attachments_by_pid)
	{
		foreach($attachments_by_pid as $aid =>$attachment)
		{
			// Skip non-visible attachments and attachments without thumbnails if $use_thumbnail is set.
			if($attachment['visible'] != 1 || ($use_thumbnail && ($attachment['thumbnail'] == 'SMALL') || empty($attachment['thumbnail'])))
			{
				continue;
			}

			// Skip non images.
			$ext = get_extension($attachment['filename']);
			if(!in_array($ext, $image_types))
			{
				continue;
			}

			$ret_attachment_id = $attachment['aid'];
			break;
		}
	}

	return $ret_attachment_id;
}

$plugins->add_hook('global_end', 'open_graph_metas_show_og_info_general');

$plugins->add_hook('forumdisplay_end', 'open_graph_metas_show_forum_forumdisplay');
$plugins->add_hook('showthread_threaded', 'open_graph_metas_show_thread_showthread_threaded');
$plugins->add_hook('showthread_linear', 'open_graph_metas_show_thread_showthread_linear');
$plugins->add_hook('member_profile_end', 'open_graph_metas_show_profile_member_profile');

/**
 * Open Graph metas for common pages.
 */
function open_graph_metas_show_og_info_general()
{
	global $mybb, $headerinclude;

	if(!defined('THIS_SCRIPT') || (THIS_SCRIPT != 'forumdisplay.php' && THIS_SCRIPT != 'showthread.php' && THIS_SCRIPT != 'member.php'))
	{
		// og:url
		//$url = open_graph_metas_helper_func_get_url('');

		// og:title
		$title = '';
		if(defined('THIS_SCRIPT'))
		{
			// Update og:url.
			$url = open_graph_metas_helper_func_get_full_url(THIS_SCRIPT);

			// Update og:title.
			global $lang;
			if(THIS_SCRIPT == 'index.php')
			{
				// $title = $lang-> . ' - ';
			}
			if(THIS_SCRIPT == 'misc.php' && $mybb->input['action'] == 'help')
			{
				$title = $lang->toplinks_help . ' - ';
			}
			if(THIS_SCRIPT == 'search.php')
			{
				$title = $lang->toplinks_search . ' - ';
			}
			if(THIS_SCRIPT == 'memberlist.php')
			{
				$title = $lang->toplinks_memberlist . ' - ';
			}
			if(THIS_SCRIPT == 'portal.php')
			{
				$title = $lang->toplinks_portal . ' - ';
			}
			if(THIS_SCRIPT == 'calendar.php')
			{
				$title = $lang->toplinks_calendar . ' - ';
			}
		}
		$title .= $mybb->settings['bbname'];

		// og:description
		$desc = open_graph_metas_helper_func_get_default_desc();
		$desc = open_graph_metas_helper_func_format_description($desc);

		// og:image
		$image = open_graph_metas_helper_func_get_default_logo();

		$headerinclude .= open_graph_metas_helper_func_output_og_metas($title, $url, $desc, $image);
	}
}

/**
 * Open Graph metas for forums.
 */
function open_graph_metas_show_forum_forumdisplay()
{
	global $mybb, $foruminfo;
	global $fid, $page;

	// og:title
	$title = $foruminfo['name'] . ' - '. $mybb->settings['bbname'];

	// og:description
	if(!empty($foruminfo['description']))
	{
		$desc = $foruminfo['description'];
	}
	else
	{
		$desc = open_graph_metas_helper_func_get_default_desc();
	}
	$desc = open_graph_metas_helper_func_format_description($desc);

	// og:url
	if($page > 1)
	{
		$url = str_replace(array("{fid}", "{page}"), array($fid, $page), FORUM_URL_PAGED);
	}
	else
	{
		$url = str_replace("{fid}", $fid, FORUM_URL);
	}
	$url = open_graph_metas_helper_func_get_full_url($url);

	// og:image
	$image = open_graph_metas_helper_func_get_default_logo();

	global $headerinclude;
	$headerinclude .= open_graph_metas_helper_func_output_og_metas($title, $url, $desc, $image);
}

/**
 * Open Graph metas for threads in threaded view mode.
 */
function open_graph_metas_show_thread_showthread_threaded()
{
	global $mybb, $forum, $thread;
	global $parser, $showpost;
	global $tid, $pid, $page, $highlight;

	// og:title
	$title = $thread['subject'] . ' - '. $mybb->settings['bbname'];

	// og:description
	$parser_options = array(
		'allow_html' => $forum['allowhtml'],
		'allow_mycode' => $forum['allowmycode'],
		'allow_smilies' => $forum['allowsmilies'],
		'allow_imgcode' => $forum['allowimgcode'],
		'allow_videocode' => $forum['allowvideocode'],
		'filter_badwords' => 1,
		);
	$desc = open_graph_metas_helper_func_format_description($parser->parse_message($showpost['message'], $parser_options));

	// og:url
	if(!empty($mybb->input['pid']))
	{
		$url = str_replace(array("{tid}", "{pid}"), array($tid, $pid), THREAD_URL_POST);
	}
	else if($page > 1)
	{
		$url = str_replace(array("{tid}", "{page}"), array($tid, $page), THREAD_URL_PAGED);
	}
	else
	{
		$url = str_replace("{tid}", $tid, THREAD_URL);
	}
	$threadmode = "";
	if($mybb->seo_support == true)
	{
		if($mybb->get_input('highlight'))
		{
			$threadmode = "&amp;mode=threaded";
		}
		else
		{
			$threadmode = "?mode=threaded";
		}
	}
	else
	{
		$threadmode = "&amp;mode=threaded";
	}
	$url = open_graph_metas_helper_func_get_full_url($url.$highlight.$threadmode);
	if(!empty($mybb->input['pid']))
	{
		$url .= "#pid{$pid}";
	}

	// og:image
	$image = '';
	if(defined('OPEN_GRAPH_METAS_POST_IMAGE_USE_ATTACHMENT') && OPEN_GRAPH_METAS_POST_IMAGE_USE_ATTACHMENT == 1)
	{
		// Use full image.
		$aid = open_graph_metas_helper_func_get_first_usable_attachment_id($forum['fid'], $pid);
		$image = open_graph_metas_helper_func_get_attachment_link($aid);
	}
	else if(defined('OPEN_GRAPH_METAS_POST_IMAGE_USE_ATTACHMENT') && OPEN_GRAPH_METAS_POST_IMAGE_USE_ATTACHMENT == 2)
	{
		// Use thumbnail.
		$aid = open_graph_metas_helper_func_get_first_usable_attachment_id($forum['fid'], $pid, true);
		$image = open_graph_metas_helper_func_get_attachment_link($aid, true);
	}
	// Fallback to use avatar or default logo.
	if(empty($image))
	{
		if((!defined('OPEN_GRAPH_METAS_POST_IMAGE_FALLBACK') || OPEN_GRAPH_METAS_POST_IMAGE_FALLBACK == 1) && $showpost['userusername'])
		{
			$useravatar = format_avatar($showpost['avatar'], '', OPEN_GRAPH_METAS_IMG_MAX_DIMS);
			$image = $useravatar['image'];
		}
		else
		{
			$image = open_graph_metas_helper_func_get_default_logo();
		}
	}

	global $headerinclude;
	$headerinclude .= open_graph_metas_helper_func_output_og_metas($title, $url, $desc, $image);
}

/**
 * Open Graph metas for threads in linear view mode.
 */
function open_graph_metas_show_thread_showthread_linear()
{
	global $db, $query;
	global $mybb, $forum, $thread;
	global $parser;
	global $tid, $pid, $page, $highlight, $threadmode;

	// database manipulation for fetching the first post in current page, and get all pids.
	$db->data_seek($query, 0);
	$showpost = $db->fetch_array($query);
	$pids = array($showpost['pid']);
	while($post = $db->fetch_array($query))
	{
		$pids[] = $post['pid'];
	}
	$db->data_seek($query, $db->num_rows($query));
	unset($post);

	// og:title
	$title = $thread['subject'] . ' - '. $mybb->settings['bbname'];

	// og:description
	$parser_options = array(
		'allow_html' => $forum['allowhtml'],
		'allow_mycode' => $forum['allowmycode'],
		'allow_smilies' => $forum['allowsmilies'],
		'allow_imgcode' => $forum['allowimgcode'],
		'allow_videocode' => $forum['allowvideocode'],
		'filter_badwords' => 1,
	);
	$desc = open_graph_metas_helper_func_format_description($parser->parse_message($showpost['message'], $parser_options));

	// og:url
	if(!empty($mybb->input['pid']))
	{
		$url = str_replace(array("{tid}", "{pid}"), array($tid, $pid), THREAD_URL_POST);
	}
	else if($page > 1)
	{
		$url = str_replace(array("{tid}", "{page}"), array($tid, $page), THREAD_URL_PAGED);
	}
	else
	{
		$url = str_replace("{tid}", $tid, THREAD_URL);
	}
	$url = open_graph_metas_helper_func_get_full_url($url.$highlight.$threadmode);
	if(!empty($mybb->input['pid']))
	{
		$url .= "#pid{$pid}";
	}

	// og:image
	$image = '';
	if(defined('OPEN_GRAPH_METAS_POST_IMAGE_USE_ATTACHMENT') && OPEN_GRAPH_METAS_THREAD_IMAGE_USE_ATTACHMENT == 1)
	{
		// Use full image.
		$aid = open_graph_metas_helper_func_get_first_usable_attachment_id($forum['fid'], $pids);
		$image = open_graph_metas_helper_func_get_attachment_link($aid);
	}
	else if(defined('OPEN_GRAPH_METAS_POST_IMAGE_USE_ATTACHMENT') && OPEN_GRAPH_METAS_THREAD_IMAGE_USE_ATTACHMENT == 2)
	{
		// Use thumbnail.
		$aid = open_graph_metas_helper_func_get_first_usable_attachment_id($forum['fid'], $pids, true);
		$image = open_graph_metas_helper_func_get_attachment_link($aid, true);
	}
	// Fallback to use avatar or default logo.
	if(empty($image))
	{
		if((!defined('OPEN_GRAPH_METAS_POST_IMAGE_FALLBACK') || OPEN_GRAPH_METAS_POST_IMAGE_FALLBACK == 1) && $showpost['userusername'])
		{
			$useravatar = format_avatar($showpost['avatar'], '', OPEN_GRAPH_METAS_IMG_MAX_DIMS);
			$image = $useravatar['image'];
		}
		else
		{
			$image = open_graph_metas_helper_func_get_default_logo();
		}
	}

	global $headerinclude;
	$headerinclude .= open_graph_metas_helper_func_output_og_metas($title, $url, $desc, $image);
}

/**
 * Open Graph metas for member profile pages.
 */
function open_graph_metas_show_profile_member_profile()
{
	global $mybb, $lang, $memprofile;
	global $uid, $useravatar;

	// og:title
	$title = $lang->profile . ' - '. $mybb->settings['bbname'];

	// og:description
	$desc = $memprofile['username'];
	if(!empty($memprofile['signature']))
	{
		$desc .= "\n".$memprofile['signature'];
	}
	$desc = open_graph_metas_helper_func_format_description($desc);

	// og:url
	$url = open_graph_metas_helper_func_get_full_url(str_replace("{uid}", $uid, PROFILE_URL));

	// og:image
	$useravatar = format_avatar($memprofile['avatar'], '', OPEN_GRAPH_METAS_IMG_MAX_DIMS);
	$image = $useravatar['image'];

	global $headerinclude;
	$headerinclude .= open_graph_metas_helper_func_output_og_metas($title, $url, $desc, $image);
}

