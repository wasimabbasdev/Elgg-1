<?php
/**
 * Elgg Reported content.
 *
 * @package ElggReportedContent
 */

/**
 * Initialize the plugin
 *
 * @return void
 */
function reportedcontent_init() {

	// Register a page handler, so we can have nice URLs
	elgg_register_page_handler('reportedcontent', 'reportedcontent_page_handler');

	// Extend CSS
	elgg_extend_view('elgg.css', 'reportedcontent/css');
	elgg_extend_view('admin.css', 'reportedcontent/admin_css');


	if (elgg_is_logged_in()) {
		// Extend footer with report content link
		elgg_register_menu_item('footer', [
			'name' => 'report_this',
			'href' => 'reportedcontent/add',
			'title' => elgg_echo('reportedcontent:this:tooltip'),
			'text' => elgg_echo('reportedcontent:this'),
			'icon' => 'exclamation-triangle',
			'priority' => 500,
			'section' => 'default',
			'link_class' => 'elgg-lightbox',
			'deps' => 'elgg/reportedcontent',
		]);
	}

	elgg_register_plugin_hook_handler('register', 'menu:user_hover', 'reportedcontent_user_hover_menu');

	elgg_register_menu_item('page', [
		'name' => 'administer_utilities:reportedcontent',
		'text' => elgg_echo('admin:administer_utilities:reportedcontent'),
		'href' => 'admin/administer_utilities/reportedcontent',
		'section' => 'administer',
		'parent_name' => 'administer_utilities',
		'context' => 'admin',
	]);
}

/**
 * Reported content page handler
 *
 * Serves the add report page
 *
 * @param array $page Array of page routing elements
 * @return bool
 */
function reportedcontent_page_handler($page) {
	// only logged in users can report things
	elgg_gatekeeper();

	if (elgg_extract(0, $page) === 'add' && elgg_is_xhr()) {
		echo elgg_view_resource('reportedcontent/add_form');
		return true;
	}

	echo elgg_view_resource('reportedcontent/add');
	return true;
}

/**
 * Add report user link to hover menu
 *
 * @param string         $hook   'register'
 * @param string         $type   'menu:user_hover'
 * @param ElggMenuItem[] $return current return value
 * @param array          $params supplied params
 *
 * @return void|ElggMenuItem[]
 */
function reportedcontent_user_hover_menu($hook, $type, $return, $params) {
	
	if (!elgg_is_logged_in()) {
		return;
	}
	
	$user = elgg_extract('entity', $params);
	if (!$user instanceof ElggUser) {
		return;
	}
	
	if (elgg_get_logged_in_user_guid() == $user->guid) {
		return;
	}
	
	$href = elgg_http_add_url_query_elements('reportedcontent/add', [
		'address' => $user->getURL(),
		'title' => $user->name,
	]);
	
	$return[] = \ElggMenuItem::factory([
		'name' => 'reportuser',
		'text' => elgg_echo('reportedcontent:user'),
		'icon' => 'exclamation-triangle',
		'href' => $href,
		'section' => 'action',
		'link_class' => 'elgg-lightbox',
		'deps' => 'elgg/reportedcontent',
	]);

	return $return;
}

return function() {
	elgg_register_event_handler('init', 'system', 'reportedcontent_init');
};
