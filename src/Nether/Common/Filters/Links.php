<?php

namespace Nether\Common\Filters;

use Nether\Common;

class Links
extends Common\Datafilters {

	static public function
	FacebookURL($Val):
	string {

		Common\Datafilters::Prepare($Val);

		$Val = trim(strip_tags($Val));
		if(!$Val) return '';

		if(strpos($Val, '/') === FALSE)
		return sprintf(
			'https://facebook.com/%s',
			ltrim($Val, '@')
		);

		return Common\Datafilters::WebsiteURL($Val);
	}

	static public function
	InstagramURL($Val):
	string {

		Common\Datafilters::Prepare($Val);

		$Val = trim(strip_tags($Val));
		if(!$Val) return '';

		if(strpos($Val, '/') === FALSE)
		return sprintf(
			'https://instagram.com/%s',
			ltrim($Val, '@')
		);

		return Common\Datafilters::WebsiteURL($Val);
	}

	static public function
	LinkedInURL($Val):
	string {

		Common\Datafilters::Prepare($Val);

		$Val = trim(strip_tags($Val));
		if(!$Val) return '';

		if(strpos($Val, '/') === FALSE)
		return sprintf(
			'https://linkedin.com/%s',
			ltrim($Val, '@')
		);

		return Common\Datafilters::WebsiteURL($Val);
	}

	static public function
	TikTokURL($Val):
	string {

		Common\Datafilters::Prepare($Val);

		$Val = trim(strip_tags($Val));
		if(!$Val) return '';

		if(strpos($Val, '/') === FALSE) {
			if(!str_starts_with($Val, '@'))
			$Val = "@{$Val}";

			return sprintf(
				'https://tiktok.com/%s',
				$Val
			);
		}

		return Common\Datafilters::WebsiteURL($Val);
	}

	static public function
	TwitterURL($Val):
	string {

		Common\Datafilters::Prepare($Val);

		$Val = trim(strip_tags($Val));
		if(!$Val) return '';

		if(strpos($Val, '/') === FALSE)
		return sprintf(
			'https://twitter.com/%s',
			ltrim($Val, '@')
		);

		return Common\Datafilters::WebsiteURL($Val);
	}

	static public function
	WebsiteURL($Val):
	string {

		Common\Datafilters::Prepare($Val);

		$Val = trim(strip_tags($Val));
		if(!$Val) return '';

		if(!preg_match('/^https?:\/\//', $Val))
		return "http://{$Val}";

		return $Val;
	}

	static public function
	YouTubeURL($Val):
	string {

		Common\Datafilters::Prepare($Val);

		$Val = trim(strip_tags($Val));
		if(!$Val) return '';

		if(strpos($Val, '/') === FALSE)
		return sprintf(
			'https://youtube.com/channel/%s',
			ltrim($Val, '@')
		);

		return Common\Datafilters::WebsiteURL($Val);
	}

}