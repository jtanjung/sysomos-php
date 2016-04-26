<?php
namespace SysOmos;

use SysOmos\Modules;
use SysOmos\Constants\SysOmosResponse;
use PHPMisc\Configs\CURLConfig;
use PHPMisc\Configs\ProxyConfig;
use PHPMisc\Classes\CURLFactory;
use PHPMisc\Constants\TextUtils;
use PHPMisc\Constants\GearmanUtils;

/**
 * Class SysOmos
 * @package SysOmos
 */
class SysOmos {

    /**
     * CURL Factory
     * @var CURLFactory
     */
	protected $curl;

    /**
     * CURL Configuration
     * @var CURLConfig
     */
	protected $curl_config;

    /**
     * CURL cookie directory
     * @var string
     */
	protected $CookieDir;

    /**
     * CURL temporary directory
     * @var string
     */
	protected $temp_dir;

    /**
     * CURL temporary file destination
     * @var string
     */
	protected $temp_file;

    /**
     * Login indicator
     * @var boolean
     */
	protected $is_login;

    /**
     * Login status
     * @var int
     */
	protected $login_status;

    /**
     * Module object
     * @var Module
     */
	protected $module;

    /**
     * Gearman function name to do the task
     * @var string
     */
	public static $GearmanWorkerName = "SysOmosRequest";

    /**
     * Sysomos username
     * @var string
     */
	public $UserName;

    /**
     * Sysomos password
     * @var string
     */
	public $Password;

    /**
     * Proxy IP Address
     * @var string
     */
	public $ProxyIP;

    /**
     * Proxy Port
     * @var int
     */
	public $ProxyPort;

    /**
     * Proxy user name
     * @var string
     */
	public $ProxyUserName;

    /**
     * Proxy password
     * @var string
     */
	public $ProxyPassword;

    /**
     * CURL request timeout
     * @var string
     */
	public $TimeOut;

    /**
     * Class constructor
     *
     */
    public function __construct( $username = NULL, $password = NULL )
    {
		$this->UserName = $username;
		$this->Password = $password;

		$this->TimeOut = 30;
		$this->curl_config = new CURLConfig();
		$this->curl = new CURLFactory( $this->curl_config );
		$this->CookieDir = realpath( __DIR__ . '/..' ) . "/cookie/";
		$this->temp_dir = realpath( __DIR__ . '/..' ) . "/temp/";
    }

    /**
     * Send curl request
     *
     * @param string $end_point
     * @param boolean $clean_response
     * @return string
     */
	protected function SendRequest( $end_point, $download = FALSE, $max_retry = 3 )
	{
		$this->curl->GetConfig()->Proxy = FALSE;
		$this->curl->GetConfig()->CookieDir = $this->CookieDir;
		$this->curl->GetConfig()->SetCookieId( md5( $this->UserName . ":" . $this->Password ) );

		if( $this->ProxyIP ){
			$this->curl->GetConfig()->Proxy = new ProxyConfig(array(
				"IP" => $this->ProxyIP,
				"Port" => $this->ProxyPort,
				"UserName" => $this->ProxyUserName,
				"Password" => $this->ProxyPassword
			));
		}

		$result = FALSE;

		if( $download ){
			$result = $this->curl->DownloadFile(
				$end_point,
				$download,
				$this->TimeOut,
				$max_retry,
				$this->temp_file
			);
		}
		else
		{
			$result = TextUtils::CleanWhiteSpace(
				$this->curl->Execute(
					$end_point,
					$this->TimeOut,
					$max_retry,
					$this->temp_file
				)->GetResponse()
			);
		}

		if( $this->curl->GetErrorCode() > 0 ){
			return FALSE;
		}

		return $result;
	}

    /**
     * Trigger module request
     *
     * @param string $temp_file
     * @return self
     */
	public function PrepareRequest( $temp_file )
	{
		$this->temp_file = $this->temp_dir . $temp_file;
		return $this;
	}

    /**
     * Get request progress
     *
     * @return int
     */
	public function GetRequestProgress()
	{
		return $this->curl->GetProgress();
	}

    /**
     * Trigger module request
     *
     * @param string $module
     * @param string $query
     * @return int
     */
	public function Request( $module, $query = '', $parameters = [] )
	{
		if( ! $this->is_login ){
			if( $module != "Login" ){
				if( ! $this->Login() ){
					return SysOmosResponse::$LOGIN_FAILED;
				}
			}
		}

		$module_name = $this->IsModuleExists( $module );
		if( $module_name === FALSE ){
			return SysOmosResponse::$NO_MODULE;
		}
		
		$this->module = new $module_name();
		$this->module->SetQuery( $query )->SetParameters( $parameters );

		$response = $this->SendRequest( $this->module->GetEndPoint() );

		if( $response === FALSE ){
			file_put_contents( $this->temp_file, "___SERVER_DOWN___" );
			return SysOmosResponse::$SERVER_DOWN;
		}

		if( empty( $response ) ){
			return SysOmosResponse::$NO_RESPONSE;
		}

		if( $this->temp_file ){
			file_put_contents( $this->temp_file, $response );
		}

		return SysOmosResponse::$OK;
	}

    /**
     * Check if cache exists
     *
     * @param string $temp_file
     * @param int $cache_expire
     * @return boolean
     */
	private function DeleteCache( $temp_file )
	{
		$temp_file_name = $this->temp_dir . $temp_file;
		foreach( glob( $temp_file_name . "*" ) as $file_name ){
			unlink( $file_name );
		}
	}

    /**
     * Check if module exists
     *
     * @param string $module
     * @return mixed
     */
	public function IsModuleExists( $module )
	{
		$module_name = 'SysOmos\\Modules\\' . trim( $module, '\\' );
		return class_exists( $module_name ) ? $module_name : FALSE;
	}

