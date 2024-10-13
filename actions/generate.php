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
$sw = input('service_worker');

if($sw == 'enabled') {
		//copied from original sw
		$siteurl = ossn_site_url();
		if(substr($siteurl, 0, 8) != 'https://') {
				ossn_trigger_message(ossn_print('favicon:generator:sw:https:error'), 'error');
				redirect(REF);
		}
}
$logo = new OssnFile();
$logo->setFile('logo');
$logo->setExtension(array(
		'jpg',
		'png',
		'jpeg',
		'jfif',
		'gif',
		'webp',
));
if(isset($logo->file['tmp_name']) && $logo->typeAllowed()) {
		$file = $logo->file['tmp_name'];

		$info   = getimagesize($file);
		$width  = $info[0];
		$height = $info[1];

		if($width != $height) {
				ossn_trigger_message(ossn_print('favicon:generator:logo:dim:error'), 'error');
				redirect(REF);
		}

		$time = time();

		$icon = new Favicon\Generator($file, $time);
		if($sw == 'enabled') {
				$icon->serviceworker = true;
		}
		$icon->generate();

		ossn_trigger_message(ossn_print('favicon:generator:logo:done'));
		redirect(REF);
} else {
		ossn_trigger_message(ossn_print('favicon:generator:logo:error'), 'error');
		redirect(REF);
}