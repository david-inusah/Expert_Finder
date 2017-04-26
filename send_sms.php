<?php 

	if (isset($_REQUEST['to'])){
		$baseurl = 'http://127.0.0.1:13013/cgi-bin/sendsms?';
		
		$data =array(
			'username' => 'tester',
			'password' => 'foobar',
			'to' => $_REQUEST['to'],
			'from' => $_REQUEST['from'],
			'smsc' => $_REQUEST['smsc'],
			'text' => $_REQUEST['text']
		);
		
		$url = $baseurl.http_build_query($data);
		
		// Get cURL resource
		$curl = curl_init($url);
		
		// Send the request
		$response = curl_exec($curl);
		
		if(!$response){
		    die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
		}
		
		// Close request to clear up some resources
		curl_close($curl);
	}
?>
