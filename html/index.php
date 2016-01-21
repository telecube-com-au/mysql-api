<?php
/**
* MySQL API : API application for connecting to MySQL from distributed systems
*
* Copyright (c) 2015 Telecube Pty Ltd
* All Rights Reserved.
*
* This software is released under the terms of the GNU Lesser General Public License v2.1
* A copy of which is available from http://www.gnu.org/copyleft/lesser.html
*
* Written for PHP 5.3.3, should work with older PHP 5.x versions.
*
* @package MySQL-API
* @version Since v1.0.0
*/

// this definition can be checked in the required scripts to ensure they aren't called directly.
define('MAIN_INCLUDED', 1);

require_once(__DIR__."/classes/_autoloader.php");
use MysqlApi\Auth;
use MysqlApi\Common;
use MysqlApi\Config;
use MysqlApi\Db;

$Auth 	= new Auth;
$Common = new Common;
$Config = new Config;
$Db 	= new Db;

// check http auth credentials
if($Config->get("http_auth_enable") === true){
	$Auth->api_http();
}

// check the apikey is set and valid
$Auth->api_check_key();

$query_start = microtime(true);

// get the keyspace/database
$keyspace 		= $Common->requested_keyspace();
// get the query
$query 			= $Common->requested_query();
$query 			= trim($query);
// get the data
$data 			= $Common->requested_data();

// pdo db connection
try{
	$dbPDO = new PDO('mysql:dbname='.$Common->requested_keyspace().';host='.$Config->get("master_db_host").';port='.$Config->get("master_db_port"), $Config->get("master_db_user"), $Config->get("master_db_pass"));
} catch(PDOException $ex){
	exit( 'Connection failed: ' . $ex->getMessage() );
}
$dbPDO->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

// test the query type
$query_type = substr(strtolower($query), 0, 6);
$response = $Db->pdo_query($query,$data,$dbPDO,$query_type);

$query_status = "OK";

$query_end = microtime(true);
$query_time = $query_end - $query_start;

$response_length = strlen(json_encode($response));

// echo the response
echo json_encode(array("query_status"=>$query_status,"query_time"=>$query_time,"response_length"=>$response_length,"response"=>$response));

?>