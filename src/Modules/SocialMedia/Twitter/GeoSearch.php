<?php namespace SysOmos\Modules\SocialMedia\Twitter;

use SysOmos\Bases\Module;
use SysOmos\Interfaces\IModule;
use SysOmos\Models\SocialMedia\Twitter\GeoSearchModel;
use PHPMisc\Constants\TextUtils;

/**
 * Class GeoSearch
 * @package SysOmos\Modules
 */
class GeoSearch extends Module implements IModule {
		
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->end_point_path = "twitter_geo_simple";
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
		$csv_data = array_slice( $csv_data, 6 );
		
		foreach( $csv_data as $key => $val ){
			if( sizeof( $val ) != 4 ){
				continue;
			}
			$this->data->AddList(
				"Chart",
				array(
					"City" => $val[ 0 ],
					"State" => $val[ 1 ],
					"Country" => $val[ 2 ],
					"Count" => (float)$val[ 3 ]
				)
			);
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
		preg_match( "/Not sufficient data to display the map/", $this->response_text, $is_empty );
		if( $is_empty ){
			return FALSE;
		}
		
		$this->data = new GeoSearchModel();		
		
		$pointers = $this->ExtractTextAll( "(.*?)", "group.createCircle\(", "\).setFill\(", $this->response_text );
		foreach( $pointers as $pointer ){
			$this->data->AddList( TextUtils::StringToJSON( $pointer ) );
		}
		
		
		$this->data->AddAsset(
			"WorldMap",
			$this->ExtractText( "(.*?)", "<h2>World<\/h2><img src=\"", "\"", $this->response_text )
		);
		$this->data->AddAsset(
			"USAMap",
			$this->ExtractText( "(.*?)", "<h2>USA<\/h2><img src=\"", "\"", $this->response_text )
		);
		$this->data->AddAsset(
			"EuropeMap",
			$this->ExtractText( "(.*?)", "<h2>Europe<\/h2><img src=\"", "\"", $this->response_text )
		);
		$this->data->AddAsset(
			"SouthAmericaMap",
			$this->ExtractText( "(.*?)", "<h2>South America<\/h2><img src=\"", "\"", $this->response_text )
		);
		$this->data->AddAsset(
			"MiddleEastMap",
			$this->ExtractText( "(.*?)", "<h2>Middle East<\/h2><img src=\"", "\"", $this->response_text )
		);
		$this->data->AddAsset(
			"AsiaMap",
			$this->ExtractText( "(.*?)", "<h2>Asia<\/h2><img src=\"", "\"", $this->response_text )
		);
		$this->data->AddAsset(
			"AfricaMap",
			$this->ExtractText( "(.*?)", "<h2>Africa<\/h2><img src=\"", "\"", $this->response_text )
		);
		
		return TRUE;
	}
}
