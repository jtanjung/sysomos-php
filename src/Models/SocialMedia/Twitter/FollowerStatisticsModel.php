<?php namespace SysOmos\SocialMedia\Twitter\Models;

use SysOmos\Bases\Model;
use SysOmos\Models\GenderDistributionModel;
use SysOmos\Interfaces\IModel;

/**
 * Class FollowerStatisticsModel
 * @package SysOmos\Models
 */
class FollowerStatisticsModel extends Model implements IModel {
		
    /**
     * Statistic data by region
     *
     * @param array
     */
	public $Regions;
		
    /**
     * Statistic data by gender
     *
     * @param GenderDistributionModel
     */
	public $GenderDistribution;
		
    /**
     * Statistic data by words
     *
     * @param array
     */
	public $WordCloud;
		
    /**
     * Statistic data by follower's authority
     *
     * @param array
     */
	public $FollowersAuthority;
		
    /**
     * Followers avarage authority
     *
     * @param float
     */
	public $AuthorityPercentage;
		
    /**
     * Lsit of high authoritative followers
     *
     * @param array
     */
	public $HighAuthoritativeFollowers;
		
    /**
     * Lsit of medium authoritative followers
     *
     * @param array
     */
	public $MediumAuthoritativeFollowers;
		
    /**
     * Lsit of low authoritative followers
     *
     * @param array
     */
	public $LowAuthoritativeFollowers;
	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->GenderDistribution = new GenderDistributionModel();
		
		$this->Regions = array();
		$this->WordCloud = array();
		$this->FollowersAuthority = array();
		$this->HighAuthoritativeFollowers = array();
		$this->MediumAuthoritativeFollowers = array();
		$this->LowAuthoritativeFollowers = array();
    }
	
    /**
     * Add new data to list storage
     *
     * @param mixed $value
     * @return self
     */
    public function AddList( $value )
	{
		$key = func_num_args() > 1 ? func_get_arg( 0 ) : "Regions";
		$val = func_num_args() > 1 ? func_get_arg( 1 ) : $value;
		$this->{$key}[] = $this->PrepareData( $val );
	}
}
