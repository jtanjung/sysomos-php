<?php namespace SysOmos\Models;

/**
 * Class AuthorityBreakdownModel
 * @package SysOmos\Models
 */
class AuthorityBreakdownModel {
		
    /**
     * Low authority breakdown as percentage value
     *
     * @param float
     */
	public $Low = 0;
		
    /**
     * Medium authority breakdown as percentage value
     *
     * @param float
     */
	public $Medium = 0;
	
    /**
     * High authority breakdown as percentage value
     *
     * @param float
     */
	public $High = 0;
	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
    }
}
