<?php
/*
+ ----------------------------------------------------------------------------+
|
| e107 website system
| Copyright (C) 2008-2016 e107 Inc (e107.org)
| Licensed under GNU GPL (http://www.gnu.org/licenses/gpl.txt)
|
| Default layout for "flexpanel" admin dashboard style.
+ ----------------------------------------------------------------------------+
*/

$DASHBOARD_TEMPLATE['layout'] = '
<style> .row-flex .panel {
    height: auto;
}
</style>
<div class="row">
	 
	<div class="col-md-12  admin-right-panel">
		<div class="sidebar-toggle">
			<a href="#" title="'.ADLAN_185.'" data-toggle-sidebar="true">&nbsp;</a>
		</div>

		<div>
			<div class="row">
				<div class="col-sm-12">
					{MESSAGES}
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12">
					<div class="draggable-panels" id="menu-area-02">
						{MENU_AREA_02}
					</div>
				</div>
			</div>

			<div class="row row-flex">
				<div class="col-sm-4">
					<div class="draggable-panels" id="menu-area-03">
						{MENU_AREA_03}
					</div>
				</div>

				<div class="col-sm-4">
					<div class="draggable-panels" id="menu-area-04">
						{MENU_AREA_04}
					</div>
				</div>

				<div class="col-sm-4">
					<div class="draggable-panels" id="menu-area-05">
						{MENU_AREA_05}
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12">
					<div class="draggable-panels" id="menu-area-06">
						{MENU_AREA_06}
					</div>
				</div>
			</div>

			<div class="row row-flex">
				<div class="col-sm-6">
					<div class="draggable-panels" id="menu-area-07">
						{MENU_AREA_07}
					</div>
				</div>

				<div class="col-sm-6">
					<div class="draggable-panels" id="menu-area-08">
						{MENU_AREA_08}
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12">
					<div class="draggable-panels" id="menu-area-09">
						{MENU_AREA_09}
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-4">
					<div class="draggable-panels" id="menu-area-10">
						{MENU_AREA_10}
					</div>
				</div>

				<div class="col-sm-4">
					<div class="draggable-panels" id="menu-area-11">
						{MENU_AREA_11}
					</div>
				</div>

				<div class="col-sm-4">
					<div class="draggable-panels" id="menu-area-12">
						{MENU_AREA_12}
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12">
					<div class="draggable-panels" id="menu-area-13">
						{MENU_AREA_13}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
';


//$DASHBOARD_TEMPLATE['positions']['menu-area-01'] = array();  // Sidebar. use admin_template with shortcodes 
$DASHBOARD_TEMPLATE['positions']['menu-area-02'] = array();
$DASHBOARD_TEMPLATE['positions']['menu-area-03'] = array();
$DASHBOARD_TEMPLATE['positions']['menu-area-04'] = array();
$DASHBOARD_TEMPLATE['positions']['menu-area-05'] = array();
$DASHBOARD_TEMPLATE['positions']['menu-area-06'] = array();
$DASHBOARD_TEMPLATE['positions']['menu-area-07'] = array();	// Content left.
$DASHBOARD_TEMPLATE['positions']['menu-area-08'] = array();	// Content right.
$DASHBOARD_TEMPLATE['positions']['menu-area-09'] = array();
$DASHBOARD_TEMPLATE['positions']['menu-area-10'] = array();
$DASHBOARD_TEMPLATE['positions']['menu-area-11'] = array();
$DASHBOARD_TEMPLATE['positions']['menu-area-12'] = array();
$DASHBOARD_TEMPLATE['positions']['menu-area-13'] = array();


$DASHBOARD_TEMPLATE['panels']['core-infopanel-admin'] = array();
$DASHBOARD_TEMPLATE['panels']['core-infopanel-mye107'] = array();
$DASHBOARD_TEMPLATE['panels']['core-infopanel-news'] = array();
 

/* available dashboard panels
core-infopanel-mye107
core-infopanel-news
plug-infopanel-user-0
*/