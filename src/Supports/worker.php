<?php
require_once realpath( __DIR__ . '/../../../..' ) . "/autoload.php";

use SysOmos\SysOmos;

$worker = new GearmanWorker();
$sysomos = new SysOmos( $argv[ 1 ], $argv[ 2 ] );
$function_name = isset( $argv[ 3 ] ) ? $argv[ 3 ] : "SysOmosRequest";

if( ! $sysomos->Login() ){
	exit( "Could not logged in to sysomos dashboard.\r\n" );
}

# add the default server (localhost)
$worker->addServer("127.0.0.1", 4730);
$worker->setId( md5( rand( 1, 19 ) . time() ) );

# add the worker function
$worker->addFunction( $function_name, "SendSysOmosRequest" );

while( $worker->work() );

function SendSysOmosRequest( $job )
{
	global $sysomos;

	$data = json_decode( $job->workload() );
	$sysomos->PrepareRequest( $data->TempFileName )->Request( $data->ModuleName, $data->Query, $data->DateStart, $data->DateEnd );
}
?>
