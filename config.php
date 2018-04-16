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

if (@file_exists('../../../../../includes/titania_config.php'))
{
	include '../../../../../includes/titania_config.php';
}
else
{
	@include 'titania_config.php'; // Included from include_path
}
