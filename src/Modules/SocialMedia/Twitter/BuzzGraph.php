<?php namespace SysOmos\Modules\SocialMedia\Twitter;

use SysOmos\Bases\Module;
use SysOmos\Interfaces\IModule;
use SysOmos\Models\SocialMedia\Twitter\BuzzGraphModel;

/**
 * Class BuzzGraph
 * @package SysOmos\Modules
 */
class BuzzGraph extends Module implements IModule {
		
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->end_point_path = "twitter_buzzgraph_simple";
    }

    /**
     * Get csv url from response text
     *
     * @return array
     */
    public function GetCSVUrl()
	{
		$csv_url = $this->ExtractText( "(.*?)", "<!-- end infobox idea-->", "Export as CSV", $this->response_text );
		$csv_url = $this->ExtractText( "(.*?)", "<a id=\"button_label_", ">", $csv_url );
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
		$csv_data = array_slice( $csv_data, 4 );

		$score_contexts = $association_scores = array();
		
		foreach( $csv_data as $key => $val ){
		
			if( sizeof( $val ) == 4 ){
				$this->data->AddList(
					"ContextScore",
					array(
						"Word" => $val[ 0 ],
						"Score" => (float)$val[ 1 ],
						"Context" => $val[ 3 ]
					)
				);
				continue;
			}
		
			if( sizeof( $val ) == 3 ){
				if( $val[ 2 ] != "association score" ){
					$this->data->AddList(
						"AssociationScore",
						array(
							"Word1" => $val[ 0 ],
							"Word2" => $val[ 1 ],
							"Score" => (float)$val[ 2 ]
						)
					);
				}
				continue;
			}
			
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
		preg_match( "/There is no Buzzgraph to display/", $this->response_text, $is_empty );
		if( $is_empty ){
			return FALSE;
		}
		
		$this->data = new BuzzGraphModel();
		
		$related_words = $this->ExtractTextAll( "(.*?)", "class=\"search_q_labels\">", "<\/div><!-- end label div", $this->response_text );		
		if( $related_words ){
			foreach( $related_words as $word ){
				$this->data->AddList( $word );
			}
		}
		
		$this->data->AddAsset(
			"Graph",
			$this->ExtractText( "(.*?)", "<div style=\"min-height: 700px;\"><img src=\"", "\"", $this->response_text )
		);
		
		return TRUE;
	}
}
