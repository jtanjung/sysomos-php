<?php 
namespace SysOmos\Models;

use SysOmos\Bases\Model;

/**
 * Class LoginModel
 * @package SysOmos\Models
 */
class LoginModel extends Model {
		
    /**
     * Login status
     *
     * @param int
     */
	public $LoginStatus;
	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
    }
}
