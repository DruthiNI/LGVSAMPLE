<?php
// Define path to application directory
//error_reporting(E_ALL ^ E_NOTICE);
error_reporting(0);
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php'; 
define('SITEURL','https://'.$_SERVER['HTTP_HOST'].'/');

define('ADMIN_EMAIL' , 'niranjan.kumar@redmarkediting.com');

define('PUBLICURL',SITEURL.'public/');
require_once('library/wildbit-postmark/vendor/autoload.php');
require_once('library/wildbit-postmark/vendor/autoload.php');
require_once('library/postmark-inbound/lib/Postmark/Autoloader.php'); 
define('JS_REDIRECT','<script>parent.$.colorbox.close(); parent.window.location.reload();</script>'); 

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();
			
			
define('ADMIN_EMAIL' , 'niranjan.kumar@redmarkediting.com');
define('MANDARILL_KEY' , 'bJ0APu1q1CMbGD2ysPBrmg');
 function SendMandrilMail($message)
{
require_once 'Mandrill_Api/src/Mandrill.php';

$mandrill = new Mandrill('bJ0APu1q1CMbGD2ysPBrmg');
$async = false;
$ip_pool = '';
$result = $mandrill->messages->send($message, $async, $ip_pool);
return $result;				 	
}


function sendwhatsappmsg_newapi($phone,$parameter)
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
		/*echo '<pre>';
		print_r($data);
		echo '</pre>';*/
		$data_string = json_encode($data);
		
		/*echo '<pre>';
		print_r($data_string);
		echo '</pre>';*/

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
		
		/* echo '<pre>';
		print_r($res);
		echo '</pre>'; */
		//die;
		curl_close($ch);
	}
	return true;
}
?>
<script>
var SITEURL='<?=SITEURL?>';
</script>