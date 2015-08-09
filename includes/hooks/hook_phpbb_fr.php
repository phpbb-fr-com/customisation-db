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

use phpbb\titania\message\message;

titania::$hook->register_ary('phpbb_fr_', array(
	array('titania_queue', 'update_first_queue_post'),
	array('titania_topic', '__construct'),
	array('titania_post', '__construct'),
	array('titania_post', 'post'),
	array('titania_post', 'edit'),
	array('titania_post', 'hard_delete'),
	array('titania_queue', 'approve'),
	array('titania_queue', 'deny'),
	array('titania_queue', 'close'),
	array('titania_queue', 'delete'),
	//array('titania_contribution', 'assign_details'),
));

function phpbb_fr_forum_id($type, $mode, $branch)
{
	switch ($type)
	{
		case TITANIA_TYPE_EXTENSION :
			switch ($mode)
			{
				case TITANIA_QUEUE_DISCUSSION:
					$config_name = 'forum_extension_queue_discussion';
				break;

				case TITANIA_QUEUE:
					$config_name = 'forum_extension_queue';
				break;

				case 'trash':
					$config_name = 'forum_extension_queue_trash';
				break;
			}
		break;

		case TITANIA_TYPE_MOD:
			switch ($mode)
			{
				case TITANIA_QUEUE_DISCUSSION:
					$config_name = 'forum_mod_queue_discussion';
				break;

				case TITANIA_QUEUE:
					$config_name = 'forum_mod_queue';
				break;

				case 'trash':
					$config_name = 'forum_mod_queue_trash';
				break;
			}
		break;

		case TITANIA_TYPE_STYLE:
			switch ($mode)
			{
				case TITANIA_QUEUE_DISCUSSION:
					$config_name = 'forum_style_queue_discussion';
				break;

				case TITANIA_QUEUE:
					$config_name = 'forum_style_queue';
				break;

				case 'trash':
					$config_name = 'forum_style_queue_trash';
				break;
			}
		break;
	}

	if (!isset($config_name) || !isset(titania::$config->$config_name))
	{
		return 0;
	}
	
	$forums = titania::$config->$config_name;
	$branch = (string) $branch;
	return isset($forums[$branch]) ? (int) $forums[$branch] : 0;
}

