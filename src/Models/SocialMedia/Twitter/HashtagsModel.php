<?php namespace SysOmos\SocialMedia\Twitter\Models;

use SysOmos\Bases\Model;
use SysOmos\Interfaces\IModel;

/**
 * Class HashtagsModel
 * @package SysOmos\Models
 */
class HashtagsModel extends Model implements IModel {
		
    /**
     * List of hastag
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
