<?php namespace SysOmos\Modules\SocialMedia\Twitter;

use SysOmos\Bases\Module;
use SysOmos\Interfaces\IModule;
use SysOmos\Models\SocialMedia\Twitter\HashtagsModel;

/**
 * Class Hashtags
 * @package SysOmos\Modules
 */
class Hashtags extends Module implements IModule {
		
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->end_point_path = "twitter_top_hashtags";
    }

    /**
     * Get csv url from response text
     *
     * @return array
     */
    public function GetCSVUrl()
	{
		$csv_url = $this->ExtractText( "(.*?)", "See more results", "Export as CSV", $this->response_text );
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
				array(
					"Hashtag" => $val[ 1 ],
					"Mentions" => (int)$val[ 2 ],
					"Percentage" => (float)$val[ 3 ]
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
		$this->data = new HashtagsModel();
		return TRUE;
	}
}
