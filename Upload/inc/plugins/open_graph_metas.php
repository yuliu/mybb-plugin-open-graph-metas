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

// Custom logo.
define('OPEN_GRAPH_METAS_DEFAULT_LOGO', '');

// Custom Description.
define('OPEN_GRAPH_METAS_DEFAULT_DESC', '"adfsdfdsaf",\'fdsafsafsd\'');

// Facebook AppID
define('OPEN_GRAPH_METAS_FB_APPID', '');

// Image Maximum Dimensions
define('OPEN_GRAPH_METAS_IMG_MAX_DIMS', '500|500');

function open_graph_metas_info()
{
	return array(
		'name'			=> 'Open Graph Metas',
		'description'	=> 'A very primitive MyBB plugin for showing Open Graph metas in forum/thread/etc. modules.',
		'website'		=> 'https://github.com/yuliu/mybb-plugin-open-graph-metas',
		'author'		=> 'Yu \'noyle\' Liu',
		'authorsite'	=> 'https://github.com/yuliu/mybb-plugin-open-graph-metas',
		'version'		=> '0.1',
		'compatibility'	=> '18*',
		'codename'		=> 'noyle_open_graph_metas',
	);
}

/**
 * Helper function for formatting and cutting long text for Open Graph Description.
 *
 * @param string $in Description text to be formatted.
 *
 * @return string Text formatted.
 */
function open_graph_metas_helper_func_get_description($in)
{
	$out = htmlspecialchars_uni($in);
	if(my_strlen($out) > OPEN_GRAPH_METAS_DESC_MAX_LENGTH)
	{
		$out = my_substr($out, 0, OPEN_GRAPH_METAS_DESC_MAX_LENGTH)."...";
	}
	return $out;
}

/**
 * Helper function for generating site-wide consistent URL.
 *
 * @param string $in Module's partial URL.
 *
 * @return string Full linkable URL.
 */
function open_graph_metas_helper_func_get_url($in)
{
	global $mybb;
	return $mybb->settings['bburl'].'/'.$in;
}

/**
 * Helper function for generating forum logo for Open Graph Image.
 *
 * @return mixed|string The full image URL.
 */
function open_graph_metas_helper_func_get_logo()
{
	if(defined('OPEN_GRAPH_METAS_DEFAULT_LOGO') && !empty(OPEN_GRAPH_METAS_DEFAULT_LOGO))
	{
		return OPEN_GRAPH_METAS_DEFAULT_LOGO;
	}
	global $theme;
	return $theme['logo'];
}

$plugins->add_hook('global_end', 'open_graph_metas_show_og_info');

$plugins->add_hook('forumdisplay_end', 'open_graph_metas_show_forum_forumdisplay');
$plugins->add_hook('showthread_threaded', 'open_graph_metas_show_thread_showthread_threaded');
$plugins->add_hook('showthread_linear', 'open_graph_metas_show_thread_showthread_linear');
$plugins->add_hook('member_profile_end', 'open_graph_metas_show_profile_member_profile');

function open_graph_metas_show_og_info()
{
	global $mybb, $headerinclude;
	if(defined('OPEN_GRAPH_METAS_FB_APPID') && !empty(OPEN_GRAPH_METAS_FB_APPID))
	{
		$headerinclude .= "\n<meta property=\"fb:app_id\" content=\"".OPEN_GRAPH_METAS_FB_APPID."\" />";
	}
	$headerinclude .= "\n<meta property=\"og:site_name\" content=\"{$mybb->settings['bbname']}\" />";
	$headerinclude .= "\n<meta property=\"og:type\" content=\"website\" />";

	if(!defined('THIS_SCRIPT') || THIS_SCRIPT != 'forumdisplay.php' || THIS_SCRIPT != 'showthread.php' || THIS_SCRIPT != 'member.php')
	{
		if(!defined('OPEN_GRAPH_METAS_FB_APPID') || empty(OPEN_GRAPH_METAS_DEFAULT_DESC))
		{
			$desc = $mybb->settings['bbname'];
		}
		else
		{
			$desc = OPEN_GRAPH_METAS_DEFAULT_DESC;
		}
		$desc = open_graph_metas_helper_func_get_description($desc);
		$url = open_graph_metas_helper_func_get_url('');
		$image = open_graph_metas_helper_func_get_logo();
		$headerinclude .= "\n<meta property=\"og:url\" content=\"{$url}\" />";
		$headerinclude .= "\n<meta property=\"og:description\" content=\"{$desc}\" />";
		$headerinclude .= "\n<meta property=\"og:image\" content=\"{$image}\" />";
	}
}

