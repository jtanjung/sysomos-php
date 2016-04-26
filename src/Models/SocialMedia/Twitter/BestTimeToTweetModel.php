<?php namespace SysOmos\SocialMedia\Twitter\Models;

use SysOmos\Bases\Model;
use SysOmos\Models\GenderDistributionModel;
use SysOmos\Interfaces\IModel;

/**
 * Class BestTimeToTweetModel
 * @package SysOmos\Models
 */
class BestTimeToTweetModel extends Model implements IModel {
		
    /**
     * List of table header
     *
     * @param array
     */
	public $Header;
		
    /**
     * List of table data
     *
     * @param array
     */
	public $Rows;
		
    /**
     * Best day to tweet
     *
     * @param string
     */
	public $BestDay;
		
    /**
     * Best time to tweet
     *
     * @param string
     */
	public $BestTime;
	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->Rows = array();
		$this->Header = array();
    }
	
    /**
     * Add new data to list storage
     *
     * @param mixed $value
     * @return self
     */
    public function AddList( $value )
	{
		$this->Header[] = $this->PrepareData( $value );
	}
}
