```
     _                 _                                              _
 ___(_)_ __ ___  _ __ | | ___  _ __    _ __ ___  __ _ _   _  ___  ___| |_
/ __| | '_ ` _ \| '_ \| |/ _ \| '_ \  | '__/ _ \/ _` | | | |/ _ \/ __| __|
\__ \ | | | | | | |_) | | (_) | | | | | | |  __/ (_| | |_| |  __/\__ \ |_
|___/_|_| |_| |_| .__/|_|\___/|_| |_| |_|  \___|\__, |\__,_|\___||___/\__|
                |_|                                |_|
```

How to use:

```php
$req = new \Simplon\Request\Request();
    
$response = new \Simplon\Request\RequestResponse();


// set additional headers for request
$req->setRequestHeaders( [
	'User-Agent: Mozilla/5.0', 
	'Accept-Language: en-US,en'
] );	


// set request and response headers log file
// by default log file name -  headers_log.txt	
$req->setLog();							

$response = $req->get(URL, $data); 			// $data - array with variables for GET request


$location = $response->getHeader()->getLocation();	// if redirect

$type = $response->getHeader()->getContentType(); 	// return type of server response

if($response->getHeader()->isJson()) echo 'IsJson'; 	// check response type

$charset = $response->getHeader()->getCharset();	// return charset of response when Content-Type: text/html; charset=utf-8

$http_headers = $response->getHeader()->getHttpHeadersArray();
var_dump( $http_headers );				// show array of http headers of response

$html =  $response->getContent();
echo $html; 						// show server response without headers


```
