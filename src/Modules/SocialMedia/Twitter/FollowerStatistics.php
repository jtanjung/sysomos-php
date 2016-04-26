<?php namespace SysOmos\Modules\SocialMedia\Twitter;

use SysOmos\Bases\UserModule;
use SysOmos\Interfaces\IModule;
use SysOmos\Models\SocialMedia\Twitter\FollowerStatisticsModel;

/**
 * Class FollowerStatistics
 * @package SysOmos\Modules
 */
class FollowerStatistics extends UserModule implements IModule {
		
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->end_point_path = "twitter_follower_statistics";
    }

    /**
     * Get csv url from response text
     *
     * @return array
     */
    public function GetCSVUrl()
	{
		$urls = $this->ExtractTextAll( "(.*?)", "'res_foot_nav_float_left res_g_f_l_default noprint no_print_preview'\)", "Export CSV", $this->response_text );
		
		return array(
			"WordCloud" => $this->ExtractText( "(.*?)", 'href="', '"', $urls[ 0 ] ),
			"Follower" => $this->ExtractText( "(.*?)", 'href="', '"', $urls[ 1 ] )
		);
		
	}

    /**
     * Execute csv data format
     *
     * @return self
     */
    protected function DoFormatCSV()
	{
		if( file_exists( $this->csv_temp_file . '_WordCloud' ) ){
			$csv_data = array_map( 'str_getcsv', file( $this->csv_temp_file . '_WordCloud' ) );
			$csv_data = array_slice( $csv_data, 5 );
			
			foreach( $csv_data as $key => $val ){
				if( sizeof( $val ) != 2 ){
					continue;
				}
				$this->data->AddList(
					"WordCloud",
					array(
						"Word" => $val[ 0 ],
						"Percentage" => (float)rtrim( $val[ 1 ], '%' )
					)
				);
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
		preg_match( "/No twitter profile found/", $this->response_text, $is_empty );
		if( $is_empty ){
			return FALSE;
		}
		
		$this->data = new FollowerStatisticsModel();
		
		$blocks = $this->ExtractTextAll( "(.*?)", '<div class="twitter_details_geo_data" style="padding-left:5px;font-size:12px">', '<\/div>', $this->response_text );
		$regions_block = $this->ExtractTextAll( "(.*?)", '<img ', '<br \/>', $blocks[ 0 ] );
		$authorities_block = $this->ExtractTextAll( "(.*?)", 'Auth', '<br \/>', $blocks[ 1 ] );
		
		foreach( $regions_block as $region ){
			$name = $this->ExtractText( "(.*?)", '"> ', ':', $region );
			$percent = $this->ExtractText( "(.*?)", "$name:", '%', $region );
			$percent = trim( $percent );
			
			$this->data->AddList(
				"Regions",
				array(
					"Flag" => $this->ExtractText( "(.*?)", 'src="', '">', $region ),
					"Name" => $name,
					"Percentage" => (float)$percent
				)
			);
		}
		
		foreach( $authorities_block as $authority ){
			$percent = $this->ExtractText( "(.*?)", ':', '% followers ', $authority );
			$percent = trim( $percent );
			
			$this->data->AddList(
				"FollowersAuthority",
				array(
					"Index" => (float)$this->ExtractText( "(.*?)", '<b>', '<\/b>', $authority ),
					"Percentage" => (float)$percent
				)
			);
		}
		
		$high_authorities = $this->ExtractText( "(.*?)", 'title="Authority 8-10"', 'title="Authority 4-7">Medium<\/span>', $this->response_text );
		$high_authorities = $this->ExtractTextAll( "(.*?)", '<div class="maFollowersItem">', '<\/div><\/div>', $high_authorities );		
		foreach( $high_authorities as $high ){
			$profile_image = $this->ExtractText( "(.*?)", '<img src="', '"', $high );			
			$screen_name = $this->ExtractText( "(.*?)", '<strong>@', '<\/strong>', $high );
			$full_name = $this->ExtractText( "(.*?)", 'style="font-size:12px;color:#a5a09b;">', '<\/span><br \/>', $high );
			$following = $this->ExtractText( "(.*?)", 'Following: <b>', '<\/b>&nbsp;', $high );
			$follower = $this->ExtractText( "(.*?)", 'Followers: <b>', '<\/b><br \/>', $high );
			$bio = $this->ExtractText( "(.*?)", '<\/b><br \/>', '<\/div>', $high );
			
			$this->data->AddList(
				"HighAuthoritativeFollowers",
				array(
					"ProfilePict" => $profile_image,
					"ScreenName" => $screen_name,
					"FullName" => $full_name,
					"Following" => (int)str_replace( ',', '', $following ),
					"Follower" => (int)str_replace( ',', '', $follower ),
					"Bio" => $bio
				)
			);
		}
		
		$medium_authorities = $this->ExtractText( "(.*?)", 'title="Authority 4-7">Medium<\/span>', 'title="Authority 0-3">Low<\/span>', $this->response_text );
		$medium_authorities = $this->ExtractTextAll( "(.*?)", '<div class="maFollowersItem">', '<\/div><\/div>', $medium_authorities );		
		foreach( $medium_authorities as $medium ){
			$profile_image = $this->ExtractText( "(.*?)", '<img src="', '"', $medium );			
			$screen_name = $this->ExtractText( "(.*?)", '<strong>@', '<\/strong>', $medium );
			$full_name = $this->ExtractText( "(.*?)", 'style="font-size:12px;color:#a5a09b;">', '<\/span><br \/>', $medium );
			$following = $this->ExtractText( "(.*?)", 'Following: <b>', '<\/b>&nbsp;', $medium );
			$follower = $this->ExtractText( "(.*?)", 'Followers: <b>', '<\/b><br \/>', $medium );
			$bio = $this->ExtractText( "(.*?)", '<\/b><br \/>', '<\/div>', $medium );
			
			$this->data->AddList(
				"MediumAuthoritativeFollowers",
				array(
					"ProfilePict" => $profile_image,
					"ScreenName" => $screen_name,
					"FullName" => $full_name,
					"Following" => (int)str_replace( ',', '', $following ),
					"Follower" => (int)str_replace( ',', '', $follower ),
					"Bio" => $bio
				)
			);
		}
		
		$low_authorities = $this->ExtractText( "(.*?)", 'title="Authority 0-3">Low<\/span>', '<a id="flExpandView" ', $this->response_text );
		$low_authorities = $this->ExtractTextAll( "(.*?)", '<div class="maFollowersItem">', '<\/div><\/div>', $low_authorities );		
		foreach( $low_authorities as $low ){
			$profile_image = $this->ExtractText( "(.*?)", '<img src="', '"', $low );			
			$screen_name = $this->ExtractText( "(.*?)", '<strong>@', '<\/strong>', $low );
			$full_name = $this->ExtractText( "(.*?)", 'style="font-size:12px;color:#a5a09b;">', '<\/span><br \/>', $low );
			$following = $this->ExtractText( "(.*?)", 'Following: <b>', '<\/b>&nbsp;', $low );
			$follower = $this->ExtractText( "(.*?)", 'Followers: <b>', '<\/b><br \/>', $low );
			$bio = $this->ExtractText( "(.*?)", '<\/b><br \/>', '<\/div>', $low );
			
			$this->data->AddList(
				"LowAuthoritativeFollowers",
				array(
					"ProfilePict" => $profile_image,
					"ScreenName" => $screen_name,
					"FullName" => $full_name,
					"Following" => (int)str_replace( ',', '', $following ),
					"Follower" => (int)str_replace( ',', '', $follower ),
					"Bio" => $bio
				)
			);
		}
		
		$this->data->AuthorityPercentage = (float)$this->ExtractText( "(.*?)", '\(Average Authority: ', '\)<\/h3>', $this->response_text );
		$this->data->GenderDistribution->Male = (float)$this->ExtractText( "(.*?)", '<div class="gender_chart_male" style="height:', '%;">', $this->response_text );
		$this->data->GenderDistribution->Female = (float)$this->ExtractText( "(.*?)", '<div class="gender_chart_female" style="height:', '%;">', $this->response_text );
		
		$blocks = $this->ExtractTextAll( "(.*?)", '<div style="float:left; display: block; width: 430px">', '<div class="twitter_details_geo_data"', $this->response_text );
		
		$this->data->AddAsset(
			"RegionMap",
			$this->ExtractText( "(.*?)", '<img src="', '"', $blocks[ 0 ] )
		);
		$this->data->AddAsset(
			"FollowersAuthority",
			$this->ExtractText( "(.*?)", '<img src="', '"', $blocks[ 1 ] )
		);
		$this->data->AddAsset(
			"WordCloud",
			$this->ExtractText( "(.*?)", '"\).src = "', '";', $this->response_text )
		);
		
		return TRUE;
	}
}
