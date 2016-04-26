<?php namespace SysOmos\SocialMedia\Twitter\Models;

use SysOmos\Bases\Model;
use SysOmos\Interfaces\IModel;
use SysOmos\Models\GenderDistributionModel;

/**
 * Class TwitterPopularityModel
 * @package SysOmos\Models
 */
class TwitterPopularityModel extends Model implements IModel {
			
    /**
     * Gender distribution statistic data
     *
     * @param GenderDistributionModel
     */
	public $GenderDistribution;
	
    /**
     * Statistic data by region
     *
     * @param array
     */
	public $Regions;
	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->GenderDistribution = new GenderDistributionModel();
		$this->Regions = array();
    }
    /**
     * Add new data to list storage
     *
     * @param mixed $value
     * @return self
     */
    public function AddList( $value )
	{
		$this->Regions[] = $this->PrepareData( $value );
	}
}
