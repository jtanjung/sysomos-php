<?php namespace SysOmos\Modules\SocialMedia\Twitter;

use SysOmos\Bases\Module;
use SysOmos\Interfaces\IModule;
use SysOmos\Models\SocialMedia\Twitter\MostRetweetedModel;

/**
 * Class MostRetweeted
 * @package SysOmos\Modules
 */
class MostRetweeted extends Module implements IModule {
		
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->end_point_path = "twitter_most_retweeted";
    }

    /**
     * Extarct response into model structure
     *
     * @return boolean
     */
    public function DoExtract()
	{
		preg_match( "/No re-tweeted tweets found/", $this->response_text, $is_empty );
		if( $is_empty ){
			return FALSE;
		}
		
		$this->data = new MostRetweetedModel();
		
		$tweets = $this->ExtractTextAll( '(.*?)', '<div class="res_g_cont_frame retweet_item_w">', '<br><br><br><\/div>', $this->response_text );
		if( $tweets ){
			foreach( $tweets as $tweet ){
				$tweet_text = $this->ExtractText( '(.*?)', '<div style="overflow:hidden;" >', '<\/span><\/div><div class="retweet_date"', $tweet );
				$tweet_text = preg_replace( '/<span class="twitter_profile_popup"(.*?)<div style="clear:both"><\/div><\/div><\/div><\/span>/', '', $tweet_text );
				$tweet_text = str_replace( '<span class="twitter_profile_popup"', '', $tweet_text );
				$tweet_text = str_replace( '<div style="clear:both"></div></div></div></span>', '', $tweet_text );
				
				$tweet_text = preg_replace( '/<[^>]+>/', '', $tweet_text );
				$tweet_text = trim( $tweet_text );
				
				$tweet_date = $this->ExtractText( '(.*?)', ' style="font-weight:bold;text-decoration:none;">', ' by ', $tweet );
				$tweet_user = $this->ExtractText( '(.*?)', ' by ', '<span class="link_image">', $tweet );
				$retweet_number = $this->ExtractText( '(.*?)', '<span style="font-size:24px;float:left;margin:1px 5px 0px 0px;">', '<\/span>', $tweet );
				
				$retweets = $this->ExtractTextAll( '(.*?)', '<span class="twitter_profile_popup"', '<div style="clear:both"><\/div>', $tweet );
				$users = array();
				foreach( $retweets as $retweet ){
					$users[] = array(
						"ScreenName" => ltrim( $this->ExtractText( '(.*?)', '<b>', '<\/b>', $retweet ), "@" ),
						"FullName" => $this->ExtractText( '(.*?)', '<span class="tpp_name">', '<\/span>', $retweet ),
						"Follower" => (int)trim( $this->ExtractText( '(.*?)', '<br \/>', ' followers<br\/>', $retweet ) ),
						"Location" => $this->ExtractText( '(.*?)', ' followers<br\/>', '<\/div>', $retweet ),
					);
				}
				$this->data->AddList(
					array(
						"Tweet" => $tweet_text,
						"Date" => $tweet_date,
						"TwitterName" => $tweet_user,
						"Retweeter" => $users,
						"Retweet" => $retweet_number
					)
				);
			}
		}
		
		return TRUE;
	}
}
