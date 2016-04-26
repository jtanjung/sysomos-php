<?php namespace SysOmos\SocialMedia\Twitter\Models;

use SysOmos\Bases\Model;
use SysOmos\Interfaces\IModel;

/**
 * Class TopSourceModel
 * @package SysOmos\Models
 */
class TopSourceModel extends Model implements IModel {
		
    /**
     * List of statistic table data
     *
     * @param array
     */
	public $Table;
		
    /**
     * List of statistic chart data
     *
     * @param array
     */
	public $Chart;
		
    /**
     * Tweet total count
     *
     * @param int
     */
	public $TweetCount;
	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->Table = array();
		$this->Chart = array();
		$this->TweetCount = 0;
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
		$this->{$key}[] = $this->PrepareData( $val );
	}
}
