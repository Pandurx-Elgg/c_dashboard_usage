
<?php

require_once(dirname(__FILE__) . "/lib/functions.php");
require_once(dirname(__FILE__) . "/lib/events.php");
require_once(dirname(__FILE__) . "/lib/pages.php");

elgg_register_event_handler('init', 'system', 'c_dashboard_usage_init');
elgg_register_event_handler('pagesetup', 'system', 'c_dashboard_usage_pagesetup');

function c_dashboard_usage_init() {
	elgg_register_event_handler("create", "object", "c_dashboard_usage_create_object_event_handler");
	elgg_register_event_handler("update", "object", "c_dashboard_usage_update_object_event_handler");
	elgg_register_event_handler("delete", "object", "c_dashboard_usage_delete_object_event_handler");

	elgg_register_admin_menu_item('administer', 'disk_space_usage', 'administer_utilities');

	$vendor_url = elgg_get_plugins_path().'c_dashboard_usage/vendors/';
	elgg_register_library('jgraph',$vendor_url.'jpgraph/src/jpgraph.php');
	elgg_register_library('jgraph_pie',$vendor_url.'jpgraph/src/jpgraph_pie.php');
	elgg_register_library('jgraph_bar',$vendor_url.'jpgraph/src/jpgraph_bar.php');

	elgg_register_css('collap_tree', '/mod/c_dashboard_usage/css/_styles.css');
	//elgg_load_css('collap_tree');

	elgg_load_library('jgraph');
	elgg_load_library('jgraph_pie');
	elgg_load_library('jgraph_bar');

	elgg_register_page_handler('groups', 'c_dashboard_usage_pagehandler');
}

function c_dashboard_usage_pagesetup() {
	if (!elgg_in_context('group_profile')) {
		return true;
	}

	$page_owner = elgg_get_page_owner_entity();
	if ($page_owner instanceof ElggGroup && $page_owner->canEdit()) {
		elgg_register_menu_item('title', array(
			'name' => 'disk_usage',
			'text' => elgg_echo('admin:administer_utilities:disk_space_usage'),
			'class' => 'elgg-button elgg-button-action',
			'href' => elgg_get_site_url() . "groups/disk-usage/{$page_owner->guid}"
		));
	}
}

function c_dashboard_usage_pagehandler($page) {
	if ($page[0] == "disk-usage") {
		c_dashboard_usage_group_page($page[1]);
		return true;
	}

	return groups_page_handler($page);
}