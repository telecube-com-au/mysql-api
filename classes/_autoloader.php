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

// ensure this definition exists before running the script.
if(!defined('MAIN_INCLUDED'))
	exit("Not allowed here!");

$mapping = array(

	'MysqlApi\Auth' 	=> __DIR__ . '/auth.php',
	'MysqlApi\Config' 	=> __DIR__ . '/config.php',
	'MysqlApi\Common' 	=> __DIR__ . '/common.php',
	'MysqlApi\Db' 		=> __DIR__ . '/db.php',

);

spl_autoload_register(function ($class) use ($mapping) {
    if (isset($mapping[$class])) {
        require_once( $mapping[$class] );
    }
}, true);

