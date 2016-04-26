<?php 
namespace SysOmos\Bases;

use PHPMisc\Constants\ObjectUtils;

/**
 * Class Model
 * @package SysOmos\Bases
 */
class Model {
	
    /**
     * List of asset, eg: graphic image urls
     *
     * @param array
     */
	public $Assets;
	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		$this->Assets = array();
    }

    /**
     * Add asset list
     *	 
     * @param string $value
     * @return self
     */
	public function AddAsset( $key, $value ) 
	{
		$this->Assets[ $key ] = $value;
		return $this;
	}

    /**
     * Get an asset from list
     *	 
     * @param string $key
     * @return mixed
     */
	public function GetAsset( $key ) 
	{
		return isset( $this->Assets[ $key ] ) ? $this->Assets[ $key ] : FALSE;
	}

    /**
     * Get all assets from list
     *	 
     * @return array
     */
	public function GetAssets() 
	{
		return $this->Assets;
	}

    /**
     * Prepare data in object format
     *	 
     * @param mixed $value
     * @return object
     */
	protected function PrepareData( $value ) 
	{
		return ObjectUtils::ArrayToJSON( $value );	
	}
	
}
