<?php namespace SysOmos\SocialMedia\Twitter\Models;

use SysOmos\Bases\Model;
use SysOmos\Interfaces\IModel;

/**
 * Class TweetsModel
 * @package SysOmos\Models
 */
class TweetsModel extends Model implements IModel {
		
    /**
     * List of tweets
     *
     * @param array
     */
	public $List;
		
    /**
     * Tweets total count
     *
     * @param int
     */
	public $Total;
	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->List = array();
    }
	
    /**
     * Add new data to list storage
     *
     * @param mixed $value
     * @return self
     */
    public function AddList( $value )
	{
		$this->List[] = $this->PrepareData( $value );
	}
}
