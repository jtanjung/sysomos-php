<?php 
namespace SysOmos\Modules;

use SysOmos\Bases\Module;
use SysOmos\Interfaces\IModule;
use SysOmos\Models\LoginModel;
use SysOmos\Constants\SysOmosResponse;

/**
 * Class Login
 * @package SysOmos\Modules
 */
class Login extends Module implements IModule {
		
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
    }
	
    /**
     * Get url end point
     *
     * @return string
     */
    public function GetEndPoint()
	{
		return "http://map.sysomos.com/login/";
	}

    /**
     * Extarct response into model structure
     *
     * @return boolean
     */
    public function DoExtract()
	{
		$this->data = new LoginModel();
		
		preg_match( "/Login/", $this->response_text, $is_not_signed_in );
		
		$result = FALSE;
		$this->data->LoginStatus = SysOmosResponse::$LOGIN_FAILED;
		
		if( ! $is_not_signed_in ){
			$result = TRUE;
			$this->data->LoginStatus = SysOmosResponse::$OK;
		}
		
		return $result;
	}
}
