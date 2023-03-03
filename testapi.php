<?php
/*function sendwhatsappmsg_newapi($phone,$parameter)
{	
	if($phone !="")
	{
		$key='xE6PPbDYMhpZupdTtJ75c2QvY';
		$url='https://conversations.messagebird.com/v1/send';
		
		$data = array(
		    "to"=>"+91".$phone,
		    "type"=>"hsm",
		    "from"=>"7fb143cb-9b0d-40b3-821c-20a11c2cf02e",
		    "content"=> array(
                'hsm'=>array(
                    'namespace'=> 'ae7639b8_6d21_4ac3_ae51_a006aae361cd',
                    'language'=>array('policy'=> 'deterministic','code'=>'en_US'),
		            
		            'templateName'=> 'instacrm',
		            'params'=>[array('default'=>"$parameter")],
                ),
	        ),
		);
		 
		$data_string = json_encode($data);
		
		 

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 360);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		  "Authorization: AccessKey $key",
		  "Content-Type: application/json; charset=utf-8")
		);
		$res=curl_exec($ch);
		echo '<pre>';
		print_r($res);
		echo '</pre>';
		//die;
		curl_close($ch);
	}
	return true;
}

$phone = 9899817418;
//$phone = 9873080833;
$parameter = 'Sumit Kumar';
sendwhatsappmsg_newapi($phone,$parameter);
*/
?>