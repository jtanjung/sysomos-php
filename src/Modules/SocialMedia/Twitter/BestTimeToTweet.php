<?php namespace SysOmos\Modules\SocialMedia\Twitter;

use SysOmos\Bases\UserModule;
use SysOmos\Interfaces\IModule;
use SysOmos\Models\SocialMedia\Twitter\BestTimeToTweetModel;

/**
 * Class BestTimeToTweet
 * @package SysOmos\Modules
 */
class BestTimeToTweet extends UserModule implements IModule {
		
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->end_point_path = "besttimetotweet";
    }

    /**
     * Extarct response into model structure
     *
     * @return boolean
     */
    public function DoExtract()
	{
		preg_match( "/Empty follower data/", $this->response_text, $is_empty );
		if( $is_empty ){
			return FALSE;
		}
		
		$this->data = new BestTimeToTweetModel();
		
		$blocks = $this->ExtractTextAll( "(.*?)", '<tr>', '<\/tr>', $this->response_text );		
		$header_blocks = $this->ExtractTextAll( "(.*?)", '<th id="column', 'th>', $blocks[ 0 ] );
		
		$this->data->AddList(
			array(
				"Value" => FALSE,
				"IsSun" => FALSE,
				"IsHour" => FALSE
			)
		);
		
		foreach( $header_blocks as $block ){
			$header = $this->ExtractText( "(.*?)", '">', '<\/', $block );
			$sun = FALSE;
			if( empty( $header ) ){
				$header = 0;				
				preg_match( '/sun_img/', $block, $is_sun );
				if( $is_sun ){
					$sun = TRUE;
				}
			}
			
			$hour = FALSE;			
			preg_match( '/th_hour/', $block, $is_hour );
			if( $is_hour ){
				$hour = TRUE;
			}

			$this->data->AddList(
				array(
					"Value" => $header,
					"IsSun" => $sun,
					"IsHour" => $hour
				)
			);
		}
		
		$color_value = array(
			"ffffff",
			"cad8ea",
			"b8cae3",
			"a6bddc",
			"94b0d5",
			"82a2ce",
			"4c7ab9"
		);
		
		$blocks = array_slice( $blocks, 1 );
		foreach( $blocks as $block ){
			$index = $this->ExtractText( "(.*?)", '<th id="row', 'th>', $block );
			$index = $this->ExtractText( "(.*?)", '">', '<\/', $index );
			$this->data->Rows[ $index ] = array();
			
			$columns = $this->ExtractTextAll( "(.*?)", '<td style="', '<\/td>', $block );
			foreach( $columns as $column ){
				$bg_color = $this->ExtractText( "(.*?)", 'background-color:#', '"', $column );
				$value = array_search( $bg_color, $color_value );
				$this->data->Rows[ $index ][] = array(
					"IntValue" => $value,
					"HexValue" => $bg_color
				);
			}
		}
		
		$best_blocks = $this->ExtractText( "(.*?)", 'id="client_timezone"><\/div>', '<\/div>', $this->response_text );
		$bests = $this->ExtractTextAll( "(.*?)", '<span>', '<\/span>', $best_blocks );
		
		$this->data->BestDay = $bests[ 0 ];
		$this->data->BestTime = $bests[ 1 ];
		
		$this->data->IsDataAvailable = $this->data->BestDay || $this->data->BestTime || 
										sizeof( $this->data->Rows ) || sizeof( $this->data->Header );
		return TRUE;
	}
}