/**
* Copy new posts for queue discussion, queue to the forum
*/
function phpbb_fr_titania_queue_update_first_queue_post($hook, &$post_object, $queue_object)
{
	if ($queue_object->queue_status == TITANIA_QUEUE_HIDE || !$queue_object->queue_topic_id)
	{
		return;
	}

	$path_helper = phpbb::$container->get('path_helper');
	
	$revision = $queue_object->get_revision();
	$revision->load_phpbb_versions();
	$branch = (int) $revision->phpbb_versions[0]['phpbb_version_branch'];

	// First we copy over the queue discussion topic if required
	$sql = 'SELECT topic_id, phpbb_topic_id, topic_category FROM ' . TITANIA_TOPICS_TABLE . '
		WHERE parent_id = ' . $queue_object->contrib_id . '
			AND topic_type = ' . TITANIA_QUEUE_DISCUSSION;
	$result = phpbb::$db->sql_query($sql);
	$topic_row = phpbb::$db->sql_fetchrow($result);
	phpbb::$db->sql_freeresult($result);

	// Do we need to create the queue discussion topic or not?
	if ($topic_row['topic_id'] && !$topic_row['phpbb_topic_id'])
	{
		$forum_id = phpbb_fr_forum_id($post_object->topic->topic_category, TITANIA_QUEUE_DISCUSSION, $branch);
		
		if (!$forum_id)
		{
			return;
		}

		$temp_post = new titania_post;

		// Go through any posts in the queue discussion topic and copy them
		$topic_id = false;
		$sql = 'SELECT * FROM ' . TITANIA_POSTS_TABLE . ' WHERE topic_id = ' . $topic_row['topic_id'];
		$result = phpbb::$db->sql_query($sql);
		while($row = phpbb::$db->sql_fetchrow($result))
		{
			$temp_post->__set_array($row);

			$post_text = $row['post_text'];

			phpbb_fr_handle_attachments($temp_post, $post_text);
			message::decode($post_text, $row['post_text_uid']);

			$post_text .= "\n\n" . $path_helper->strip_url_params($temp_post->get_url(), 'sid');

			$options = array(
				'poster_id'				=> $row['post_user_id'],
				'forum_id' 				=> $forum_id,
				'topic_title'			=> $row['post_subject'],
				'post_text'				=> $post_text,
			);

			titania::_include('functions_posting', 'phpbb_posting');

			if ($topic_id)
			{
				$options = array_merge($options, array(
					'topic_id'	=> $topic_id,
				));

				phpbb_posting('reply', $options);
			}
			else
			{
				switch ($topic_row['topic_category'])
				{
					case TITANIA_TYPE_EXTENSION :
						$options['poster_id'] = titania::$config->forum_extension_robot;
					break;

					case TITANIA_TYPE_MOD :
						$options['poster_id'] = titania::$config->forum_mod_robot;
					break;

					case TITANIA_TYPE_STYLE :
						$options['poster_id'] = titania::$config->forum_style_robot;
					break;
				}

				$topic_id = phpbb_posting('post', $options);
			}
		}
		phpbb::$db->sql_freeresult($result);

		if ($topic_id)
		{
			$sql = 'UPDATE ' . TITANIA_TOPICS_TABLE . '
				SET phpbb_topic_id = ' . $topic_id . '
				WHERE topic_id = ' . $topic_row['topic_id'];
			phpbb::$db->sql_query($sql);
		}

		unset($temp_post);
	}

	// Does a queue topic already exist?  If so, don't repost.
	$sql = 'SELECT phpbb_topic_id FROM ' . TITANIA_TOPICS_TABLE . '
		WHERE topic_id = ' . $queue_object->queue_topic_id;
	phpbb::$db->sql_query($sql);
	$phpbb_topic_id = phpbb::$db->sql_fetchfield('phpbb_topic_id');
	phpbb::$db->sql_freeresult();
	if ($phpbb_topic_id)
	{
		return;
	}

	$forum_id = phpbb_fr_forum_id($post_object->topic->topic_category, $post_object->topic->topic_type, $branch);

	if (!$forum_id)
	{
		return;
	}

	$post_object->submit();

	titania::_include('functions_posting', 'phpbb_posting');

	// Need some stuff
	phpbb::$user->add_lang_ext('phpbb/titania', 'contributions');
	$contrib = new titania_contribution;
	$contrib->load((int) $queue_object->contrib_id);
	$revision = $queue_object->get_revision();
	$contrib->get_download($revision->revision_id);

	switch ($post_object->topic->topic_category)
	{
		case TITANIA_TYPE_EXTENSION :
			$post_object->topic->topic_first_post_user_id = titania::$config->forum_extension_robot;
			$lang_var = 'EXTENSION_QUEUE_TOPIC';
		break;

		case TITANIA_TYPE_MOD :
			$post_object->topic->topic_first_post_user_id = titania::$config->forum_mod_robot;
			$lang_var = 'MOD_QUEUE_TOPIC';
		break;

		case TITANIA_TYPE_STYLE :
			$post_object->topic->topic_first_post_user_id = titania::$config->forum_style_robot;
			$lang_var = 'STYLE_QUEUE_TOPIC';
		break;

		default :
			return;
	}

	$description = $contrib->contrib_desc;
	message::decode($description, $contrib->contrib_desc_uid);
	$download = current($contrib->download);

	$post_text = sprintf(phpbb::$user->lang[$lang_var],
		$contrib->contrib_name,
		$path_helper->strip_url_params($contrib->author->get_url(), 'sid'),
		users_overlord::get_user($contrib->author->user_id, '_username'),
		$description,
		$revision->revision_version,
		$path_helper->strip_url_params($revision->get_url(), 'sid'),
		$download['real_filename'],
		get_formatted_filesize($download['filesize'])
	);

	$post_text .= "\n\n" . $post_object->post_text;

	phpbb_fr_handle_attachments($post_object, $post_text);
	message::decode($post_text, $post_object->post_text_uid);

	$post_text .= "\n\n" . $path_helper->strip_url_params($post_object->get_url(), 'sid');

	$options = array(
		'poster_id'				=> $post_object->topic->topic_first_post_user_id,
		'forum_id' 				=> $forum_id,
		'topic_title'			=> $post_object->topic->topic_subject,
		'post_text'				=> $post_text,
	);

	$topic_id = phpbb_posting('post', $options);

	$post_object->topic->phpbb_topic_id = $topic_id;

	$sql = 'UPDATE ' . TITANIA_TOPICS_TABLE . '
		SET phpbb_topic_id = ' . (int) $topic_id . '
		WHERE topic_id = ' . $post_object->topic->topic_id;
	phpbb::$db->sql_query($sql);
}

