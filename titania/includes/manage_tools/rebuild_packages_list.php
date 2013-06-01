<?php
/**
*
* @package Titania
* @copyright (c) 2013 phpBB Customisation Database Team
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

class rebuild_packages_list
{
	function display_options()
	{
		return 'REBUILD_PACKAGES_LIST';
	}

	function run_tool()
	{
		if (!titania::$config->composer_vendor_name)
		{
			trigger_error('COMPOSER_VENDOR_EMPTY');
		}

		titania::_include('tools/composer_package_manager', false, 'titania_composer_package_helper');
		$package_helper = new titania_composer_package_helper();

		// We can't run the tool if the composer directory is not writable...
		if (!$package_helper->packages_dir_writable())
		{
			trigger_error('COMPOSER_DIR_NOT_WRITABLE');
		}

		$start = request_var('start', 0);
		$batch_size = 100;

		// Remove any JSON files present in the directory
		if (!$start)
		{
			$package_helper->clear_packages_dir();
		}

		$excluded_types = titania_types::use_composer(true); 

		$sql = 'SELECT COUNT(contrib_id) AS total_contribs
			FROM ' . TITANIA_CONTRIBS_TABLE . '
			WHERE contrib_status = ' . TITANIA_CONTRIB_APPROVED .
				(!empty($excluded_types) ? ' AND ' . phpbb::$db->sql_in_set('contrib_type', $excluded_types, true) : '');
		phpbb::$db->sql_query($sql);
		$total_contribs = phpbb::$db->sql_fetchfield('total_contribs');

		$sql = 'SELECT contrib_name_clean, contrib_id, contrib_type 
			FROM ' . TITANIA_CONTRIBS_TABLE . '
			WHERE contrib_status = ' . TITANIA_CONTRIB_APPROVED .
				(!empty($excluded_types) ? ' AND ' . phpbb::$db->sql_in_set('contrib_type', $excluded_types, true) : '') . '
			ORDER BY contrib_type ASC, contrib_id ASC';
		$result = phpbb::$db->sql_query_limit($sql, $batch_size, $start);

		$contribs = array();
		while ($row = phpbb::$db->sql_fetchrow($result))
		{
			$contribs[$row['contrib_id']] = $row;
		}
		phpbb::$db->sql_freeresult($result);

		// Grab all approved revisions for the contribs that we're handling
		$sql = 'SELECT contrib_id, revision_version, attachment_id
			FROM ' . TITANIA_REVISIONS_TABLE . '
			WHERE revision_status = ' . TITANIA_REVISION_APPROVED . ' 
				AND ' . phpbb::$db->sql_in_set('contrib_id', array_keys($contribs)) . '
			ORDER BY contrib_id ASC, revision_id ASC';
		$result = phpbb::$db->sql_query($sql);

		while ($row = phpbb::$db->sql_fetchrow($result))
		{
			$contribs[$row['contrib_id']]['revisions'][] = $row;
		}
		phpbb::$db->sql_freeresult($result);

		$prev_type = 0;
		$i = sizeof($contribs);

		foreach ($contribs as $contrib_id => $data)
		{
			$i--;

			if (empty($data['revisions']))
			{
				continue;
			}

			if ($prev_type != $data['contrib_type'])
			{
				if (isset($package_manager))
				{
					$this->put_data($package_manager, $map);
				}

				$package_manager = new titania_composer_package_manager($contrib_id, $data['contrib_name_clean'], $data['contrib_type'], $package_helper, false);
				$map = $package_manager->helper->get_json_data($package_manager->type_name . '-map', true);

				$group_count = sizeof($package_manager->packages_data);
				$group = explode('-', $package_manager->packages_file);
				$group = $group[2];
			}
			else
			{
				$package_manager->contrib_id = $contrib_id;
				$package_manager->package_name = titania::$config->composer_vendor_name . '/' . $data['contrib_name_clean'];

				if ($group != $prev_group)
				{
					$package_manager->set_group_data($group);
				}
			}

			foreach ($data['revisions'] as $revision)
			{
				$package_manager->add_release($revision['revision_version'], $revision['attachment_id'], true);
			}

			$map[] = (int) $contrib_id;
			$prev_group = $group;
			$prev_type = $data['contrib_type'];
			$group_count++;

			// Write the data if the group is full or if this is the last contrib in the batch.
			if ($group_count == $package_manager->contribs_per_file || $i == 0)
			{
				$this->put_data($package_manager, $map);
				$group++;
			}
		}

		$start += $batch_size;

		if ($start >= $total_contribs)
		{
			trigger_error('DONE');
		}

		meta_refresh(3, titania_url::build_url('manage/administration', array('t' => 'rebuild_packages_list', 'start' => $start, 'submit' => 1, 'hash' => generate_link_hash('manage'))));
		trigger_error($start . '/' . $total_contribs);
	}

	private function put_data($package_manager, $map)
	{
		$package_manager->helper->put_json_data($package_manager->type_name . '-map', $map);
		$package_manager->submit();
	}
}
