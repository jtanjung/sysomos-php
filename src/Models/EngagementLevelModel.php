<?php namespace SysOmos\Models;

/**
 * Class EngagementLevelModel
 * @package SysOmos\Models
 */
class EngagementLevelModel {
		
    /**
     * Percentage of single ( just 1 ) tweet
     *
     * @param float
     */
	public $Single = 0;
		
    /**
     * Percentage of tweets count between 2 - 4 tweets
     *
     * @param float
     */
	public $Few = 0;
		
    /**
     * Percentage of tweets count between 5 - 7 tweets
     *
     * @param float
     */
	public $Average = 0;
		
    /**
     * Percentage of tweets count more than 7 tweets
     *
     * @param float
     */
	public $Large = 0;

	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
    }
}
