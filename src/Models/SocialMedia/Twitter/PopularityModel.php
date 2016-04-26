<?php namespace SysOmos\SocialMedia\Twitter\Models;

use SysOmos\Bases\Model;
use SysOmos\Interfaces\IModel;

/**
 * Class PopularityModel
 * @package SysOmos\Models
 */
class PopularityModel extends Model implements IModel {
		
    /**
     * List of popularity chart data
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
		$this->Chart = array();
    }
    /**
     * Add new data to list storage
     *
     * @param mixed $value
     * @return self
     */
    public function AddList( $value )
	{
		$this->Chart[] = $this->PrepareData( $value );
	}
}
