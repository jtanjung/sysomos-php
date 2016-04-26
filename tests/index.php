<?php
date_default_timezone_set( "Asia/Jakarta" );
require_once "../vendor/autoload.php";

use SysOmos\SysOmos;
use SysOmos\SysOmosResponse;

$sysomos = new SysOmos();
$sysomos->UserName = "USERNAME";
$sysomos->Password = "PASSWORD";

$source = $sysomos->TopQuery( "#facebook" );

echo "<h1>#facebook</h1>";
print_r( $source->GetData() );
echo "<br />";
echo '<div><img src="' . $source->GetMap() . '" /></div>';
