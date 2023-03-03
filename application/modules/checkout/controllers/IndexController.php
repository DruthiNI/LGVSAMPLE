<?php
 class Checkout_IndexController extends Zend_Controller_Action
 {
     public function init()
     {
         /* Initialize action controller here */
 		//parent::init ();
 		//$this->_helper->ViewRenderer->setViewSuffix('php');
 		//Zend_Session::start();	
 		$this->db = Zend_Registry::get('db');
 		  
		 date_default_timezone_set("Asia/Kolkata");
 		//////////////////Flash message session ////////////////
         $this->_s = Zend_Registry::get('session');
         if ($this->_s->message) {
             $this->view->successmessage = $this->_s->message;
             $this->_s->message = NULL;
         }
 		   $this->view->leftsection='message';
		   $this->PriceQuoteObj = new Pricequote_Model_Pricequote();
		   $this->QueryObj = new Managequery_Model_Managequery();
		   
		  
		   $date = new Zend_Date();
		  
		  
		 $this->_helper->layout->disableLayout();
	    
     }
      public function indexAction()
     { 
	 if(!$_REQUEST['id'])
	 {
	   $this->_redirect('checkout/index/expire');	 
	 }
	 $id=$_REQUEST['id'];
	 $this->view->serviceInfo=$serviceInfo=$this->PriceQuoteObj->getCheckoutServiceInfo($id);
	 
	 if(!$serviceInfo['id'])
	 {
	   $this->_redirect('checkout/index/expire');	 
	 }
	 else if($serviceInfo['status']==1)
	 {
	   $this->_redirect('checkout/index/expire');	 
	 }
	 else if($serviceInfo['expiry_date'] < strtotime(date("m/d/Y")))
	 {
	   $this->_redirect('checkout/index/expire');	 
	 }
	  if($serviceInfo['website_id']==8)
	  {
	$this->view->packageData = $this->PriceQuoteObj->getServicePackageData($id); 
	$firstPackageInfo=$this->PriceQuoteObj->getfirstServicePackage($id);
	$packageId=$firstPackageInfo['id'];

	 if($_REQUEST['package'])
	 {
	 $packageId=base64_decode($_REQUEST['package']);
	 }
	 $this->view->packageId=$packageId;
	 
	 $this->view->servicePackageInfo=$this->PriceQuoteObj->getServicePackageInfo($packageId);
	 $this->view->packageMilestoneData=$this->PriceQuoteObj->getServicePackageMilestoneData($packageId);
	 $this->view->MilestoneInfo=$this->PriceQuoteObj->FetchPackageCurrentMilestone($packageId);

		
	  }
	  else
	  {
	 $this->view->MilestoneData = $this->PriceQuoteObj->getServiceMilestoneData($id);
	 $this->view->MilestoneInfo=$this->PriceQuoteObj->FetchCurrentMilestone($id);
	  }
	 
     }
	 
	 public function expireAction()
	 {
		 
	 }
 
 public function checkoutFormAction()
 {
	if(!$_REQUEST['id'])
	 {
	   $this->_redirect('checkout/index/expire');	 
	 }
	 $id=$_REQUEST['id'];
	 $this->view->amount=$_REQUEST['amount'];
	 $this->view->serviceInfo=$serviceInfo=$this->PriceQuoteObj->getServiceInfo($id);
	 $this->view->clientInfo=$clientInfo=$this->QueryObj->getViewQueryId($serviceInfo['ref_id']);


	 
 }
 
 public function checkoutproccessAction()
 {
if($this->getRequest()->getParam('id') and $this->getRequest()->getParam('id')!="")
{
$id=$this->getRequest()->getParam('id');
$this->view->serviceInfo=$serviceInfo=$this->PriceQuoteObj->getCheckoutServiceInfo($id);
$ref_id=$serviceInfo['ref_id'];;
$this->view->total_price=$total_price=$this->getRequest()->getParam('total_price');

$address=$serviceInfo['location'];
$city=$serviceInfo['city'];
//$state=$this->getRequest()->getParam('state');
//$zip_code=$this->getRequest()->getParam('zip_code');
//$country=$this->getRequest()->getParam('country');
$phone=$serviceInfo['phone'];
$quote_id=$this->getRequest()->getParam('quote_id');

$this->view->clientInfo=$clientInfo=$this->QueryObj->getViewQueryId($ref_id);
 
$body ='Dear '.$serviceInfo['name'].'<br/><br/>Your Submitted Details as Follows:<br/><br/>';
$body ='This is to inform you that a payment of an amount of INR '.$total_price.' was processed  on '.date('d/m/Y').'.<br/><br/>';
$body.='<strong>Client Name: </strong>'.$serviceInfo['name'].'<br/>';
$body.='<strong>Client Email: </strong>'.$serviceInfo['email_id'].'<br/>';
$body.='<strong>Phone: </strong>'.$phone.'<br/>';
$body.='<strong>Address: </strong>'.$address.'<br/>';
$body.='<strong>City: </strong>'.$city.'<br/>';
//$body.='<strong>State: </strong>'.$state.'<br/>';
//$body.='<strong>Country: </strong>'.$country.'<br/><br/>';
$body.='<strong>Order Details are as follows: </strong><br/><br/>';
$body.='<strong>Reference ID: </strong>'.$ref_id.'<br/>';
$body.='<strong>Quote Id: </strong>'.$quote_id.'<br/>';
$body.='<strong>Order Status: </strong>Pending<br/>';
$body.='<strong>Order Amount: </strong>INR '.$total_price.'<br/>';
$body.='<strong>Service: </strong>'.$serviceInfo['service_name'].'<br/>';
$body.='<strong>Consultant Name: </strong>'.$clientInfo['user_name'].'<br/>';
$body.='<strong>Description: </strong>'.$serviceInfo['order_summary'].'<br/>';
$body .='<br/> Best Regards, <br/>'.$serviceInfo['payment_website'].'<br/>';	

 $message = array(
        'html' => $body,
        'subject' => 'Payment Attempt Ref #'.$ref_id.' | '.$serviceInfo['payment_website'],
        'from_email' => $clientInfo['website_email'],
        'from_name' => $from_name,
        'to' => array(
               array(
					'email' => $clientInfo['email_id'],
					'name' => $clientInfo['user_name'],
					'type' => 'to'
            ),
			array(
				'email' => 'pnt.chd@gmail.com', 				
                'name' => 'Admin',
                'type' => 'bcc'
            )
        )
    );
	
	SendMandrilMail($message);
	
}

  
 }
 
 public function getPackageDetailsAction()
 {
	 $package_id=$this->getRequest()->getParam('package_id');
	$this->view->packageInfo=$packageInfo=$this->PriceQuoteObj->getServicePackageInfo($package_id); 
	$this->view->serviceInfo=$serviceInfo=$this->PriceQuoteObj->getCheckoutServiceInfo($packageInfo['service_id']);
 }
  
 }
 
