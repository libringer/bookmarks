<?php
/**
 * Created by PhpStorm.
 * User: MaxTsykarev
 * Date: 27.10.2020
 * Time: 19:20
 */
namespace Libringer\Bookmarks;

class Grab
{
	public function load(string $url = '')
	{
		if (function_exists('curl_init')) {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$content = curl_exec($ch);
			curl_close($ch);
			unset($ch);
		} else {
			if (!function_exists('file_get_contents')) {
				$fh = fopen($url, 'r', FALSE);
				$content = '';
				while (!feof($fh)) {
					$content .= fread($fh, 128);
				}
				fclose($fh);
			} else {
				$content = file_get_contents($url, NULL);
			}
		}
		return $content;
	}
}