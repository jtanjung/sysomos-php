<?php namespace SysOmos\Modules\SocialMedia\Twitter;

use SysOmos\Bases\UserModule;
use SysOmos\Interfaces\IModule;
use SysOmos\Models\SocialMedia\Twitter\FollowerGrowthModel;

/**
 * Class FollowerGrowth
 * @package SysOmos\Modules
 */
class FollowerGrowth extends UserModule implements IModule {
		
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->end_point_path = "twitter_follower_graphs";
    }

    /**
     * Get csv url from response text
     *
     * @return array
     */
    public function GetCSVUrl()
	{
		$url = $this->ExtractText( "(.*?)", "'res_foot_nav_float_left res_g_f_l_default noprint no_print_preview'\)", "Export CSV", $this->response_text );
		return $this->ExtractText( "(.*?)", 'href="', '"', $url );
	}

    /**
     * Execute csv data format
     *
     * @return self
     */
    protected function DoFormatCSV()
	{
		$csv_data = array_map( 'str_getcsv', file( $this->csv_temp_file ) );
		$csv_data = array_slice( $csv_data, 3 );
		
		foreach( $csv_data as $key => $val ){
			if( sizeof( $val ) < 2 ){
				continue;
			}
			
			$this->data->AddList(
				array(
					"Date" => $val[ 0 ],
					"Follower" => (float)$val[ 1 ],
					"NewFollower" => isset( $val[ 2 ] ) ? (float)$val[ 2 ] : 0
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
		preg_match( "/An error occurred/", $this->response_text, $is_empty );
		if( $is_empty ){
			return FALSE;
		}
		
		$this->data = new FollowerGrowthModel();
		
		$this->data->BestDates = $this->ExtractTextAll( "(.*?)", 'title="See most retweeted Tweets for this date">', '<\/a><br \/>', $this->response_text );
		$this->data->NewFollowerAvg = $this->ExtractText( "(.*?)", '<span style="color:#417a04;font-size:20px;font-weight:bold;">', '<\/span>', $this->response_text );
		$this->data->NewFollowerAvg = (int)str_replace( ",", "", $this->data->NewFollowerAvg ); 
				
		return TRUE;
	}
}
