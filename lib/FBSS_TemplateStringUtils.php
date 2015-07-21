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
}
