<?php
date_default_timezone_set( "Asia/Jakarta" );
require_once "../vendor/autoload.php";

use SysOmos\SysOmos;

$sysomos = new SysOmos();
$sysomos->UserName = "USERNAME";
$sysomos->Password = "PASSWORD";

$sysomos->Request( "TopSource", "#facebook" );

var_dump( $sysomos->GetData() );
