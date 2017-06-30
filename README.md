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

$response = $req->get(URL, $data); // $data - array with variables for GET request

$location = $response->getHeader()->getLocation();// if redirect

$type = $response->getHeader()->getContentType(); // return type of server response

if($response->getHeader()->isJson()) echo 'IsJson'; // check response type

$html =  $response->getContent();
echo $html; // show erver response without headers
```
