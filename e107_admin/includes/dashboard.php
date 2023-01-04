 <?php

/**
 * @file
 * Flexpanel dashboard style.
 */

if(!defined('e107_INIT'))
{
	exit;
}

// Get "Apply dashboard preferences to all administrators" setting.
$adminPref = e107::getConfig()->get('adminpref', 0);
$flepanelEnabled = true;

// If not Main Admin and "Apply dashboard preferences to all administrators" is checked.
if(!getperms('1') && $adminPref == 1)
{
	$flepanelEnabled = false;
}

define('FLEXPANEL_ENABLED', $flepanelEnabled);


// Save rearranged menus to user.
if(e_AJAX_REQUEST)
{
	if(FLEXPANEL_ENABLED && varset($_POST['core-flexpanel-order'], false))
	{
		// If "Apply dashboard preferences to all administrators" is checked.
		if($adminPref == 1)
		{
			e107::getConfig()
				->setPosted('core-flexpanel-order', $_POST['core-flexpanel-order'])
				->save();
		}
		else
		{
			e107::getUser()
				->getConfig()
				->set('core-flexpanel-order', $_POST['core-flexpanel-order'])
				->save();
		}
		exit;
	}
}

// Dashboard uses infopanel's methods to avoid code duplication.
// not used directly flexpanel - intention 
e107_require_once(e_ADMIN . 'includes/infopanel.php');

/* Notes
core-flexpanel-layout is not used because layout is replaced with dashboard template 

*/

/**
 * Class adminstyle_flexpanel.
 */
class adminstyle_dashboard extends adminstyle_infopanel
{


		private $iconlist = array();
		static $positions	= array();

		/**
		 * Constructor.
		 */
		public function __construct()
		{
			parent::__construct();

			$this->iconlist = $this->getIconList();

			self::$positions = e107::getTemplate(false, 'dashboard', 'positions');

			if (FLEXPANEL_ENABLED)
			{
				e107::css('inline', '.draggable-panels .panel-heading { cursor: move; }');
				e107::js('core', 'core/admin.flexpanel.js', 'jquery', 4);

				if (varset($_GET['mode']) == 'customize')
				{
					e107::css('inline', '.layout-container { display: table; margin-left: auto; margin-right: auto; }');
					e107::css('inline', '.layout-container label.radio { float: left; padding: 0; width: 120px; margin: 7px; cursor: pointer; text-align: center; }');
					e107::css('inline', '.layout-container label.radio img { margin-left: auto; margin-right: auto; display: block; }');
					e107::css('inline', '.layout-container label.radio input { width: 100%; margin-left: auto; margin-right: auto; display: block; }');
					e107::css('inline', '.layout-container label.radio p { width: 100%; text-align: center; display: block; margin: 20px 0 0 0; }');
				}

				// Save posted Layout type.
				if (varset($_POST['e-flexpanel-layout']))
				{
					$user_pref = $this->getUserPref();

					// If Layout has been changed, we clear previous arrangement in order to use defaults.
					if ($user_pref['core-flexpanel-layout'] != $_POST['e-flexpanel-layout'])
					{
						$this->savePref('core-flexpanel-order', array());
					}

					$this->savePref('core-flexpanel-layout', $_POST['e-flexpanel-layout']);
				}
			}
		}


		/**
		 * Get selected area and position for a menu item.
		 *
		 * @param $id
		 *  Menu ID.
		 * @return array
		 *  Contains menu area and weight.
		 */
		function getMenuPosition($id)
		{
			$user_pref = $this->getUserPref();

			if (!empty($user_pref['core-flexpanel-order'][$id]))
			{
				return $user_pref['core-flexpanel-order'][$id];
			}

			$default = array(
				'area'   => 'menu-area-01',
				'weight' => 1000,
			);

			$positions = $this->getDefaultPositions();

			$layout = varset($user_pref['core-flexpanel-layout'], 'default');

			if (!empty($positions[$layout][$id]))
			{
				return $positions[$layout][$id];
			}

			if (strpos($id, 'plug-infopanel-') === 0) // addon dashboards default to area 2.
			{
				$default = array(
					'area'   => 'menu-area-02',
					'weight' => 1000,
				);
			}

			return $default;
		}


		/**
		 * Get default menu positions.
		 *
		 * @return array
		 */
		function getDefaultPositions()
		{
			return array( 
				'default'           => array(
					'core-infopanel-mye107'         => array(
						'area'   => 'menu-area-07',
						'weight' => 0,
					),
					'core-infopanel-news'           => array(
						'area'   => 'menu-area-08',
						'weight' => 0,
					),
					'core-infopanel-website_status' => array(
						'area'   => 'menu-area-08',
						'weight' => 1,
					),
				),
			);
		}

