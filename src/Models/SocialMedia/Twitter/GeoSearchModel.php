<?php namespace SysOmos\SocialMedia\Twitter\Models;

use SysOmos\Bases\Model;
use SysOmos\Interfaces\IModel;

/**
 * Class GeoSearchModel
 * @package SysOmos\Models
 */
class GeoSearchModel extends Model implements IModel {
				
    /**
     * World large map coordinate pointer
     *
     * @param array
     */
	public $LargeMapPoints;
		
    /**
     * List chart data
     *
     * @param array
     */
	public $Chart;
	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->LargeMapPoints = array();
		$this->Chart = array();
		$this->AddAsset( "LargeMap", "http://map.sysomos.com/img/geo/worldmap4.jpg" );
    }
	
    /**
     * Add new data to list storage
     *
     * @param mixed $value
     * @return self
     */
    public function AddList( $value )
	{
		$key = func_num_args() > 1 ? func_get_arg( 0 ) : "LargeMapPoints";
		$val = func_num_args() > 1 ? func_get_arg( 1 ) : $value;
		$this->{$key}[] = $this->PrepareData( $val );
	}
}
