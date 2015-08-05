<?php
/**
*
* This file is part of the phpBB Customisation Database package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

$config = array(
	/**
	* phpBB versions array
	*
	* @param array(
	*	(release branch) => array(
	*		'latest_revision' => (revision number)
	* 		'allow_uploads' => (allow submission of revisions for this version of phpBB?),
	*	),
	* ),
	*/
	'phpbb_versions' => array(
		'20'	=> array('latest_revision' => '23', 'name' => 'phpBB 2.0.x', 'allow_uploads' => false),
		'30'	=> array('latest_revision' => '14', 'name' => 'phpBB 3.0.x', 'allow_uploads' => true),
		'31'	=> array('latest_revision' => '5', 'name' => 'phpBB 3.1.x', 'allow_uploads' => true),
	),

	/**
	* Relative path to the phpBB installation.
	*
	* @param	string	$phpbb_root_path	Path relative from the titania root path.
	*/
	'phpbb_root_path' => '../../forums/',

	/**
	* Relative path from the server root (generate_board_url(true))
	*
	* @param	string	Path to the phpBB folder
	*/
	'phpbb_script_path' => 'forums/',

	/**
	* Relative path from the server root (generate_board_url(true))
	*
	* @param	string	Path to the titania folder
	*/
	'titania_script_path' => 'customise/',

	/**
	* Prefix of the sql tables.  Not the prefix for the phpBB tables, prefix for the Titania tables only.
	* This MUST NOT be the same as the phpBB prefix!
	*
	* @param	string	$titania_table_prefix	Table prefix
	*/
	'table_prefix' => 'cdb_',

	/**
	* Path to the style demo board you would like styles to be installed on upon validation
	* (there is a checkbox option for styles to be installed on the demo board when approving)
	*
	* @param bool|string false to not use a style demo board, path to the board root
	*/
	'demo_style_path' => array(
		'30'	=> false,
		'31'	=> false,
	),

	/**
	* Full URL to the demo style.  We will perform sprintf(demo_style_full, $style_id), so please write the url properly
	* Example (from phpbb.com) http://www.phpbb.com/styles/demo/3.0/?style_id=%s
	*
	* @param bool|string false to not use a style demo board
	*/
	'demo_style_url' =>	array(
		'30'	=> false,
		'31'	=> false,
	),

	'demo_style_hook' => array(
		'30'	=> false,
		'31'	=> false,
	),

	// When editing styles, do not allow non-team members to modify the demo URL
	'can_modify_style_demo_url' => true,

	/**
	* Team groups (members will get TITANIA_TEAMS_ACCESS)
	*/
	'team_groups' => array(5),

	// Display backtrace for TITANIA_TEAMS_ACCESS level
	'display_backtrace'	=> 2,

	/**
	* IDs of database forum
	*/
	'forum_mod_database'		=> array(
		'30'	=> 10,
	),
	'forum_style_database'		=> array(
		'30'	=> 8,
		'31'	=> 7,
	),
	'forum_extension_database'	=> array(
		'31'	=> 5,
	),

	/**
	* IDs of account used for topic/post release in database forum
	*/
	'forum_extension_robot' => 52,
	'forum_mod_robot' => 52,
	'forum_style_robot' => 51,

	// Remove unsubmitted revisions and attachments
	'cleanup_titania'	=> true,
);
