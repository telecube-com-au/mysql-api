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

class Auth{

	public static function api_http(){
		global $Config;

		if (!isset($_SERVER['PHP_AUTH_USER'])) {
		    header('WWW-Authenticate: Basic realm="'.$Config->get('http_auth_realm').'"');
		    header('HTTP/1.0 401 Unauthorized');
		    echo 'Access to this site needs to be authenticated!';
		    exit;
		} else {
		    // check the username and password are valid
			$http_auth_user = $Config->get('http_auth_user');
			$http_auth_pass = $Config->get('http_auth_pass');
			if($http_auth_user && $http_auth_pass){
				if($_SERVER['PHP_AUTH_USER'] == $Config->get('http_auth_user') && $_SERVER['PHP_AUTH_PW'] == $Config->get('http_auth_pass')){
					return true;
				}else{
					// user/pass don't match so reject the request
				    header('WWW-Authenticate: Basic realm="'.$Config->get('http_auth_realm').'"');
					header('HTTP/1.1 401 Unauthorized');
				    echo 'Access denied!!';	
			    	exit;			
				}
			}else{
				// no user/pass so reject the request
			    header('WWW-Authenticate: Basic realm="'.$Config->get('http_auth_realm').'"');
				header('HTTP/1.1 401 Unauthorized');
			    echo 'Access denied!!!';	
			    exit;			
			}
		}		
	}

	public static function api_check_key(){
		global $Config;

		// get the apikey from config
		$apikey = $Config->get("apikey");

		// check the api key
		if(!isset($_REQUEST["apikey"]) || $_REQUEST["apikey"] != $apikey){
			header('HTTP/1.0 401 Unauthorized');
			echo json_encode(array("query_status"=>"ERROR","response"=>"Unauthorized request."));
			exit();
		}
	}



	
}
