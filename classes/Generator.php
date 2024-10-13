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
namespace Favicon;
class Generator {
		public function __construct($file, $time) {
				$this->file          = $file;
				$this->time          = $time;
				$this->serviceworker = false;
		}
		public function browserXML() {
				$file = __FaviconGenerator__ . 'configs/browserconfig.xml.dist';
				$file = file_get_contents($file);

				$urllogo_310 = ossn_site_url("sfavicons/favicon-310x310.png?v{$this->time}");
				$urllogo_512 = ossn_site_url("sfavicons/favicon-512x512.png?v{$this->time}");

				$newfile = str_replace('{512logo_url}', $urllogo_512, $file);
				$newfile = str_replace('{310logo_url}', $urllogo_310, $newfile);
				return $newfile;
		}
		public function webManifest() {
				$urllogo_192 = ossn_site_url("sfavicons/favicon-192x192.png?v{$this->time}");
				$urllogo_512 = ossn_site_url("sfavicons/favicon-512x512.png?v{$this->time}");

				$sitename = ossn_site_settings('site_name');

				$manifest = array(
						'name'             => $sitename,
						'short_name'       => $sitename,
						'icons'            => array(
								array(
										'src'     => $urllogo_192,
										'sizes'   => '192x192',
										'type'    => 'image/png',
										'purpose' => 'any maskable',
								),
								array(
										'src'     => $urllogo_512,
										'sizes'   => '512x512',
										'type'    => 'image/png',
										'purpose' => 'any maskable',
								),
						),

						'theme_color'      => '#ffffff',
						'background_color' => '#ffffff',
						'start_url'        => ossn_site_url(),
						'display'          => 'standalone',
						'orientation'      => 'any',
						'scope'            => ossn_site_url(),
				);
				if(isset($this->serviceworker) && $this->serviceworker === true) {
						$manifest['serviceworker'] = array(
								'src' => ossn_site_url('sfavicons/sw.js'),
						);
				}
				return json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		}
		public function html() {
				return ossn_plugin_view('favicon_generator/html', array(
						'time' => $this->time,
				));
		}
		public function yandex() {
				$manifest = array(
						'version'     => '1.0',
						'api_version' => 1,
						'layout'      => array(
								'logo'       => ossn_site_url("sfavicons/favicon-50x50.png?v{$this->time}"),
								'color'      => 'background_color',
								'show_title' => true,
						),
				);
				return json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		}
		public function ico($png32, $destination) {
				if(!is_file($png32) || !is_dir($destination)) {
						return false;
				}
				require_once __FaviconGenerator__ . 'vendors/png2ico/php2ico.php';
				$icon = new \php2ico($png32);
				return $icon->save_ico($destination . 'favicon.ico');
		}
		public function resize($input_name, $maxwidth, $maxheight) {
				// Get the size information from the image
				$imgsizearray = getimagesize($input_name);
				if($imgsizearray == false) {
						return false;
				}
				$image = new \OssnImage($input_name);

				$accepted_formats = array(
						'image/jpeg'  => 'jpeg',
						'image/pjpeg' => 'jpeg',
						'image/png'   => 'png',
						'image/x-png' => 'png',
						'image/webp'  => 'webp',
				);

				// make sure the function is available
				$load_function = 'imagecreatefrom' . $accepted_formats[$imgsizearray['mime']];
				if(!is_callable($load_function)) {
						return false;
				}
				$image->resizeToBestFit($maxwidth, $maxheight);
				return $image->getImageAsString(IMAGETYPE_PNG, 50);
		}
		public function generate() {
				if(!is_file($this->file)) {
						return false;
				}
				$path = ossn_route()->www . 'sfavicons/';

				//delete old dir and create again
				\OssnFile::DeleteDir($path);
				mkdir($path, 0755, true);

				//png icon files
				$config = array(
						16,
						32,
						37,
						48,
						50,
						57,
						60,
						72,
						76,
						96,
						114,
						120,
						144,
						152,
						180,
						192,
						150,
						310,
						512,
				);
				foreach ($config as $size) {
						$new = $this->resize($this->file, $size, $size, false);
						file_put_contents($path . "favicon-{$size}x{$size}.png", $new);
				}

				//ico file
				//we need to use 32px PNG file
				if(!$this->ico($path . 'favicon-32x32.png', $path)) {
						//failed for some reason
						//delete generated icons
						error_log("FaviconGenerator: Failed to generate ICO file from {$path}");
						\OssnFile::DeleteDir($path);
						return false;
				}

				//webmanifest
				$manifest = $this->webManifest();
				file_put_contents($path . 'manifest.json', $manifest);

				//yandex
				$yandex = $this->yandex();
				file_put_contents($path . 'yandex-browser-manifest.json', $yandex);

				//browserxml
				$browserxml = $this->browserXML();
				file_put_contents($path . 'browserconfig.xml', $browserxml);

				//header
				$html = $this->html();
				file_put_contents($path . 'head.html', $html);

				//service worker
				if(isset($this->serviceworker) && $this->serviceworker === true) {
						$sw = ossn_plugin_view('favicon_generator/favicon-sw');
						file_put_contents($path . 'sw.js', $sw);
				}
				
				//generated settings
				file_put_contents($path . 'generated', 1);
		}
}