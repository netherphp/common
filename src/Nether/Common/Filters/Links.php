<?php

namespace Nether\Common\Filters;

use Nether\Common;

#[Common\Meta\DateAdded('2023-07-07')]
class Links {

	use
	Common\Package\DatafilterPackage;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FacebookURL($Val):
	string {

		static::Prepare($Val);

		$Val = trim(strip_tags($Val));
		if(!$Val) return '';

		if(strpos($Val, '/') === FALSE)
		return sprintf(
			'https://facebook.com/%s',
			ltrim($Val, '@')
		);

		return static::WebsiteURL($Val);
	}

	static public function
	InstagramURL($Val):
	string {

		static::Prepare($Val);

		$Val = trim(strip_tags($Val));
		if(!$Val) return '';

		if(strpos($Val, '/') === FALSE)
		return sprintf(
			'https://instagram.com/%s',
			ltrim($Val, '@')
		);

		return static::WebsiteURL($Val);
	}

	static public function
	LinkedInURL($Val):
	string {

		static::Prepare($Val);

		$Val = trim(strip_tags($Val));
		if(!$Val) return '';

		if(strpos($Val, '/') === FALSE)
		return sprintf(
			'https://linkedin.com/%s',
			ltrim($Val, '@')
		);

		return static::WebsiteURL($Val);
	}

	static public function
	ThreadsURL($Val):
	string {

		static::Prepare($Val);

		$Val = trim(strip_tags($Val));
		if(!$Val) return '';

		if(strpos($Val, '/') === FALSE) {
			if(!str_starts_with($Val, '@'))
			$Val = "@{$Val}";

			return sprintf(
				'https://threads.net/%s',
				$Val
			);
		}

		return static::WebsiteURL($Val);
	}

	static public function
	TikTokURL($Val):
	string {

		static::Prepare($Val);

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

		return static::WebsiteURL($Val);
	}

	static public function
	TwitterURL($Val):
	string {

		static::Prepare($Val);

		$Val = trim(strip_tags($Val));
		if(!$Val) return '';

		if(strpos($Val, '/') === FALSE)
		return sprintf(
			'https://twitter.com/%s',
			ltrim($Val, '@')
		);

		return static::WebsiteURL($Val);
	}

	static public function
	WebsiteURL($Val):
	string {

		static::Prepare($Val);

		$Val = trim(strip_tags($Val));
		if(!$Val) return '';

		if(!preg_match('/^https?:\/\//', $Val))
		return "http://{$Val}";

		return $Val;
	}

	static public function
	YouTubeURL($Val):
	string {

		static::Prepare($Val);

		$Val = trim(strip_tags($Val));
		if(!$Val) return '';

		if(strpos($Val, '/') === FALSE)
		return sprintf(
			'https://youtube.com/@%s',
			ltrim($Val, '@')
		);

		return static::WebsiteURL($Val);
	}

}
