<?php
$serverName = "DESKTOP-VDMBV97";
$uid = "";
$pwd = "";
$databaseName = "AAB_CLNSA";
$connectionInfo = array( "UID"=>$uid,
                         "PWD"=>$pwd,
                         "Database"=>$databaseName);

$conn = sqlsrv_connect( $serverName, $connectionInfo);
 ?>
