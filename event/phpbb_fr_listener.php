<?php
/**
 *
 * @package Titania
 * @copyright (c) 2015 phpBB-fr.com website team <site@phpbb-fr.com>
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace phpbb\titania\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
* Event listener
*/
class phpbb_fr_listener implements EventSubscriberInterface
{
	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\titania\controller\helper */
	protected $controller_helper;

	/** @var \phpbb\titania\config\config */
	protected $ext_config;

	/**
	 * Constructor
	 *
	 * @param \phpbb\user $user
	 * @param \phpbb\template\template $template
	 * @param \phpbb\titania\controller\helper $controller_helper
	 * @param \phpbb\titania\config\config $ext_config
	 */
	public function __construct(\phpbb\user $user, \phpbb\template\template $template, \phpbb\titania\controller\helper $controller_helper, \phpbb\titania\config\config $ext_config)
	{
		$this->user = $user;
		$this->template = $template;
		$this->controller_helper = $controller_helper;
		$this->ext_config = $ext_config;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.page_header_after'	=> 'menunav_link',
		);
	}

	public function menunav_link()
	{
		$this->user->add_lang_ext('phpbb/titania', 'phpbb_fr');
		$this->template->assign_var('U_CUSTOMISATIONS', $this->get_real_url($this->controller_helper->route('phpbb.titania.general')));
	}

	/**
	 * Modify URL to point back to correct Titania location.
	 *
	 * Since the UCP module does not run from app.php, the generated route will
	 * always point back under the phpBB board. The URL needs to be adjusted
	 * if Titania is running from an app.php that is not under the board root.
	 *
	 * Borrowed from the UCP controller of Titania
	 *
	 * @param string $url
	 * @return string
	 */
	protected function get_real_url($url)
	{
		if ($this->ext_config->titania_script_path)
		{
			return generate_board_url(true) .'/'. rtrim($this->ext_config->titania_script_path, '/') .
			substr($url, strlen(generate_board_url()));
		}
		return $url;
	}
}
