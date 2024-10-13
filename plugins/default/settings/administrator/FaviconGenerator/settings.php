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
echo ossn_view_form('favicon_generator/admin', array(
    	'action' => ossn_site_url() . 'action/favicon_generator/settings',
		'class' => 'favicon-generator-form-admin',
));		