function phpbb_fr_titania_topic___construct($hook, &$topic_object)
{
	$topic_object->object_config = array_merge($topic_object->object_config, array(
		'phpbb_topic_id'	=> array('default' => 0),
	));
}

function phpbb_fr_titania_post___construct($hook, &$post_object)
{
	$post_object->object_config = array_merge($post_object->object_config, array(
		'phpbb_post_id'	=> array('default' => 0),
	));
}

function phpbb_fr_titania_post_post($hook, &$post_object)
{
	if (defined('IN_TITANIA_CONVERT') || !$post_object->topic->phpbb_topic_id)
	{
		return;
	}

	titania::_include('functions_posting', 'phpbb_posting');

	$path_helper = phpbb::$container->get('path_helper');
	$post_text = $post_object->post_text;

	phpbb_fr_handle_attachments($post_object, $post_text);
	message::decode($post_text, $post_object->post_text_uid);

	$post_text .= "\n\n" . $path_helper->strip_url_params($post_object->get_url(), 'sid');

	$options = array(
		'poster_id'				=> $post_object->post_user_id,
		'topic_id'				=> $post_object->topic->phpbb_topic_id,
		'topic_title'			=> $post_object->post_subject,
		'post_text'				=> $post_text,
	);

	$post_object->phpbb_post_id = phpbb_posting('reply', $options);

	$sql = 'UPDATE ' . TITANIA_POSTS_TABLE . '
		SET phpbb_post_id = ' . $post_object->phpbb_post_id . '
		WHERE post_id = ' . $post_object->post_id;
	phpbb::$db->sql_query($sql);
}

function phpbb_fr_titania_post_edit($hook, &$post_object)
{
	if (defined('IN_TITANIA_CONVERT') || !$post_object->phpbb_post_id)
	{
		return;
	}

	titania::_include('functions_posting', 'phpbb_posting');

	$path_helper = phpbb::$container->get('path_helper');
	$post_text = $post_object->post_text;

	phpbb_fr_handle_attachments($post_object, $post_text);
	message::decode($post_text, $post_object->post_text_uid);

	$post_text .= "\n\n" . $path_helper->strip_url_params($post_object->get_url(), 'sid');

	$options = array(
		'post_id'				=> $post_object->phpbb_post_id,
		'topic_title'			=> $post_object->post_subject,
		'post_text'				=> $post_text,
	);

	phpbb_posting('edit', $options);
}

function phpbb_fr_titania_post_hard_delete($hook, &$post_object)
{
	if (defined('IN_TITANIA_CONVERT') || !$post_object->phpbb_post_id)
	{
		return;
	}

	phpbb::_include('functions_posting', 'delete_post');

	$sql = 'SELECT t.*, p.*
	FROM ' . TOPICS_TABLE . ' t, ' . POSTS_TABLE . ' p
		WHERE p.post_id = ' . $post_object->phpbb_post_id . '
		AND t.topic_id = p.topic_id';
	$result = phpbb::$db->sql_query($sql);
	$post_data = phpbb::$db->sql_fetchrow($result);
	phpbb::$db->sql_freeresult($result);

	delete_post($post_data['forum_id'], $post_data['topic_id'], $post_data['post_id'], $post_data);
}


/**
* Move queue topics to the trash can
*/

function phpbb_fr_titania_queue_approve($hook, &$queue_object)
{
	phpbb_fr_move_queue_topic($queue_object);
}

