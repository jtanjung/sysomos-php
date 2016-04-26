<?php namespace SysOmos\Modules\SocialMedia\Twitter;

use SysOmos\Bases\Module;
use SysOmos\Interfaces\IModule;
use SysOmos\Models\SocialMedia\Twitter\PopularityModel;

/**
 * Class Popularity
 * @package SysOmos\Modules
 */
class Popularity extends Module implements IModule {
		
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->end_point_path = "twitter_popularity_simple";
    }

    /**
     * Get csv url from response text
     *
     * @return array
     */
    public function GetCSVUrl()
	{
		$csv_url = $this->ExtractText( "(.*?)", "<div class=\"res_foot_nav_float_left res_g_f_l_default \"", "Export as CSV", $this->response_text );
		$csv_url = $this->ExtractText( "(.*?)", "href=\"", "\"", $csv_url );
		return $csv_url;
	}

    /**
     * Execute csv data format
     *
     * @return self
     */
    protected function DoFormatCSV()
	{		
		$csv_data = array_map( 'str_getcsv', file( $this->csv_temp_file ) );
		
		$dates = $csv_data[ 9 ];
		$values = $csv_data[ 10 ];
		
		for( $i = 0; $i < sizeof( $dates ); $i++ ){
			if( empty( $dates[ $i ] ) ){
				continue;
			}
			
			$this->data->AddList(
				array(
					"Date" => $dates[ $i ],
					"Volume" => isset( $values[ $i ] ) ? (int)$values[ $i ] : 0
				)
			);
		}
		
		if( ! sizeof( $this->data->Chart ) ){
			$this->data = FALSE;
		}
		
		return $this;
	}

    /**
     * Extarct response into model structure
     *
     * @return boolean
     */
    public function DoExtract()
	{
		$this->data = new PopularityModel();
		return TRUE;
	}
}
