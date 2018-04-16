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

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

use phpbb\titania\ext;

$config = array(
	/**
	* Path to the style demo board you would like styles to be installed on upon validation
	* (there is a checkbox option for styles to be installed on the demo board when approving)
	*
	* @param bool|string false to not use a style demo board, path to the board root
	*/
	'demo_style_path' => array(
		'30'	=> false,
		'31'	=> false,
		'32'	=> false,
	),

	/**
	* Full URL to the demo style.  We will perform sprintf(demo_style_full, $style_id), so please write the url properly
	* Example (from phpbb.com) http://www.phpbb.com/styles/demo/3.0/?style_id=%s
	*
	* @param bool|string false to not use a style demo board
	*/
	'demo_style_url' => array(
		'30'	=> false,
		'31'	=> false,
		'32'	=> false,
	),
	
	'demo_style_hook' => array(
		'30'	=> false,
		'31'	=> false,
		'32'	=> false,
	),

	/**
	* Style Path (titania/style/ *path* /)
	*/
	'style' => 'default',

	/**
	* Team groups (members will get TITANIA_TEAMS_ACCESS)
	*/
	'team_groups' => array(5),

	/**
	* IDs of database forum
	*/
	'forum_mod_database'		=> array(
		'30'	=> 0,
	),
	'forum_style_database'		=> array(
		'30'	=> 0,
		'31'	=> 0,
		'32'	=> 0,
	),
	'forum_extension_database'	=> array(
		'31'	=> 0,
		'32'	=> 0,
	),

	/**
	* IDs of account used for topic/post release in database forum
	*/
	'forum_mod_robot'		=> 0,
	'forum_style_robot'		=> 0,
	'forum_extension_robot'	=> 0,

	/**
	* Show the support/discussion panel in each contribution to the public?
	*/
	'support_in_titania' => true,

	/**
	* If the type of post made is in this array we will increment their postcount as posts are made within titania
	*/
	'increment_postcount'	=> array(ext::TITANIA_SUPPORT),

	/**
	* Note: There are still more configuration settings!
	*
	* This example file does not contain all the configuration settings because there are quite a few more trivial settings most probably will not worry about.
	*
	* To see the additiona settings available, please see includes/core/config.php
	*/
);
