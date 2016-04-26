<?php namespace SysOmos\Modules\SocialMedia\Twitter;

use SysOmos\Bases\Module;
use SysOmos\Interfaces\IModule;
use SysOmos\Models\SocialMedia\Twitter\TweetsModel;

/**
 * Class Tweets
 * @package SysOmos\Modules
 */
class Tweets extends Module implements IModule {
		
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->end_point_path = "twitter_search_simple";
    }

    /**
     * Get csv url from response text
     *
     * @return array
     */
    public function GetCSVUrl()
	{
		return $this->ExtractText( "(.*?)", "'url': '", "',", $this->response_text );
	}

    /**
     * Execute csv data format
     *
     * @return self
     */
    protected function DoFormatCSV()
	{		
		$this->data->Total = $this->ExtractText( "(.*?)", "<b>", "<\/b> Tweets", file_get_contents( $this->csv_temp_file ) );
		return $this;
	}

    /**
     * Extarct response into model structure
     *
     * @return boolean
     */
    public function DoExtract()
	{
		preg_match( "/No results found/", $this->response_text, $is_empty );
		if( $is_empty ){
			return FALSE;
		}
		
		$this->data = new TweetsModel();		
		
		$tweets = $this->ExtractTextAll( '(.*?)', '<div style="width:700px;height:90px;" class="tweetItem">', '<!-- ending tweet content -->', $this->response_text );
		if( $tweets ){
			foreach( $tweets as $block ){
			
				$tweet = $this->ExtractText( "(.*?)", '<div style="clear:both"><\/div><\/div><\/div><\/span><span >', '<\/span><br>', $block );
				$tweet = preg_replace( '/<[^>]+>/', '', $tweet );
				
				$links = $this->ExtractText( "(.*?)", '<div class="tweet_web_intents">', '<\/div>', $block );
				$links = $this->ExtractTextAll( "(.*?)", '<a href="', '"', $links );
			
				$this->data->AddList(
					array(
						"ScreenName" => $this->ExtractText( '(.*?)', '<span style="font-size:14px;"><b>@', '<\/b>', $block ),
						"FullName" => $this->ExtractText( '(.*?)', '<span class="tpp_name">', '<\/span>', $block ),
						"Follower" => (int)$this->ExtractText( '(.*?)', '<\/span><\/span><br \/>', ' followers', $block ),
						"Location" => $this->ExtractText( '(.*?)', ' followers<br\/>', '<\/div>', $block ),
						"Tweet" => $tweet,
						"Time" => $this->ExtractText( '(.*?)', '<span style="font-size: small; font-weight: bold;" >', '<\/span>', $block ),
						"Links" => array(
							"Reply" => $links[ 0 ],
							"Retweet" => $links[ 1 ],
							"Favorite" => $links[ 2 ]
						)
					)
				);
				
			}
		}
		
		return TRUE;
	}
}
