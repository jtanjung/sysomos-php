<?php namespace SysOmos\Modules\SocialMedia\Twitter;

use SysOmos\Bases\Module;
use SysOmos\Interfaces\IModule;
use SysOmos\Models\SocialMedia\Twitter\TopSourceModel;

/**
 * Class TopSource
 * @package SysOmos\Modules
 */
class TopSource extends Module implements IModule {
		
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->end_point_path = "twitter_top_sources";
    }

    /**
     * Get csv url from response text
     *
     * @return array
     */
    public function GetCSVUrl()
	{
		return $this->ExtractText( "(.*?)", "<div class=\"contGChartButtons\"><a href=\"", "\" class=\"chartExportCSV\"", $this->response_text );
	}

    /**
     * Execute csv data format
     *
     * @return self
     */
    protected function DoFormatCSV()
	{
		$csv_data = array_map( 'str_getcsv', file( $this->csv_temp_file ) );
		
		$names = $csv_data[ 6 ];
		$values = $csv_data[ 7 ];
		
		for( $i = 0; $i < sizeof( $names ); $i++ ){
			$this->data->AddList(
				"Chart",
				array(
					"ScreenText" => $names[ $i ],
					"Volume" => isset( $values[ $i ] ) ? (float)$values[ $i ] : 0
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
		$this->data = new TopSourceModel();
		
		$reports = $this->ExtractText( "(.*?)", "class='report' width=\"540\" cellspacing=\"0\">", "<\/table>", $this->response_text );
		$anchors = $this->ExtractTextAll( "(.*?)", "<td width=\"40\" style=\"padding-right:0px\"><a href=\"", "<img border=\"0\" width=\"32\" height=\"32\"", $reports );
		$images = $this->ExtractTextAll( "(.*?)", "height=\"32\" src=", " \/>", $reports );
		$screennames = $this->ExtractTextAll( "(.*?)", "@", "<\/a>", $reports );
		$spannumbers = $this->ExtractTextAll( "([0-9]+)", "<span class=\"number\">", "<\/span>", $reports );
		$numbers = $this->ExtractTextAll( "([0-9]+)", "<td>", "<\/td>", $reports );
		
		if( $numbers ){
			foreach( $numbers as $num ){
				array_push( $spannumbers, $num );
			}
		}
				
		$this->data->TweetCount = 0;
		for( $i = 0; $i < sizeof( $anchors ); $i++ ){
			$count = (int)$spannumbers[ $i ];
			$this->data->TweetCount += $count;
			$this->data->AddList(
				"Table",
				array(
					"ProfileUrl" => $anchors[ $i ],
					"ProfilePict" => $images[ $i ],
					"ScreenName" => "@" . $screennames[ $i ],
					"TweetCount" => $count
				)
			);
		}
		
		$this->data->AddAsset(
			"PieChart",
			$this->ExtractText( "(.*?)", "<div class=\"contGChart\"><img src=\"", "\"", $this->response_text )
		);

		if( ! sizeof( $this->data->Table ) ){
			$this->data = FALSE;
			return FALSE;
		}
				
		return TRUE;
	}
}
