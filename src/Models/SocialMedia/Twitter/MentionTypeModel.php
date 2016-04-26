<?php namespace SysOmos\SocialMedia\Twitter\Models;

/**
 * Class MentionTypeModel
 * @package SysOmos\Models
 */
class MentionTypeModel {
		
    /**
     * Number of retweets in percentage
     *
     * @param float
     */
	public $Retweets = 0;
		
    /**
     * Number of regular tweet in percentage
     *
     * @param float
     */
	public $RegularTweet = 0;

    /**
     * Number of reply in percentage
     *
     * @param float
     */
	public $Reply = 0;
	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
    }
}
