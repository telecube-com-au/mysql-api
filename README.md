# MySQL-API

An API connector to a MySQL DB instance for use in a distributed network

It can run on the same server as the databse or separately and has the ability to specify a readonly cluster of databases while writes go to the master.

The api endpoint expects 

- apikey - to authorise the request
- keyspace - the database name you want to connect to
- query - the query you want to run
- data - a json encoded array of values for the PDO prepared statement placeholders

To use the api send a http post|get request, for example;

### _GET example
```
https://node1.example.com/?apikey=abcde12345&keyspace=mydbname&query=select * from sometable limit 1;&data=[""]
```

### _POST example using curl
```php
$mysql_api_nodes = array("https://node1.example.com","https://node2.example.com");
$keyspace 	= "mydbname";
$query 		= "insert into sometable (field1, field2, field3) values (?,?,?);";	
$data 		= array($var1, $var2, $var3);
$result 	= mysql_api_query($mysql_api_nodes, '/', array('keyspace'=>$keyspace,'query'=>$query,'data'=>json_encode($data)));

// example curl function
function mysql_api_query($nodes, $path, $data){
	$apikey = "abcde12345"; // get the apikey in here somehow
	$http_auth_user = ""; // these details too
	$http_auth_pass = "";

	$data = array_merge(array('apikey'=>$apikey),$data);
	// loop through the nodes if we don';t get an ok status
	for ($i=0; $i < count($nodes); $i++) { 
	    $res = api_http($nodes[$i].$path,$data,$http_auth_user,$http_auth_pass,10,true);
	    if(isset($res->query_status) && $res->query_status == "OK"){
	       break; 
	    }
	}
	return $res;
}

function api_http($url,$postArr,$httpAuthUser='',$httpAuthPass='',$timeout=60,$json_decode=false){
    if(!isset($timeout)) $timeout=60;
    $curl = curl_init();
    $post = http_build_query($postArr);
    if(isset($referer)){
        curl_setopt ($curl, CURLOPT_REFERER, $referer);
    }
    curl_setopt ($curl, CURLOPT_URL, $url);
    curl_setopt ($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt ($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322)');
    curl_setopt ($curl, CURLOPT_HEADER, false);
    curl_setopt ($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, false);
	if($httpAuthUser != '' && $httpAuthPass != ''){
		curl_setopt ($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
		curl_setopt ($curl, CURLOPT_USERPWD, $httpAuthUser.':'.$httpAuthPass);
    }
	curl_setopt ($curl, CURLOPT_POST, true);
    curl_setopt ($curl, CURLOPT_POSTFIELDS, $post);
    curl_setopt ($curl, CURLOPT_HTTPHEADER,
        array("Content-type: application/x-www-form-urlencoded"));
    $html = curl_exec ($curl);
    curl_close ($curl);
    if($json_decode){
	    return json_decode($html);
    }else{
	    return $html;
    }
}
```