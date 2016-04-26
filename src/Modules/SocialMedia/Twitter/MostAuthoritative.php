<?php namespace SysOmos\Modules\SocialMedia\Twitter;

use SysOmos\Bases\Module;
use SysOmos\Interfaces\IModule;
use SysOmos\Models\SocialMedia\Twitter\MostAuthoritativeModel;

/**
 * Class MostAuthoritative
 * @package SysOmos\Modules
 */
class MostAuthoritative extends Module implements IModule {
		
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->end_point_path = "twitter_search_influence";
    }

    /**
     * Extarct response into model structure
     *
     * @return boolean
     */
    public function DoExtract()
	{
		preg_match( "/No results found for your query/", $this->response_text, $is_empty );
		if( $is_empty ){
			return FALSE;
		}
		
		$this->data = new MostAuthoritativeModel();
		
		$blocks = $this->ExtractTextAll( "(.*?)", '<div class="twitter_auth_wrapper"', '<div style="clear:both;height:0px;line-height:0px;"><\/div>', $this->response_text );
		foreach( $blocks as $block ){
			$screen_name = $this->ExtractText( "(.*?)", '<span class="tw_user">', '<\/span>', $block );
			$screen_name = $this->ExtractText( "(.*?)", '">@', '<\/a>', $screen_name );

			$bio_text = $this->ExtractText( "(.*?)", '<div class="twauth_profile">', '<\/div><\/div>', $block );
			if( ! $bio_text ){
				$bio_text = $this->ExtractText( "(.*?)", '<div class="twauth_profile">', '<\/div>', $block );
			}
			
			$link = $this->ExtractText( "(.*?)", '<a href="', '"', $bio_text );
			if( $link ){
				$bio_text = $this->ExtractText( "(.*?)", '<div class="twauth_profile">', '<br \/><div style="margin-top:3px;font-size:13px;">', $block );
			}
			
			$authority = $this->ExtractText( "(.*?)", '<div class="twauth_auth">', '<br \/>', $block );
			$authority = $this->ExtractText( "(.*?)", '<b>', '<\/b>', $authority );

			$tweet = $this->ExtractText( "(.*?)", 'div class="twauth_tweet">', '<br \/>', $block );
			$tweet = preg_replace( '/<[^>]+>/', '', $tweet );

			$this->data->AddList(
				array(
					"ScreenName" => $screen_name,
					"FullName" => $this->ExtractText( "(.*?)", '<span class="twauth_name">', '<\/span>', $block ),
					"Location" => $this->ExtractText( "(.*?)", '<span class="twloc">', '<\/span>', $block ),
					"Link" => $link,
					"Bio" => $bio_text,
					"Follower" => $this->ExtractText( "(.*?)", '<span class="twauth_num_followers">', '<\/span><br \/>Followers', $block ),
					"Authority" => (int)$authority,
					"Tweet" => $tweet,
					"Time" => $this->ExtractText( "(.*?)", 'style="text-decoration:none;float:left;">', ' <span class="link_image"><\/span>', $block )
				)
			);
		}
		
		return TRUE;
	}
}
