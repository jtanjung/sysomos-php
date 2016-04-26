<?php namespace SysOmos\SocialMedia\Twitter\Models;

use SysOmos\Bases\Model;
use SysOmos\Interfaces\IModel;

/**
 * Class BuzzGraphModel
 * @package SysOmos\Models
 */
class BuzzGraphModel extends Model implements IModel {
		
    /**
     * List of context score
     *
     * @param array
     */
	public $ContextScore;
		
    /**
     * List of association score
     *
     * @param array
     */
	public $AssociationScore;
		
    /**
     * List of related words
     *
     * @param array
     */
	public $RelatedWords;
	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->RelatedWords = array();
		$this->ContextScore = array();
		$this->AssociationScore = array();
    }
    /**
     * Add new data to list storage
     *
     * @param mixed $value
     * @return self
     */
    public function AddList( $value )
	{
		$key = func_num_args() > 1 ? func_get_arg( 0 ) : "RelatedWords";
		$val = func_num_args() > 1 ? func_get_arg( 1 ) : $value;
		$this->{$key}[] = $this->PrepareData( $val );
	}
}
