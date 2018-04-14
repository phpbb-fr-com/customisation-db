<?php
/**
 *
 * @package Titania
 * @copyright (c) 2015 phpBB-fr.com website team <site@phpbb-fr.com>
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace phpbb\titania\migrations;

class hook_phpbb_fr extends base
{
	static public function depends_on()
	{
		return array('\phpbb\titania\migrations\release_1_1_0');
	}

	public function effectively_installed()
	{
		$table_prefix = $this->get_titania_table_prefix();
		return $this->db_tools->sql_column_exists($table_prefix . 'topics', 'phpbb_topic_id');
	}

	public function update_schema()
	{
		$table_prefix = $this->get_titania_table_prefix();

		return array(
			'add_columns'	   => array(
				$table_prefix . 'topics'	=> array(
					'phpbb_topic_id'	=> array('UINT', 0),
				),
				$table_prefix . 'posts'		=> array(
					'phpbb_post_id'		=> array('UINT', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		$table_prefix = $this->get_titania_table_prefix();

		return array(
			'drop_columns'	   => array(
				$table_prefix . 'topics'	=> array(
					'phpbb_topic_id',
				),
				$table_prefix . 'posts'	=> array(
					'phpbb_post_id',
				),
			),
		);
	}
}

