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

}