    /**
     * Check if cache exists
     *
     * @param string $temp_file
     * @param int $cache_expire
     * @return boolean
     */
	public function IsCacheExists( $temp_file, $cache_expire = 1800 )
	{
		$temp_file_name = $this->temp_dir . $temp_file;
		if( ! file_exists( $temp_file_name ) && ! empty( $temp_file ) ){
			return FALSE;
		}
		
		if( is_int( $cache_expire ) ){
			$file_time = filemtime( $temp_file_name ) + $cache_expire;
			if( $file_time < time() ){
				$this->DeleteCache( $temp_file );
				return FALSE;
			}
		}

		return $temp_file_name;
	}

    /**
     * Retrieve module data from temp file
     *
     * @param string $module
     * @param string $temp_file
     * @return int
     */
	public function PrepareResponse( $module, $temp_file, $cache_expire = 1800 )
	{
		$module_name = $this->IsModuleExists( $module );
		if( $module_name === FALSE ){
			return SysOmosResponse::$NO_MODULE;
		}
		
		$temp_file_name = $this->IsCacheExists( $temp_file, $cache_expire );
		if( ! $temp_file_name ){
			return SysOmosResponse::$NO_RESPONSE;
		}

		$response = TextUtils::CleanWhiteSpace( file_get_contents( $temp_file_name ) );
				
		$this->module = new $module_name();

		$csv_file = $temp_file_name . "_tmp";
		$result = $this->module->SetTempFilePath( $csv_file )->SetResponse( $response )->Extract();

		if( $result === SysOmosResponse::$SERVER_DOWN ){
			$this->DeleteCache( $temp_file );
			return SysOmosResponse::$SERVER_DOWN;
		}

		if( $result === SysOmosResponse::$INVALID_QUERY ){
			$this->DeleteCache( $temp_file );
			return SysOmosResponse::$INVALID_QUERY;
		}
				
		if( $result === FALSE ){
			$this->DeleteCache( $temp_file );
			return SysOmosResponse::$NO_DATA;
		}

		$assets = $this->module->GetData()->GetAssets();
		if( sizeof( $assets ) ){
			foreach( $assets as $key => $val ){
				$this->SendRequest( $val, $csv_file . "_asset_$key" );
			}
		}

		$csv_url = $this->module->GetCSVUrl();

		if( $csv_url ){
			$csv = FALSE;
			switch( gettype( $csv_url ) ){
				case "array":
					foreach( $csv_url as $key => $val ){

						if( file_exists( $csv_file . "_" . $key ) ){
							$csv = TRUE;
							continue;
						}

						$__csv = $this->SendRequest( $val, $csv_file . "_" . $key );
						if( ! $csv ){
							$csv = $__csv;
						}

					}
					break;
				case "string":
					if( ! file_exists( $csv_file ) ){
						$csv = $this->SendRequest( $csv_url, $csv_file );
					}
					else
					{
						$csv = TRUE;
					}
					break;
			}

			if( $csv ){
				$this->module->FormatCSV();
			}
		}

		return SysOmosResponse::$OK;
	}

    /**
     * Login yo Sysomos.com
     *
     * @param string $username
     * @param string $password
     * @return self
     */
    public function Login()
    {
		if( $this->is_login ){
			return TRUE;
		}

		$this->curl->GetConfig()->ClearCookie();
		$this->curl->SetPostField(array(
			"u" => $this->UserName,
			"p" => $this->Password,
			"ref" => "http://map.sysomos.com/login/",
			"action" => "login"
		));

		$response = $this->SendRequest( "http://map.sysomos.com/login/", FALSE, 0 );
		preg_match( "/Logged In successfully/", $response, $is_signed_in );

		$result = TRUE;

		if( ! $is_signed_in ){
			$result = FALSE;
		}

		return $result;
    }

    /**
     * Get gearman worker file path
     *
     * @return string
     */
    public function GetWorkerPath()
    {
			return realpath( __DIR__ . '/..' ) . "/src/Supports/worker.php";
	}

	/**
	* Start gearman worker
	*/
	public function StartWorker( $count = 3, $name = "SysOmosRequest" )
	{
		GearmanUtils::RunWorker( $this->GetWorkerPath() . " "  . escapeshellarg( $this->UserName ) . " " . escapeshellarg( $this->Password ) . " " . escapeshellarg( $name ), $count );
	}

	/**
	* Start gearman worker
	*/
	public function StopWorker( $name = "SysOmosRequest" )
	{
		GearmanUtils::TerminateWorker( $this->GetWorkerPath() . " "  . escapeshellarg( $this->UserName ) . " " . escapeshellarg( $this->Password ) . " " . escapeshellarg( $name ), $name );
	}

    /**
     * Get module data
     *
     * @return Model
     */
    public function GetData()
    {
		return $this->module->GetData();
	}

    /**
     * Get one of module asset
     *
     * @param string $key
     * @return mixed
     */
    public function GetAsset( $process_id, $key )
    {
		$asset_file = $this->temp_dir . $process_id . "_tmp_asset_$key";
		if( ! file_exists( $asset_file ) ){
			return FALSE;
		}

		return $asset_file;
	}

    /**
     * Get all module asset
     *
     * @return array
     */
    public function GetAssets( $process_id )
    {
		$asset_files = FALSE;
		$assets = $this->module->GetAssets();
		if( sizeof( $assets ) ){
			$asset_files = array();
			foreach( $assets as $key => $val ){
				$asset_files[ $key ] = $this->GetAsset( $process_id, $key );
			}
		}

		return $asset_files;
	}

}
