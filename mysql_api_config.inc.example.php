<?php
/**
* MySQL API : API application for connecting to MySQL from distributed systems
*
* a config file needs to be placed in a folder outside of the web root
* by default the script looks for it at /opt/mysql_api_config.inc.php
* you can change that to wherever you want by updating the include in the config class
* /classes/config.php
* 	=> include("/opt/mysql_api_config.inc.php");
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

// ensure this definition exists before running the script.
if(!defined('MAIN_INCLUDED'))
	exit("Not allowed here!");

// the master db for write/read
$master_db_host = "";
$master_db_port = "";
$master_db_user = "";
$master_db_pass = "";

// readonly slaves
$db_slaves = array(
		// readonly slave 1
		array(
				"db_host" => "",
				"db_port" => "",
				"db_user" => "",
				"db_pass" => "",
			),
		// readonly slave 2
		array(
				"db_host" => "",
				"db_port" => "",
				"db_user" => "",
				"db_pass" => "",
			),
		// etc ..
	);

// set strong keys here - a good key generator can be found at: https://www.grc.com/passwords.htm
$apikey 			= "";

// http basic auth settings
$http_auth_enable 	= true; // enable|disable http auth by setting true|false
$http_auth_realm 	= "MySQL API";
$http_auth_user 	= "";
$http_auth_pass 	= "";




