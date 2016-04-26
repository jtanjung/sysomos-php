<?php namespace SysOmos\Modules\SocialMedia\Twitter;

use SysOmos\Bases\Module;
use SysOmos\Interfaces\IModule;
use SysOmos\Models\SocialMedia\Twitter\UserDetailsModel;

/**
 * Class UserDetails
 * @package SysOmos\Modules
 */
class UserDetails extends Module implements IModule {
		
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->end_point_path = "twitter_user_details";
    }

    /**
     * Extarct response into model structure
     *
     * @return boolean
     */
    public function DoExtract()
	{		
		preg_match( "/<p>No Twitter user /", $this->response_text, $is_not_found );		
		if( $is_not_found ){
			return FALSE;
		}
		
		$this->data = new UserDetailsModel();
		
		$this->data->Url = $this->ExtractText( "(.*?)", '<div style="float: right"><a href="', '">', $this->response_text );
		$this->data->ScreenName = $this->ExtractText( "(.*?)", '<input type="text" name="u" value="@', '"', $this->response_text );
		$this->data->FullName = $this->ExtractText( "(.*?)", '<h2>@' . $this->data->ScreenName . ' \(', '\)<\/h2>', $this->response_text );
		$this->data->FollowingCount = (int)$this->ExtractText( "(.*?)", ' style="padding-left: 65px;">', ' Following, ', $this->response_text );
		$this->data->FollowerCount = (int)$this->ExtractText( "(.*?)", ' Following, ', ' Followers. ', $this->response_text );
		$this->data->Authority = (int)$this->ExtractText( "(.*?)", '. Authority ', '<img src=', $this->response_text );
		
		$twitter_info = $this->ExtractText( "(.*?)", 'style="margin-right: 5px;">', '<div class="clear_me"><\/div><\/div>', $this->response_text );
		$this->data->Location = $this->ExtractText( "(.*?)", '<br \/>', '<a href=', $twitter_info );
		if( ! $this->data->Location ){
			$this->data->Location = $this->ExtractText( "(.*?)", '<br \/>', '<br \/>', $twitter_info );
		}
		
		$this->data->Website = $this->ExtractText( "(.*?)", '<a href="', '" target="_blank">', $twitter_info );
		if( $this->data->Website ){
			$this->data->About = $this->ExtractText( "(.*?)", '<\/a><br \/>', '<\/div>', $twitter_info );
		}
		elseif( $this->data->Location ){
			$this->data->About = $this->ExtractText( "(.*?)", '<br \/>' . $this->data->Location, '<\/div>', $twitter_info );
		}
		else
		{
			$this->data->About = $this->ExtractText( "(.*?)", '<br \/>', '<\/div>', $twitter_info );
		}
		
		$this->data->About = str_replace( '<br />', ' ', $this->data->About );
		
		return TRUE;
	}
}
