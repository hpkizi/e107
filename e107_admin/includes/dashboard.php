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
		public $positions	= array();
		static $userAdminPanelArray = array();

		/**
		 * Constructor.
		 */
		public function __construct()
		{
			parent::__construct();

			$this->iconlist = $this->getIconList();

			$this->positions = e107::getTemplate(false, 'dashboard', 'positions');
			self::$userAdminPanelArray = $this->getUserAdminPanelArray();

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
		 * Allow to set your own admin panels.
		 */
		function getUserAdminPanelArray()
		{
			$user_pref = $this->getUserPref();
			$myE107 = varset($user_pref['core-infopanel-mye107'], array());
			if (empty($myE107)) // Set default icons.
			{
				$user_pref['core-infopanel-mye107'] = e107::getNav()->getDefaultAdminPanelArray();
			}
			$this->userAdminPanelArray = $user_pref['core-infopanel-mye107'];
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
				'area'   => 'menu-area-02',
				'weight' => 1000,
			);

			$positions = $this->getDefaultPositions();

			$layout = varset($user_pref['core-flexpanel-layout'], 'default');
 
			if (!empty($positions[$layout][$id]))
			{
				return $positions[$layout][$id];
			}

			/*if (strpos($id, 'plug-infopanel-') === 0) // addon dashboards default to area 2.
			{
				$default = array(
					'area'   => 'menu-area-02',
					'weight' => 1000,
				);
			}
*/
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

		public function core_infopanel_news($options = array()) 
		{
			$ns = e107::getRender();
		 
			$dashboardUniqueId 	= varset($options['uniqueId'], time());
			$dashboardStyle		= varset($options['style'], 'flexbox');

			$newsTabs = array();
			$newsTabs['coreFeed'] = array('caption' => LAN_GENERAL, 'text' => "<div id='e-adminfeed' style='min-height:300px'></div><div class='right'><a rel='external' href='" . ADMINFEEDMORE . "'>" . LAN_MORE . "</a></div>");
			$newsTabs['pluginFeed'] = array('caption' => LAN_PLUGIN, 'text' => "<div id='e-adminfeed-plugin'></div>");
			$newsTabs['themeFeed'] = array('caption' => LAN_THEMES, 'text' => "<div id='e-adminfeed-theme'></div>");

			$code = "
			jQuery(function($){
				$('#e-adminfeed').load('" . e_ADMIN . "admin.php?mode=core&type=feed');
				$('#e-adminfeed-plugin').load('" . e_ADMIN . "admin.php?mode=addons&type=plugin');
				$('#e-adminfeed-theme').load('" . e_ADMIN . "admin.php?mode=addons&type=theme');
				
			
			});
			";
			e107::js('inline', $code, 'jquery');

			$ns->setStyle( $dashboardStyle);
			$ns->setUniqueId($dashboardUniqueId);
		 
			$coreInfoPanelNews = $ns->tablerender(LAN_LATEST_e107_NEWS, e107::getForm()->tabs($newsTabs, array('active' => 'coreFeed')), $dashboardUniqueId, true);
			
			return $coreInfoPanelNews;
		}

		/**
		 * Display icons
		 */
		function core_infopanel_icons($options = array())
		{
 
			$ns = e107::getRender();

			$dashboardUniqueId 	= varset($options['uniqueId'], time());
			$dashboardStyle		= varset($options['style'], 'flexbox');
			$dashboardLinks     = varset($options['links'], '');
			$dashboardCaption     = varset($options['caption'], '');

			$newarray = e107::getNav()->adminLinks($dashboardLinks);
	 
			$adminPanel = "<div id='.$dashboardUniqueId.' >";

			foreach ($newarray as $key => $val)
			{
				if (in_array($key, $this->userAdminPanelArray))
				{
					if ($tmp = e107::getNav()->renderAdminButton($val['link'], $val['title'], $val['caption'], $val['perms'], $val['icon_32'], "div"))
					{
						$adminPanel .= $tmp;
					}
				}
			}
			$adminPanel .= "</div>";

			$ns->setStyle($dashboardStyle);
			$ns->setUniqueId($dashboardUniqueId);

			$coreInfoPanelAdmin = $ns->tablerender($dashboardCaption, $adminPanel, $dashboardUniqueId, true);

			return $coreInfoPanelAdmin;
		}


		/* Comments */
		function core_infopanel_comments($options = array())
 		{
			$ns = e107::getRender();

			$dashboardUniqueId 	= "plug-infopanel-comments-0";
			$dashboardStyle		= varset($options['style'], 'flexbox');
			$dashboardLinks     = varset($options['links'], '');
			$dashboardCaption     = varset($options['caption'], '');

			$ns->setStyle($dashboardStyle);
			$ns->setUniqueId($dashboardUniqueId);

			$coreInfoPanelAdmin = $ns->tablerender($dashboardCaption, $this->renderLatestComments(), $dashboardUniqueId, true);

			//return $this->renderLatestComments(); trying to change core code for now (2x rendered pannel)
			return $coreInfoPanelAdmin;
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
 
			/* LOGIC CHANGE SET EVERYTHING IN TEMPLATE *************************************
			/* TODO FIXME template name - for now trying to avoid to use admin_template   */

			$supported_panels = e107::getTemplate(false, 'dashboard', 'panels');
 
			foreach($supported_panels AS $key => $panel) {
				$key = str_replace('-', '_', $key); // TODO fix this if they solve #4940 different way
 
				$params  = $panel;

				$params['uniqueId'] 	= $key; 
				$params['style'] 		= varset($params['style'], 'flexpanel');
			//	$params['options'] 		= $panel ;
			 
				$panel_type = varset($panel['type'], $key);
				
				$text = e107::callMethod("adminstyle_dashboard", $panel_type, $params);
		 
				if($text) {
				 
					$info = $this->getMenuPosition($key);
	 
					if (!isset($this->positions[$info['area']][$info['weight']]))
					{
						$this->positions[$info['area']][$info['weight']] = '';
					}
					$this->positions[$info['area']][$info['weight']] .= $text;
					
				}
			}
  
			/*  END OF CHANGE **************************************************************/

			// --------------------- Latest Comments --------------------------
			// $this->positions['Area01'] .= $this->renderLatestComments(); // TODO
 
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
					if (!isset($this->positions[$info['area']][$info['weight']]))
					{
						$this->positions[$info['area']][$info['weight']] = '';
					}
					$this->positions[$info['area']][$info['weight']] .= $inc;
				}
			}

 
			// --------------------- Plugin Addon Dashboards ---------------------- eg. e107_plugin/user/e_dashboard.php
			// each plugin renders its own panel - chart function is needed 
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
					if (!isset($this->positions[$info['area']][$info['weight']]))
					{
						$this->positions[$info['area']][$info['weight']] = '';
					}
					$this->positions[$info['area']][$info['weight']] .= $inc;
				}
			}


 
			// Sorting panels.
			foreach ($this->positions as $key => $value)
			{
				ksort($this->positions[$key]);
			}

			$FLEXPANEL_LAYOUT = e107::getCoreTemplate("dashboard", 'layout');
 
			$template = varset($FLEXPANEL_LAYOUT);
			$template = str_replace('{MESSAGES}', $mes->render(), $template);

			foreach ($this->positions as $key => $value)
			{
				$token = '{' . strtoupper(str_replace('-', '_', $key)) . '}';
				$template = str_replace($token, implode("\n", $value), $template);
			}

			echo $template;
			 
		}

}