		/**
		 * Render contents.
		 */
		public function render()
		{
			/** @var admin_shortcodes $admin_sc */
			$admin_sc = e107::getScBatch('admin');
			$tp = e107::getParser();
			$ns = e107::getRender();
			$mes = e107::getMessage();
			$pref = e107::getPref();
			$frm = e107::getForm();

			$user_pref = $this->getUserPref();

			if (varset($_GET['mode']) == 'customize')
			{
				echo $frm->open('infopanel', 'post', e_SELF);
				//echo $ns->tablerender(LAN_DASHBOARD_LAYOUT, $this->renderLayoutPicker(), 'personalize', true);
				echo '<div class="clear">&nbsp;</div>';
				echo $this->render_infopanel_options(true);
				echo $frm->close();
				return;
			}
 
			// --------------------- Personalized Panel -----------------------
			$myE107 = varset($user_pref['core-infopanel-mye107'], array());
			if (empty($myE107)) // Set default icons.
			{
				$user_pref['core-infopanel-mye107'] = e107::getNav()->getDefaultAdminPanelArray();
			}

			$ns->setStyle('flexpanel');
			$mainPanel = "<div id='core-infopanel-mye107'>";
			$mainPanel .= "<div class='left'>";
			$count = 0;
			foreach ($this->iconlist as $key => $val)
			{
				if (in_array($key, $user_pref['core-infopanel-mye107']))
				{
					if ($tmp = e107::getNav()->renderAdminButton($val['link'], $val['title'], $val['caption'], $val['perms'], $val['icon_32'], "div"))
					{
						$mainPanel .= $tmp;
						$count++;
					}
				}

				if ($count == 20)
				{
					break;
				}
			}
			$mainPanel .= "</div></div>";

			// Rendering the saved configuration.
			$ns->setStyle('flexpanel');
			$caption = $tp->lanVars(LAN_CONTROL_PANEL, ucwords(USERNAME));
			$ns->setUniqueId('core-infopanel-mye107');
			$coreInfoPanelMyE107 = $ns->tablerender($caption, $mainPanel, "core-infopanel-mye107", true);
			$info = $this->getMenuPosition('core-infopanel-mye107');
			if (!isset(self::$positions[$info['area']][$info['weight']]))
			{
				self::$positions[$info['area']][$info['weight']] = '';
			}
			self::$positions[$info['area']][$info['weight']] .= $coreInfoPanelMyE107;


			// --------------------- e107 News --------------------------------
			$newsTabs = array();
			$newsTabs['coreFeed'] = array('caption' => LAN_GENERAL, 'text' => "<div id='e-adminfeed' style='min-height:300px'></div><div class='right'><a rel='external' href='" . ADMINFEEDMORE . "'>" . LAN_MORE . "</a></div>");
			$newsTabs['pluginFeed'] = array('caption' => LAN_PLUGIN, 'text' => "<div id='e-adminfeed-plugin'></div>");
			$newsTabs['themeFeed'] = array('caption' => LAN_THEMES, 'text' => "<div id='e-adminfeed-theme'></div>");
			$ns->setStyle('flexpanel');
			$ns->setUniqueId('core-infopanel-news');
			$coreInfoPanelNews = $ns->tablerender(LAN_LATEST_e107_NEWS, e107::getForm()->tabs($newsTabs, array('active' => 'coreFeed')), "core-infopanel-news", true);
			$info = $this->getMenuPosition('core-infopanel-news');
			if (!isset(self::$positions[$info['area']][$info['weight']]))
			{
				self::$positions[$info['area']][$info['weight']] = '';
			}
			self::$positions[$info['area']][$info['weight']] .= $coreInfoPanelNews;


			// --------------------- Website Status ---------------------------
			/*	$ns->setStyle('flexpanel');
		$ns->setUniqueId('core-infopanel-website_status');
		$coreInfoPanelWebsiteStatus = '';// 'hi';/// "<div id='core-infopanel-website_status'>".$this->renderAddonDashboards()."</div>";  $ns->tablerender(LAN_WEBSITE_STATUS, $this->renderAddonDashboards(), "core-infopanel-website_status", true);
		$info = $this->getMenuPosition('core-infopanel-website_status');
		self::$positions[$info['area']][$info['weight']] .= $coreInfoPanelWebsiteStatus;*/


			// --------------------- Latest Comments --------------------------
			// self::$positions['Area01'] .= $this->renderLatestComments(); // TODO
 
			// --------------------- User Selected Menus ----------------------
			if (varset($user_pref['core-infopanel-menus']))
			{
				$ns->setStyle('flexpanel');
				foreach ($user_pref['core-infopanel-menus'] as $val)
				{
					// Custom menu.
					if (is_numeric($val))
					{
						$menu = e107::getDb()->retrieve('page', 'menu_name', 'page_id = ' . (int) $val);
						$id = 'cmenu-' . $menu;
						$inc = e107::getMenu()->renderMenu($val, null, null, true);
					}
					else
					{
						$id = $frm->name2id($val);
						$inc = $tp->parseTemplate("{PLUGIN=$val|TRUE}");
					}
					$info = $this->getMenuPosition($id);
					if (!isset(self::$positions[$info['area']][$info['weight']]))
					{
						self::$positions[$info['area']][$info['weight']] = '';
					}
					self::$positions[$info['area']][$info['weight']] .= $inc;
				}
			}


			// --------------------- Plugin Addon Dashboards ---------------------- eg. e107_plugin/user/e_dashboard.php
			$dashboards = $this->getAddonDashboards();
			if (!empty($dashboards))
			{
				$ns->setStyle('flexpanel');
				foreach ($dashboards as $val)
				{
					$id = $val['mode'];
					$ns->setUniqueId($id);
					$inc = $ns->tablerender($val['caption'], $val['text'], $val['mode'], true);
					$info = $this->getMenuPosition($id);
					if (!isset(self::$positions[$info['area']][$info['weight']]))
					{
						self::$positions[$info['area']][$info['weight']] = '';
					}
					self::$positions[$info['area']][$info['weight']] .= $inc;
				}
			}


			// Sorting panels.
			foreach (self::$positions as $key => $value)
			{
				ksort(self::$positions[$key]);
			}

			$FLEXPANEL_LAYOUT = e107::getCoreTemplate("dashboard", 'layout');

			include_once($layout_file);

			$template = varset($FLEXPANEL_LAYOUT);
			$template = str_replace('{MESSAGES}', $mes->render(), $template);

			foreach (self::$positions as $key => $value)
			{
				$token = '{' . strtoupper(str_replace('-', '_', $key)) . '}';
				$template = str_replace($token, implode("\n", $value), $template);
			}

			echo $template;
			 
		}

}

