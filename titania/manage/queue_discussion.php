<?php
/**
 *
 * @package titania
 * @version $Id$
 * @copyright (c) 2008 phpBB Customisation Database Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
* @ignore
*/
if (!defined('IN_TITANIA'))
{
	exit;
}

topics_overlord::display_forums_complete('queue_discussion');

titania::page_header('QUEUE_DISCUSSION');

titania::page_footer(true, 'manage/queue_discussion.html');