<?php namespace SysOmos\SocialMedia\Twitter\Models;

use SysOmos\Bases\Model;
use SysOmos\Interfaces\IModel;

/**
 * Class FollowerGrowthModel
 * @package SysOmos\Models
 */
class FollowerGrowthModel extends Model implements IModel {
		
    /**
     * Chart data
     *
     * @param array
     */
	public $Chart;
	
    /**
     * List of dates with most followers added
     *
     * @param array
     */
	public $BestDates;
		
    /**
     * Number of new follower per month
     *
     * @param int
     */
	public $NewFollowerAvg;
	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		
		$this->Chart = array();
		$this->BestDates = array();
    }
	
    /**
     * Add new data to list storage
     *
     * @param mixed $value
     * @return self
     */
    public function AddList( $value )
	{
		$key = func_num_args() > 1 ? func_get_arg( 0 ) : "Chart";
		$val = func_num_args() > 1 ? func_get_arg( 1 ) : $value;
		$this->{$key}[] = $this->PrepareData( $val );
	}
}
