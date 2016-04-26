<?php namespace SysOmos\Modules\SocialMedia\Twitter;

use SysOmos\Bases\Module;
use SysOmos\Interfaces\IModule;
use SysOmos\Models\SocialMedia\Twitter\WordCloudModel;

/**
 * Class WordCloud
 * @package SysOmos\Modules
 */
class WordCloud extends Module implements IModule {
		
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->end_point_path = "twitter_wordcloud";
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
		$csv_data = array_slice( $csv_data, 5 );
		
		foreach( $csv_data as $key => $val ){
			if( sizeof( $val ) != 2 ){
				continue;
			}
			$this->data->AddList(
				array(
					"Word" => $val[ 0 ],
					"Frequency" => $val[ 1 ]
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
		preg_match( "/There is no Word Cloud to display/", $this->response_text, $is_empty );
		if( $is_empty ){
			return FALSE;
		}
		
		$this->data = new WordCloudModel();
		
		$this->data->AddAsset(
			"Graphic",
			$this->ExtractText( "(.*?)", "\"\).src = \"", "\";", $this->response_text )
		);
		
		return TRUE;
	}
}
