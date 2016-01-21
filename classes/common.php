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

class Common{

	public static function requested_keyspace(){
		if(isset($_REQUEST['keyspace']) && !empty($_REQUEST['keyspace'])){
			return $_REQUEST['keyspace'];
		}else{
			header("HTTP/1.1 400 Bad Request");
			echo json_encode(array("query_status"=>"ERROR","response"=>"You must set a keyspace!"));
			exit();
		}
	}

	public static function requested_query(){
		if(isset($_REQUEST['query']) && !empty($_REQUEST['query'])){
			return $_REQUEST['query'];
		}else{
			header("HTTP/1.1 400 Bad Request");
			echo json_encode(array("query_status"=>"ERROR","response"=>"You must set a query!"));
			exit();
		}
	}

	public static function requested_data(){
		if(isset($_REQUEST['data']) && !empty($_REQUEST['data'])){
			return json_decode($_REQUEST['data'], true);
		}else{
			header("HTTP/1.1 400 Bad Request");
			echo json_encode(array("query_status"=>"ERROR","response"=>"You must set query data!"));
			exit();
		}
	}





}

