<?php
/**
 * Open Source Social Network
 *
 * @package   Open Source Social Network
 * @author    OSSN Core Team <info@openteknik.com>
 * @copyright (C) Engr. Arsalan Shah
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */

define('__FaviconGenerator__', ossn_route()->com . 'FaviconGenerator/');
ossn_register_class(array(
		'Favicon\Generator' => __FaviconGenerator__ . 'classes/Generator.php',
));
ossn_register_callback('ossn', 'init', function () {
		if(ossn_isAdminLoggedin()) {
				ossn_register_com_panel('FaviconGenerator', 'settings');
				ossn_register_action('favicon_generator/settings', __FaviconGenerator__ . 'actions/generate.php');
				ossn_register_action('favicon_generator/reset', __FaviconGenerator__ . 'actions/reset.php');
		}
		//taken from original favicons component
		ossn_add_hook('page', 'override:view', '_com_favicons_page_handler');
		ossn_extend_view('ossn/admin/head', '_com_favicons_add_head_tags');

		//from original service worker
		ossn_extend_view('ossn/site/head', '_com_serviceworker_enable_service_worker');
		ossn_extend_view('ossn/admin/head', '_com_serviceworker_enable_service_worker');
});
function _com_serviceworker_enable_service_worker() {
		$sw_file = ossn_route()->www . 'sfavicons/sw.js';
		$sw_url  = ossn_site_url() . 'sfavicons/sw.js';
		if(file_exists($sw_file)) {
				$script =
						"\n<script>if('serviceWorker' in navigator) { navigator.serviceWorker.register(\"" . $sw_url . "\").then(function() { /* console.log('Service Worker Registered'); */ });}</script>\n";
				return $script;
		}
}
function _com_favicons_page_handler($hook, $type, $return, $params) {
		if($params['handler'] != 'shared_content') {
				ossn_extend_view('ossn/site/head', '_com_favicons_add_head_tags');
		}
}

function _com_favicons_add_head_tags() {
		$tags_file = ossn_route()->www . 'sfavicons/head.html';
		if(file_exists($tags_file)) {
				$content = file_get_contents($tags_file, true);
				$tags    = "\n" . $content . "\n";
				return $tags;
		}
}