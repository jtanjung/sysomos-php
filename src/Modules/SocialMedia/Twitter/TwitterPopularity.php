<?php namespace SysOmos\Modules\SocialMedia\Twitter;

use SysOmos\Bases\AjaxModule;
use SysOmos\Interfaces\IModule;
use SysOmos\Models\SocialMedia\Twitter\TwitterPopularityModel;

/**
 * Class TwitterPopularity
 * @package SysOmos\Modules
 */
class TwitterPopularity extends AjaxModule implements IModule {
		
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->end_point_path = "twitter/twitterpopularity";
		$this->view_end_point = "twitter_search_simple";
		$this->source_end_point = "Twitter";
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
		
		$this->data = new TwitterPopularityModel();

		$genders_data = $this->ExtractTextAll( '(.*?)', '<span class="t_circle_percent_num_decimal">', '<\/span>', $this->response_text );
		
		$this->data->GenderDistribution->Male = (float)$genders_data[ 0 ];
		$this->data->GenderDistribution->Female = (float)$genders_data[ 1 ];

		$regions = $this->ExtractText( '(.*?)', '<table class="top_countries"', '<\/table>', $this->response_text );
		$regions = $this->ExtractTextAll( '(.*?)', '<tr>', '<\/tr>', $regions );
		
		foreach( $regions as $region ){
			$this->data->AddList(
				array(
					"Flag" => 'http://map.sysomos.com' . $this->ExtractText( '(.*?)', 'src="', '"', $region ),
					"Country" => $this->ExtractText( '(.*?)', '<td class="top_country_name">', '<\/td>', $region ),
					"Percentage" => (float)$this->ExtractText( '(.*?)', '<b>', '<\/b>', $region )
				)
			);			
		}
				
		$word_cloud = $this->ExtractText( '(.*?)', '<h5 clas="dash_title">Wordcloud<\/h5>', 'style="width:', $this->response_text );
		$word_cloud = $this->ExtractText( '(.*?)', '<img src="', '"', $word_cloud );

		$buzz_graph = $this->ExtractText( '(.*?)', '<h5 clas="dash_title">Buzzgraph<\/h5>', ' style=""', $this->response_text );
		$buzz_graph = $this->ExtractText( '(.*?)', '<img src="', '"', $buzz_graph );
				
		$this->data->AddAsset( "WordCloud", $word_cloud );
		$this->data->AddAsset( "BuzzGraph", $buzz_graph );
		
		return TRUE;
	}
}
