<?php


$url = 'https://simplex-financials.com/star-dental/reporting/daily_report';
//Encode the array into JSON.
$patient_details = array();
//The JSON data.
$data_string = json_encode($patient_details);

try{                                                                                                         

	$ch = curl_init($url);                                                                      
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		'Content-Type: application/json',                                                                                
		'Content-Length: ' . strlen($data_string))                                                                       
	);                                                                                                                     
	$result = curl_exec($ch);
	curl_close($ch);
}
catch(Exception $e)
{
	$response = "something went wrong";
	echo json_encode($response.' '.$e);
}

?>