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

class Db{

	public static function pdo_query($q,$data=array(),$link){
		
		$rq_type = substr(strtolower($q), 0, 6);

	    try{
			$res = array();
	    	
	    	$rec = $link->prepare($q);  
	    	
	    	if($rq_type == "select"){
	    		$rec->execute($data); 
				$rec->setFetchMode(\PDO::FETCH_ASSOC);  
				while($rs = $rec->fetch()){
					$res[] = $rs;
				}
	    	}else{
	    		$res = $rec->execute($data); 
	    	}


			$rec->closeCursor();
			return $res;

	    }catch(\PDOException $ex){
			return $ex->getMessage();
	    } 
	}



}