function phpbb_fr_titania_queue_deny($hook, &$queue_object)
{
	phpbb_fr_move_queue_topic($queue_object);
}

function phpbb_fr_titania_queue_close($hook, &$queue_object)
{
	phpbb_fr_move_queue_topic($queue_object);
}

function phpbb_fr_titania_queue_delete($hook, &$queue_object)
{
	phpbb_fr_move_queue_topic($queue_object);
}

function phpbb_fr_move_queue_topic($queue_object)
{
	$sql = 'SELECT phpbb_topic_id, topic_category FROM ' . TITANIA_TOPICS_TABLE . '
		WHERE topic_id = ' . (int) $queue_object->queue_topic_id;
	$result = phpbb::$db->sql_query($sql);
	$row = phpbb::$db->sql_fetchrow($result);
	phpbb::$db->sql_freeresult($result);

	if (!$row['phpbb_topic_id'])
	{
		return;
	}

	phpbb::_include('functions_admin', 'move_topics');
	
	$revision = $queue_object->get_revision();
	$revision->load_phpbb_versions();
	$branch = (int) $revision->phpbb_versions[0]['phpbb_version_branch'];

	move_topics($row['phpbb_topic_id'], phpbb_fr_forum_id($row['topic_category'], 'trash', $branch));
}

// Display a warning for styles not meeting the licensing guidelines
function phpbb_fr_titania_contribution_assign_details($hook, &$vars, $contrib)
{
	if ($contrib->contrib_type != TITANIA_TYPE_STYLE || empty($contrib->download))
	{
		return;
	}

	foreach ($contrib->download as $download)
	{
		if ($download['revision_license'] == '')
		{
			if (isset($vars['WARNING']))
			{
				$vars['WARNING'] .= '<br />';
			}
			else
			{
				$vars['WARNING'] = '';
			}

			$vars['WARNING'] .= 'WARNING: This style currently does not meet our licensing guidelines.';

			break;
		}
	}
}

function phpbb_fr_handle_attachments($post, &$post_text)
{
	if (!$post->post_attachment)
	{
		return;
	}

	$sort_order = (phpbb::$config['display_order']) ? 'ASC' : 'DESC';

	$sql = 'SELECT attachment_id, real_filename
		FROM ' . TITANIA_ATTACHMENTS_TABLE . '
		WHERE is_orphan = 0
			AND object_type = ' . (int) $post->post_type . '
			AND object_id = ' . (int) $post->post_id . '
		ORDER BY attachment_id ' . $sort_order;
	$result = phpbb::$db->sql_query($sql);
	$attachments = array();

	phpbb::$user->add_lang('viewtopic');
	$path_helper = phpbb::$container->get('path_helper');
	$controller_helper = phpbb::$container->get('controller.helper');

	while ($row = phpbb::$db->sql_fetchrow($result))
	{
		$download_url = $path_helper->strip_url_params(
			$controller_helper->route('phpbb.titania.download', array('id' => $row['attachment_id'])),
			'sid'
		);
		$attachments[] = '[' . phpbb::$user->lang['ATTACHMENT'] . "] [url=$download_url]{$row['real_filename']}[/url]";
	}
	phpbb::$db->sql_freeresult($result);

	if (empty($attachments))
	{
		return;
	}

	preg_match_all('#<!\-\- ia([0-9]+) \-\->(.*?)<!\-\- ia\1 \-\->#', $post_text, $matches, PREG_PATTERN_ORDER);

	$replace = array();
	foreach ($matches[0] as $num => $capture)
	{
		// Flip index if we are displaying the reverse way
		$index = (phpbb::$config['display_order']) ? ($tpl_size-($matches[1][$num] + 1)) : $matches[1][$num];

		$replace['from'][] = $matches[0][$num];
		$replace['to'][] = (isset($attachments[$index])) ? "\n$attachments[$index]\n" : '';

		unset($attachments[$index]);
	}

	if (isset($replace['from']))
	{
		$post_text = str_replace($replace['from'], $replace['to'], $post_text);
	}

	if (!empty($attachments))
	{
		$post_text .= "\n\n" . implode("\n", $attachments);
	}
}
