<?php

namespace NetherTestSuite\Common\Filters;

use Nether\Common\Filters;
use Nether\Common\Datafilter;
use Nether\Common\Struct\DatafilterItem;
use Nether\Common\UUID;
use Exception;
use PHPUnit\Framework\TestCase;

class LinksTest
extends TestCase {

	/** @test */
	public function
	TestFilterWebsiteURL():
	void {

		$Dataset = [
			'https://pegasusgate.net' => 'https://pegasusgate.net',
			'http://pegasusgate.net'  => 'http://pegasusgate.net',
			'pegasusgate.net'         => 'http://pegasusgate.net'
		];

		$In = NULL;
		$Out = NULL;

		foreach($Dataset as $In => $Out)
		$this->AssertEquals($Out, Filters\Links::WebsiteURL($In));

		return;
	}

	/** @test */
	public function
	TestFilterFacebookURL():
	void {

		$Dataset = [
			'https://facebook.com/facebook' => 'https://facebook.com/facebook',
			'facebook'                      => 'https://facebook.com/facebook',
			'facebook.com/facebook'         => 'http://facebook.com/facebook'
		];

		$In = NULL;
		$Out = NULL;

		foreach($Dataset as $In => $Out)
		$this->AssertEquals($Out, Filters\Links::FacebookURL($In));

		return;
	}

	/** @test */
	public function
	TestFilterTwitterURL():
	void {

		$Dataset = [
			'https://twitter.com/twitter' => 'https://twitter.com/twitter',
			'twitter'                     => 'https://twitter.com/twitter',
			'@twitter'                    => 'https://twitter.com/twitter',
			'twitter.com/twitter'         => 'http://twitter.com/twitter'
		];

		$In = NULL;
		$Out = NULL;

		foreach($Dataset as $In => $Out)
		$this->AssertEquals($Out, Filters\Links::TwitterURL($In));

		return;
	}

	/** @test */
	public function
	TestFilterInstagramURL():
	void {

		$Dataset = [
			'https://instagram.com/instagram' => 'https://instagram.com/instagram',
			'instagram'                       => 'https://instagram.com/instagram',
			'@instagram'                      => 'https://instagram.com/instagram',
			'instagram.com/instagram'         => 'http://instagram.com/instagram'
		];

		$In = NULL;
		$Out = NULL;

		foreach($Dataset as $In => $Out)
		$this->AssertEquals($Out, Filters\Links::InstagramURL($In));

		return;
	}

	/** @test */
	public function
	TestFilterTikTokURL():
	void {

		$Dataset = [
			'https://tiktok.com/@tiktok' => 'https://tiktok.com/@tiktok',
			'tiktok'                     => 'https://tiktok.com/@tiktok',
			'@tiktok'                    => 'https://tiktok.com/@tiktok',
			'tiktok.com/@tiktok'         => 'http://tiktok.com/@tiktok'
		];

		$In = NULL;
		$Out = NULL;

		foreach($Dataset as $In => $Out)
		$this->AssertEquals($Out, Filters\Links::TikTokURL($In));

		return;
	}

	/** @test */
	public function
	TestFilterYouTubeURL():
	void {

		$Dataset = [
			'https://youtube.com/@youtube' => 'https://youtube.com/@youtube',
			'youtube'                      => 'https://youtube.com/@youtube',
			'@youtube'                     => 'https://youtube.com/@youtube'
		];

		$In = NULL;
		$Out = NULL;

		foreach($Dataset as $In => $Out)
		$this->AssertEquals($Out, Filters\Links::YouTubeURL($In));

		return;
	}

	/** @test */
	public function
	TestFilterLinkedInURL():
	void {

		$Dataset = [
			'https://linkedin.com/linkedin' => 'https://linkedin.com/linkedin',
			'linkedin'                      => 'https://linkedin.com/linkedin',
			'@linkedin'                     => 'https://linkedin.com/linkedin',
			'linkedin.com/linkedin'         => 'http://linkedin.com/linkedin'
		];

		$In = NULL;
		$Out = NULL;

		foreach($Dataset as $In => $Out)
		$this->AssertEquals($Out, Filters\Links::LinkedInURL($In));

		return;
	}

	/** @test */
	public function
	TestFilterThreadsURL():
	void {

		$Dataset = [
			'https://threads.net/@threads' => 'https://threads.net/@threads',
			'threads'                      => 'https://threads.net/@threads',
			'@threads'                     => 'https://threads.net/@threads',
			'threads.net/@threads'         => 'http://threads.net/@threads'
		];

		$In = NULL;
		$Out = NULL;

		foreach($Dataset as $In => $Out)
		$this->AssertEquals($Out, Filters\Links::ThreadsURL($In));

		return;
	}

}
