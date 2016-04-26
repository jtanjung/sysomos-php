<?php namespace SysOmos\SocialMedia\Twitter\Models;

use SysOmos\Bases\Model;

/**
 * Class UserDetailsModel
 * @package SysOmos\Models
 */
class UserDetailsModel extends Model {
		
    /**
     * Twitter profile url
     *
     * @param string
     */
	public $Url;
		
    /**
     * Twitter Screen name
     *
     * @param string
     */
	public $ScreenName;
		
    /**
     * Twitter full name
     *
     * @param string
     */
	public $FullName;
		
    /**
     * Location
     *
     * @param string
     */
	public $Location;
		
    /**
     * Twitter about
     *
     * @param string
     */
	public $About;
		
    /**
     * Website url
     *
     * @param string
     */
	public $Website;
		
    /**
     * Number of account that user has follow
     *
     * @param int
     */
	public $FollowingCount;
		
    /**
     * Number of follower account
     *
     * @param int
     */
	public $FollowerCount;
		
    /**
     * Authority quantity
     *
     * @param int
     */
	public $Authority;
	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
    }
}
