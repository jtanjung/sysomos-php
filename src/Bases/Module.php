<?php
namespace SysOmos\Bases;

use SysOmos\Bases\Model;
use SysOmos\Constants\SysOmosResponse;
use PHPMisc\Constants\TextUtils;

/**
 * Class Module
 * @package SysOmos\Bases
 */
class Module {

    /**
     * End point sub path
     * @var string
     */
	protected $end_point_path;

    /**
     * Query data storage
     * @var Model
     */
	protected $data;

    /**
     * Query keyword
     * @var string
     */
	protected $query;

    /**
     * Request parameters
     * @var array
     */
	protected $parameters;

    /**
     * Sysomos response content
     * @var string
     */
	protected $response_text;

    /**
     * Temporary csv file name
     * @var string
     */
	protected $csv_temp_file;

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
    }

    /**
     * Set query keyword
     *
     * @param string $query
     * @return self
     */
    public function SetQuery( $query )
	{
		$this->query = $query;
		return $this;
	}

    /**
     * Set request parameters
     *
     * @param array $value
     * @return self
     */
    public function SetParameters( $value )
	{
		$this->parameters = $value;
		return $this;
	}

    /**
     * Set response text
     *
     * @param string $response_text
     * @return self
     */
	public function SetResponse( $response_text )
	{
		$this->response_text = FALSE;
		if( ! is_string( $response_text ) ){
			return $this;
		}

		$this->response_text = $response_text;
		return $this;
	}

    /**
     * Extarct response into model structure
     *
     * @return boolean
     */
    public function Extract()
	{
		if( ! $this->response_text ){
			return FALSE;
		}

		if( trim( $this->response_text ) == "___SERVER_DOWN___" ){
			return SysOmosResponse::$SERVER_DOWN;
		}

		preg_match( "/Invalid Query :/", $this->response_text, $invalid_query );
		if( $invalid_query ){
			return SysOmosResponse::$INVALID_QUERY;
		}

		return $this->DoExtract();
	}

    /**
     * Get csv url from response text
     *
     * @return array
     */
    public function GetCSVUrl()
	{
		return FALSE;
	}

    /**
     * Format csv data
     *
     * @return mixed
     */
    public function FormatCSV()
	{
		return $this->DoFormatCSV();
	}

    /**
     * Execute csv data format
     *
     * @return self
     */
    protected function DoFormatCSV()
	{
		return $this;
	}

    /**
     * Get query data
     *
     * @return Model
     */
    public function GetData()
    {
		return $this->data;
	}

    /**
     * Set temporary file path
     *
     * @return self
     */
    public function SetTempFilePath( $path )
    {
		$this->csv_temp_file = $path;
		return $this;
	}

    /**
     * Get url end point
     *
     * @return string
     */
    public function GetEndPoint()
	{
		if( ! $this->end_point_path ){
			return FALSE;
		}

		$query_string = array(
			"q" => $this->query,
			"luc" => FALSE,
			"viw" => $this->end_point_path,
			"sFl" => "",
			"sco" => "DATE_ISOME"
		);
		
		$this->SetTimelineParameter( $query_string )
			 ->SetDemographicsParameter( $query_string )
			 ->SetSubKeywordsParameter( $query_string )
			 ->SetBlogSetParameter( $query_string )
			 ->SetMediaSetParameter( $query_string )
			 ->SetLanguageParameter( $query_string )
			 ->SetDomainParameter( $query_string );

		$query_string = http_build_query( $query_string );
		$sub_path = urlencode( "/inc/analyze/" . $this->end_point_path . ".jsp?$query_string" );
		return "http://map.sysomos.com/wrapper.jsp?w=" . $sub_path;
	}

    /**
     * Add timeline range to request parameters
	 *
     * @param array $value
     * @return self
     */
    private function SetTimelineParameter( &$value )
	{
		if( ! isset( $this->parameters->timeline->start ) || 
			! isset( $this->parameters->timeline->end ) ){
			return $this;
		}
		
		$start_date = $this->parameters->timeline->start;
		$end_date = $this->parameters->timeline->end;

		if( date( 'Y-m-d', strtotime( $end_date ) ) == date( 'Y-m-d' ) ){
			$dRg = strtotime( $end_date ) - strtotime( $start_date );
			$dRg = ceil( $dRg / 86400 );
			$value[ "dRg" ] = $dRg;
		}
		else
		{
			$value[ "sDy" ] = date( 'Y-m-d', strtotime( $start_date ) );
			$value[ "eDy" ] = date( 'Y-m-d', strtotime( $end_date ) );
		}
		
		return $this;
	}

    /**
     * Add demographic options to request parameters
	 *
     * @param array $value
     * @return self
     */
    private function SetDemographicsParameter( &$value )
	{
		if( ! @$this->parameters->demographics ){
			return $this;
		}
		
		if( is_array( @$this->parameters->demographics->countries ) ){
			$value[ 'mGo' ] = implode( ',', $this->parameters->demographics->countries );
		}
		if( @$this->parameters->demographics->province ){
			$value[ 'pro' ] = $this->parameters->demographics->province;
		}
		if( @$this->parameters->demographics->city ){
			$value[ 'cit' ] = $this->parameters->demographics->city;
		}
		
		return $this;
	}

    /**
     * Add sub keyword to request parameters
	 *
     * @param array $value
     * @return self
     */
    private function SetSubKeywordsParameter( &$value )
	{
		if( @$this->parameters->sub_keywords ){
			$value[ 'skw' ] = $this->parameters->sub_keywords;
		}
		
		return $this;
	}

    /**
     * Add blog to request parameters
	 *
     * @param array $value
     * @return self
     */
    private function SetBlogSetParameter( &$value )
	{
		if( @$this->parameters->blog_set ){
			$value[ 'bst' ] = $this->parameters->blog_set;
		}
		
		return $this;
	}

    /**
     * Add media to request parameters
	 *
     * @param array $value
     * @return self
     */
    private function SetMediaSetParameter( &$value )
	{
		if( @$this->parameters->media_set ){
			$value[ 'mst' ] = $this->parameters->media_set;
		}
		
		return $this;
	}

    /**
     * Add language to request parameters
	 *
     * @param array $value
     * @return self
     */
    private function SetLanguageParameter( &$value )
	{
		if( is_array( @$this->parameters->language ) ){
			$value[ 'lnG' ] = implode( ',', $this->parameters->language );
		}
		
		return $this;
	}

    /**
     * Add domain to request parameters
	 *
     * @param array $value
     * @return self
     */
    private function SetDomainParameter( &$value )
	{
		if( @$this->parameters->domain ){
			$value[ 'dmN' ] = $this->parameters->domain;
		}
		
		return $this;
	}

    /**
     * Grab single text between two string using regex pattern
	 *
     * @param string $pattern
     * @param string $start
     * @param string $end
     * @param string $text
     * @return string
     */
    protected function ExtractText( $pattern, $start, $end, $text )
	{
		return TextUtils::GrabText( $pattern, $start, $end, $text );
	}

    /**
     * Grab all texts between two string using regex pattern
	 *
     * @param string $pattern
     * @param string $start
     * @param string $end
     * @param string $text
     * @return string
     */
    protected function ExtractTextAll( $pattern, $start, $end, $text )
	{
		return TextUtils::GrabTextAll( $pattern, $start, $end, $text );
	}

}
