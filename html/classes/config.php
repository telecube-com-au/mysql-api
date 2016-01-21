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

namespace MysqlApi;

// ensure this definition exists before running the script.
if(!defined('MAIN_INCLUDED'))
	exit("Not allowed here!");

class Config{

	// master detail
	private $master_db_host;
	private $master_db_port;
	private $master_db_user;
	private $master_db_pass;

	// readonly slaves
	private $db_slaves;

	// apikey
	private $apikey;

	// http auth detail
	private $http_auth_enable;
	private $http_auth_realm;
	private $http_auth_user;
	private $http_auth_pass;

	public function __construct(){
		// modify this to point to your config script
		// see mysql_api_config.inc.example.php for details
		include("/opt/mysql_api_config.inc.php");

		// master detail
    	$this->master_db_host 	= $master_db_host; 
    	$this->master_db_port 	= $master_db_port; 
    	$this->master_db_user 	= $master_db_user; 
    	$this->master_db_pass 	= $master_db_pass; 

		// readonly slaves
		$this->db_slaves 		= $db_slaves;

		// apikey
		$this->apikey 			= $apikey;

		// http auth detail
		$this->http_auth_enable = $http_auth_enable;
		$this->http_auth_realm 	= $http_auth_realm;
		$this->http_auth_user 	= $http_auth_user;
		$this->http_auth_pass 	= $http_auth_pass;

    }       

	public function get($varname){
		return isset($this->$varname) ? $this->$varname : false;
	}








}

