<?php namespace SysOmos\Modules\SocialMedia\Twitter;

use SysOmos\Bases\Module;
use SysOmos\Interfaces\IModule;
use SysOmos\Models\SocialMedia\Twitter\DemographicsModel;

/**
 * Class Demographics
 * @package SysOmos\Modules
 */
class Demographics extends Module implements IModule {
		
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->end_point_path = "twitter_demographics_simple";
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
     * Retrieve region data from csv response content
     *
     * @return self
     */
    private function DoFormatRegionCSV( $csv_data, $region_name, $label, $value )
	{
		$names = $csv_data[ $label ];
		$values = $csv_data[ $value ];
		
		for( $i = 0; $i < sizeof( $names ); $i++ ){
			$this->data->AddList(
				$region_name,
				array(
					"ScreenText" => $names[ $i ],
					"Volume" => (float)$values[ $i ]
				)
			);
		}
		
		return $this;
	}

    /**
     * Execute csv data format
     *
     * @return self
     */
    protected function DoFormatCSV()
	{		
		$csv_data = array_map( 'str_getcsv', file( $this->csv_temp_file ) );
		
		$this->data->GenderDistribution->Male = (float)$csv_data[ 8 ][ 0 ];
		$this->data->GenderDistribution->Female = (float)$csv_data[ 8 ][ 1 ];
		
		$this->DoFormatRegionCSV( $csv_data, "CountryDistribution", 12, 13 )
			 ->DoFormatRegionCSV( $csv_data, "USStatesDistribution", 17, 18 )
			 ->DoFormatRegionCSV( $csv_data, "CanadaDistribution", 22, 23 );
		
		return $this;
	}

    /**
     * Extarct response into model structure
     *
     * @return boolean
     */
    public function DoExtract()
	{
		preg_match( "/No gender information available/", $this->response_text, $is_empty );
		if( $is_empty ){
			return FALSE;
		}
		
		$this->data = new DemographicsModel();
		
		$this->data->AddAsset(
			"CountryDistribution",
			$this->ExtractText( 
				"(.*?)", 
				"<h2>Country Distribution<\/h2><div class=\"contGChart\"><img src=\"", 
				"\" width=\"750\" height=\"240\"", 
				$this->response_text 
			) 
		);
		
		$this->data->AddAsset(
			"USStatesDistribution",
			$this->ExtractText( 
				"(.*?)", 
				"<h2>US States Distribution<\/h2><div class=\"contGChart\"><img src=\"", 
				"\" width=\"750\" height=\"240\"", 
				$this->response_text 
			) 
		);
		
		$this->data->AddAsset(
			"CanadaDistribution",
			$this->ExtractText( 
				"(.*?)", 
				"<h2>Canada Province Distribution<\/h2><div class=\"contGChart\"><img src=\"", 
				"\" width=\"750\" height=\"240\"", 
				$this->response_text 
			) 
		);
				
		return TRUE;
	}
}