function open_graph_metas_show_forum_forumdisplay()
{
	global $headerinclude, $mybb, $foruminfo;
	if(!empty($foruminfo['description']))
	{
		$desc = open_graph_metas_helper_func_get_description($foruminfo['description']);
	}
	else
	{
		$desc = $foruminfo['name'];
	}

	global $fid, $page;
	if($page > 1)
	{
		$url = str_replace(array("{fid}", "{page}"), array($fid, $page), FORUM_URL_PAGED);
	}
	else
	{
		$url = str_replace("{fid}", $fid, FORUM_URL);
	}
	$url = open_graph_metas_helper_func_get_url($url);

	$image = open_graph_metas_helper_func_get_logo();

	$headerinclude .= "\n<meta property=\"og:title\" content=\"{$foruminfo['name']} - {$mybb->settings['bbname']}\" />";
	$headerinclude .= "\n<meta property=\"og:url\" content=\"{$url}\" />";
	$headerinclude .= "\n<meta property=\"og:description\" content=\"{$desc}\" />";
	$headerinclude .= "\n<meta property=\"og:image\" content=\"{$image}\" />";
}

function open_graph_metas_show_thread_showthread_threaded()
{
	global $mybb, $forum, $thread;
	$parser_options = array(
		'allow_html' => $forum['allowhtml'],
		'allow_mycode' => $forum['allowmycode'],
		'allow_smilies' => $forum['allowsmilies'],
		'allow_imgcode' => $forum['allowimgcode'],
		'allow_videocode' => $forum['allowvideocode'],
		'filter_badwords' => 1,
		);

	global $parser, $showpost;
	$desc = open_graph_metas_helper_func_get_description($parser->parse_message($showpost['message'], $parser_options));

	global $tid, $pid, $page, $highlight;
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

	$url = open_graph_metas_helper_func_get_url($url.$highlight.$threadmode);
	if(!empty($mybb->input['pid']))
	{
		$url .= "#pid{$pid}";
	}

	if($showpost['userusername'])
	{
		$useravatar = format_avatar($showpost['avatar'], '', OPEN_GRAPH_METAS_IMG_MAX_DIMS);
		$image = $useravatar['image'];
	}
	else
	{
		$image = open_graph_metas_helper_func_get_logo();
	}

	global $headerinclude;
	$headerinclude .= "\n<meta property=\"og:title\" content=\"{$thread['subject']} - {$mybb->settings['bbname']}\" />";
	$headerinclude .= "\n<meta property=\"og:url\" content=\"{$url}\" />";
	$headerinclude .= "\n<meta property=\"og:description\" content=\"{$desc}\" />";
	$headerinclude .= "\n<meta property=\"og:image\" content=\"{$image}\" />";
}

function open_graph_metas_show_thread_showthread_linear()
{
	global $db, $query;
	$db->data_seek($query, 0);
	$showpost = $db->fetch_array($query);
	$db->data_seek($query, $db->num_rows($query));

	global $mybb, $forum, $thread;
	$parser_options = array(
		'allow_html' => $forum['allowhtml'],
		'allow_mycode' => $forum['allowmycode'],
		'allow_smilies' => $forum['allowsmilies'],
		'allow_imgcode' => $forum['allowimgcode'],
		'allow_videocode' => $forum['allowvideocode'],
		'filter_badwords' => 1,
	);

	global $parser;
	$desc = open_graph_metas_helper_func_get_description($parser->parse_message($showpost['message'], $parser_options));

	global $tid, $pid, $page, $highlight, $threadmode;
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
	$url = open_graph_metas_helper_func_get_url($url.$highlight.$threadmode);
	if(!empty($mybb->input['pid']))
	{
		$url .= "#pid{$pid}";
	}

	if($showpost['userusername'])
	{
		$useravatar = format_avatar($showpost['avatar'], '', OPEN_GRAPH_METAS_IMG_MAX_DIMS);
		$image = $useravatar['image'];
	}
	else
	{
		$image = open_graph_metas_helper_func_get_logo();
	}

	global $headerinclude;
	$headerinclude .= "\n<meta property=\"og:title\" content=\"{$thread['subject']} - {$mybb->settings['bbname']}\" />";
	$headerinclude .= "\n<meta property=\"og:url\" content=\"{$url}\" />";
	$headerinclude .= "\n<meta property=\"og:description\" content=\"{$desc}\" />";
	$headerinclude .= "\n<meta property=\"og:image\" content=\"{$image}\" />";
}

function open_graph_metas_show_profile_member_profile()
{
	global $mybb, $lang, $memprofile;
	$desc = $memprofile['username'];
	if(!empty($memprofile['signature']))
	{
		$desc .= "\n".$memprofile['signature'];
	}
	$desc = open_graph_metas_helper_func_get_description($desc);

	global $uid, $useravatar;
	$url = str_replace("{uid}", $uid, PROFILE_URL);
	$useravatar = format_avatar($memprofile['avatar'], '', OPEN_GRAPH_METAS_IMG_MAX_DIMS);
	$image = $useravatar['image'];

	global $headerinclude;
	$headerinclude .= "\n<meta property=\"og:title\" content=\"{$lang->profile} - {$mybb->settings['bbname']}\" />";
	$headerinclude .= "\n<meta property=\"og:url\" content=\"{$url}\" />";
	$headerinclude .= "\n<meta property=\"og:description\" content=\"{$desc}\" />";
	$headerinclude .= "\n<meta property=\"og:image\" content=\"{$image}\" />";
}

