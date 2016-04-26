<?php namespace SysOmos\Modules\SocialMedia\Twitter;

use SysOmos\Bases\Module;
use SysOmos\Interfaces\IModule;
use SysOmos\Models\SocialMedia\Twitter\ReachModel;

/**
 * Class Reach
 * @package SysOmos\Modules
 */
class Reach extends Module implements IModule {
		
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->end_point_path = "twitter_reach_simple";
    }

    /**
     * Extarct response into model structure
     *
     * @return boolean
     */
    public function DoExtract()
	{
		preg_match( "/None or very few results found/", $this->response_text, $is_empty );
		if( $is_empty ){
			return FALSE;
		}
		
		$this->data = new ReachModel();
		
		$this->data->Impressions = $this->ExtractText( "(.*?)", "<span style=\"font-size: 25px;\">", "<\/span>", $this->response_text );
		$impression_data = $this->ExtractTextAll( "([0-9]+)", "<b>", "<\/b>", $this->response_text );
		$this->data->Mentions = $impression_data ? (int)$impression_data[ 0 ] : 0;
		$this->data->Users = $impression_data ? (int)$impression_data[ 1 ] : 0;
		
		$this->data->AuthorityBreakdown->Low = $this->ExtractText( "(.*?)", "authBreakdownLabel1 = \"", "\"", $this->response_text );
		$this->data->AuthorityBreakdown->Medium = $this->ExtractText( "(.*?)", "authBreakdownLabel0 = \"", "\"", $this->response_text );
		$this->data->AuthorityBreakdown->High = $this->ExtractText( "(.*?)", "authBreakdownLabel2 = \"", "\"", $this->response_text );

		$this->data->MentionType->Retweets = $this->ExtractText( "(.*?)", "mentionTypeLabel0 = \"", "\"", $this->response_text );
		$this->data->MentionType->RegularTweet = $this->ExtractText( "(.*?)", "mentionTypeLabel1 = \"", "\"", $this->response_text );
		$this->data->MentionType->Reply = $this->ExtractText( "(.*?)", "mentionTypeLabel2 = \"", "\"", $this->response_text );

		$this->data->EngagementLevel->Single = $this->ExtractText( "(.*?)", "engagementLevelLabel0 = \"", "\"", $this->response_text );
		$this->data->EngagementLevel->Few = $this->ExtractText( "(.*?)", "engagementLevelLabel1 = \"", "\"", $this->response_text );
		$this->data->EngagementLevel->Average = $this->ExtractText( "(.*?)", "engagementLevelLabel2 = \"", "\"", $this->response_text );
		$this->data->EngagementLevel->Large = $this->ExtractText( "(.*?)", "engagementLevelLabel3 = \"", "\"", $this->response_text );
				
		return TRUE;
	}
}
