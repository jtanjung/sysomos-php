<?php 
namespace SysOmos\Bases;

use SysOmos\Bases\Module;

/**
 * Class UserModule
 * @package SysOmos\Bases
 */
class UserModule extends Module {

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
		if( ! $this->end_point_path ){
			return FALSE;
		}
		
		$w_param = array(
			"u" => $this->query
		);
		
		if( $this->start_date && $this->end_date ){
			$w_param[ "sDy" ] = $this->start_date;
			$w_param[ "eDy" ] = $this->end_date;
		}
		else
		{
			$w_param[ "dRg" ] = date("t");
		}
		
		$w_param = http_build_query( $w_param );
		$sub_path = urlencode( "/ajax/" . $this->end_point_path . ".jsp?$w_param" );
		return "http://map.sysomos.com/wrapper.jsp?w=" . $sub_path;
	}
	
}
