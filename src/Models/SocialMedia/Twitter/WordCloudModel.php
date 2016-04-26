<?php namespace SysOmos\SocialMedia\Twitter\Models;

use SysOmos\Bases\Model;
use SysOmos\Interfaces\IModel;

/**
 * Class WordCloudModel
 * @package SysOmos\Models
 */
class WordCloudModel extends Model implements IModel {
		
    /**
     * List of words
     *
     * @param array
     */
	public $List;
	
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
