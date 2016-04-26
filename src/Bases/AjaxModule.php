<?php 
namespace SysOmos\Bases;

use SysOmos\Bases\Module;

/**
 * Class AjaxModule
 * @package SysOmos\Bases
 */
class AjaxModule extends Module {
	
    /**
     * End point view param
     * @var string
     */
	protected $view_end_point;
	
    /**
     * End point srS param
     * @var string
     */
	protected $source_end_point;
	
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
			"q" => $this->query,
			"luc" => false,
			"viw" => $this->view_end_point ? $this->view_end_point : basename( $this->end_point_path ),
			"sFl" => "",
			"srS" => $this->source_end_point,
			"sco" => "DATE_ISOME"
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
		return "http://map.sysomos.com/ajax/" . $this->end_point_path . ".jsp?$w_param";
	}
	
}
