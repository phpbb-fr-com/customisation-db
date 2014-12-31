<?php
/**
*
* @package Titania
* @copyright (c) 2014 phpBB Limited
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_TITANIA'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'EXTENSION'							=> 'Extension',
	'EXTENSIONS'						=> 'Extensions',
	'EXTENSION_CREATE_PUBLIC'			=> '[b]Extension name[/b]: %1$s
[b]Author:[/b] [url=%2$s]%3$s[/url]
[b]Extension description[/b]: %4$s
[b]Extension version[/b]: %5$s
[b]Tested on phpBB version[/b]: %11$s

[b]Download file[/b]: [url=%6$s]%7$s[/url]
[b]File size:[/b] %8$s Bytes

[b]Extension overview page:[/b] [url=%9$s]View[/url]

[color=blue][b]The phpBB Team is not responsible nor required to provide support for this extension. By installing this extension, you acknowledge that the phpBB Support Team or phpBB Extensions Team may not be able to provide support.[/b][/color]

[size=150][url=%10$s]--&gt;[b]Extension support[/b]&lt;--[/url][/size]',
	'EXTENSION_QUEUE_TOPIC'				=> '[b]Extension name[/b]: %1$s
[b]Author:[/b] [url=%2$s]%3$s[/url]
[b]Extension description[/b]: %4$s
[b]Extension version[/b]: %5$s

[b]Download file[/b]: [url=%6$s]%7$s[/url]
[b]File size:[/b] %8$s Bytes',
	'EXTENSION_REPLY_PUBLIC'			=> '[b][color=darkred]Extension validated/released[/color][/b]',
	'EXTENSION_REPLY_PUBLIC_NOTES'		=> '

[b]Notes:[/b] %s',
	'EXTENSION_UPDATE_PUBLIC'			=> '[b][color=darkred]extension Updated to version %1$s
See first post for Download Link[/color][/b]',
	'EXTENSION_UPDATE_PUBLIC_NOTES'		=> '

[b]Notes:[/b] %1$s',
	'EXTENSION_UPLOAD_AGREEMENT'		=> '<span style="font-size: 1.5em;">By submitting this revision you agree to abide by the <a href="http://www.phpbb.com/extensions/policies/">Extensions database policies</a> and that your extension conforms to and follows the <a href="https://area51.phpbb.com/docs/31x/coding-guidelines.html">phpBB 3.1 Coding Guidelines</a>.

You also agree and accept that this extension’s license and the license of any included components are compatible with the <a href="http://www.gnu.org/licenses/gpl-2.0.html">GNU GPLv2</a> and that you also allow the re-distributibution of your extension through this website indefinitely. For a list of available licenses and licenses compatible with the GNU GPLv2 please reference the <a href="http://en.wikipedia.org/wiki/List_of_FSF_approved_software_licenses">list of FSF approved software licenses</a>.</span>',
	'EXTENSION_VALIDATION'				=> '[phpBB Extension-Validation] %1$s %2$s',
	'EXTENSION_VALIDATION_MESSAGE_APPROVE'	=> 'Thank you for submitting your extension to the phpBB.com extensions database. After careful inspection by the Extensions Team, your extension has been [b][color=#5c8503]approved[/color][/b] and released into our extensions database.

It is our hope that you will provide a basic level of support for this extension and keep it updated with future releases of phpBB. We appreciate your work and contribution to the community. Authors like yourself make phpBB.com a better place for everyone.

[b]Notes from the Extensions Team about your extension:[/b]
[quote]%s[/quote]

Sincerely,
phpBB Extensions Team',
	'EXTENSIONS_VALIDATION_MESSAGE_DENY'		=> 'Hello,

As you may know all extensions submitted to the phpBB extensions database must be validated and approved by members of the phpBB Team.

Upon validating your extension the phpBB Extensions Team regrets to inform you that we have had to [b][color=#A91F1F]deny[/color][/b] your extension.

To correct the problem(s) with your extension, please following the below instructions:
[list=1][*]Make the necessary changes to correct any problems (listed below) that resulted in your extension being denied.
[*]Re-upload your extension to our extensions database.[/list]
Please ensure you tested your extension on the latest version of phpBB (see the [url=http://www.phpbb.com/downloads/]Downloads[/url] page) before you re-submit your extension.

Here is a report on why your extension was denied:
[quote]%s[/quote]

Please refer to the following links before you reupload your extension:
[list][/list]

For further reading, you may want to review the following:
[list][/list]

For help with writing phpBB extensions, the following resources exist:
[list][*][url=https://www.phpbb.com/community/viewforum.php?f=461]Extension Writers Discussion forum[/url]
[*]IRC Support - [url=irc://irc.freenode.net/phpBB-coding]#phpBB-coding[/url] is registered on the FreeNode IRC network ([url=irc://irc.freenode.net/]irc.freenode.net[/url])[/list]

[b]If you wish to discuss anything in this PM please use the “Validation Discussion“ sticky topic located in your extension’s Discussion/Support tab.[/b]

If you feel this denial was not warranted please contact the Extension Validation Leader.
If you have any queries and further discussion please use the Queue Discussion Topic.

Thank you,
phpBB Extensions Team',
));
