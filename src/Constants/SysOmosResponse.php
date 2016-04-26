<?php
namespace SysOmos\Constants;

use PHPMisc\Constants\ResponseCode;

/**
 * Class SysOmosResponse
 * @package SysOmos\Constants
 */
class SysOmosResponse extends ResponseCode {

    /**
     * Response code for no data
     */
	public static $NO_DATA = 'NO_DATA';

    /**
     * Response code for no module
     */
	public static $NO_MODULE = 'NO_MODULE';

    /**
     * Response code for waiting data response
     */
	public static $WAITING_RESPONSE = 'WAITING_RESPONSE';

    /**
     * Response code for server down
     */
	public static $SERVER_DOWN = 'SERVER_DOWN';

    /**
     * Response code for invalid query
     */
	public static $INVALID_QUERY = 'INVALID_QUERY';	
}
