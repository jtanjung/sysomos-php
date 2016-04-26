<?php 
namespace SysOmos\Models;

use SysOmos\Bases\Model;

/**
 * Class GenderDistributionModel
 * @package SysOmos\Models
 */
class GenderDistributionModel extends Model {
		
    /**
     * Percentage of male distribution
     *
     * @param float
     */
	public $Male = 0;
		
    /**
     * Percentage of female distribution
     *
     * @param float
     */
	public $Female = 0;
	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
    }
}
