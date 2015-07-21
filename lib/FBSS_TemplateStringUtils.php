<?php

class FBSS_TemplateStringUtils {
	
	/* combination of string util methods */
	public static function createMessageHTML($txt) {
		$txt = self::createHTMLLinks($txt);
		$txt = self::createHashtagLinks($txt);
		$txt = self::createHTMLLineBreaks($txt);
		
		return $txt;
	}
	
	
	public static function createHTMLLinks($txt) {
		$search = '/(https?:\/\/(.+?))(\s|$|\n)/i';
		$replace = '<a href="$1" rel="nofollow" target="_blank">$1</a> ';
		return preg_replace($search, $replace, $txt);
	}
	
	public static function createHashtagLinks($txt) {
		$search = '/([^&]#(.+?))(\s|$|\n|,|\.)/i';
		$replace = ' <a href="https://www.facebook.com/hashtag/$2" rel="nofollow" target="_blank">#$2</a> ';
		return preg_replace($search, $replace, $txt);
	}

	public static function createHTMLLineBreaks($txt) {
		return preg_replace('/\n/', '<br />', $txt);
	}
	
	public static function getLocalTimestamp($timestamp) {
		$gmt_offset = get_option('gmt_offset', 0);
		$gmt_offset_string = sprintf("%s hours", $gmt_offset);
		if (preg_match('/^(-?\d+)\.5/', $gmt_offset, $result)) {
			$gmt_offset_string = sprintf("%s hours 30 minutes", $result[1]);
		}
		
		return strtotime($gmt_offset_string, $timestamp);
	}
}
