<?php namespace SysOmos\SocialMedia\Twitter\Models;

use SysOmos\Bases\Model;
use SysOmos\Interfaces\IModel;
use SysOmos\Models\GenderDistributionModel;

/**
 * Class DemographicsModel
 * @package SysOmos\Models
 */
class DemographicsModel extends Model implements IModel {
		
    /**
     * Gender distribution statistic data
     *
     * @param GenderDistributionModel
     */
	public $GenderDistribution;
		
    /**
     * Country distribution graphic image url
     *
     * @param array
     */
	public $CountryDistribution;
		
    /**
     * US States distribution graphic image url
     *
     * @param array
     */
	public $USStatesDistribution;
		
    /**
     * Canada province distribution graphic image url
     *
     * @param array
     */
	public $CanadaDistribution;
	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->GenderDistribution = new GenderDistributionModel();
		$this->CountryDistribution = array();
		$this->USStatesDistribution = array();
		$this->CanadaDistribution = array();
    }
	
    /**
     * Add new data to list storage
     *
     * @param mixed $value
     * @return self
     */
    public function AddList( $value )
	{
		$key = func_num_args() > 1 ? func_get_arg( 0 ) : "Table";
		$val = func_num_args() > 1 ? func_get_arg( 1 ) : $value;
		
		if( ! is_array( $this->{$key} ) ){
			return FALSE;
		}
		
		$this->{$key}[] = $this->PrepareData( $val );
	}
}
