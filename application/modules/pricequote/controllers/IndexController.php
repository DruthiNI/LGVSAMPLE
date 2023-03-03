<?php
    /*
     * class Template_IndexController
     * Date: 15-April-2013
     * Developer: Niranjan Singh
     * Modified By: Niranjan Singh
     * 
     */
 class pricequote_IndexController extends Zend_Controller_Action {
    /*
     * Date: 15-April-2013
     * Developer: Niranjan Singh
     * Modified By: Niranjan Singh
     * 
     */
    public function init() {
        parent::init();
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
		//////////////////Flash message session ////////////////
        $this->_s = Zend_Registry::get('session');
        if ($this->_s->message) {
            $this->view->successmessage = $this->_s->message;
            $this->_s->message = NULL;
        }
		 date_default_timezone_set("Asia/Kolkata");
		/////check User type
         $this->view->leftsection='manageuser';
          $this->teamsObj = new Teams_Model_Teams();
		  $this->userObj=$userObj = new Manageuser_Model_Manageuser();
		  $this->template = new Template_Model_Template();
		  $this->QueryObj = new Managequery_Model_Managequery();
		  $this->QuoteObj = new Managequote_Model_Managequote();
		  $this->PriceQuoteObj = new Pricequote_Model_Pricequote();
		  $this->chatObj = new Managequote_Model_Chat();
  		   if(!isset($userInfo->id)){ 
            $this->_redirect('dashboard/');
         }
		  $this->view->userInfo = $this->userInfo =$userObj->getUserId($userInfo->id);
		  if($this->getRequest()->getParam('ajaxify')==1)
	   {
		 $this->_helper->layout->disableLayout();
	   }
     }
     /*
     * To list the Templates 
     * Date: 14-OCT-2015
     * Developer: Niranjan Singh
     * Modified By: Niranjan Singh
     * 
     */
 
public function userPriceQuoteAction()
{
$this->_helper->layout()->disableLayout();
$this->view->ref_id=$ref_id =$this->getRequest()->getParam('ref_id');
$this->view->QueryInfo=$QueryInfo = $this->QueryObj->getViewQueryId($ref_id);
$this->view->quoteData=$this->PriceQuoteObj->getPriceQuoteByRef($ref_id);

$this->view->quoteServiceData=$this->template->getServiceThisWebsite($QueryInfo['website_id']);
 }

public function submitPriceQuoteAction()
{
   
 $ref_id=$this->getRequest()->getParam('ref_id');
 $quote_service_id=$this->getRequest()->getParam('quote_service_id');
 $total_price=$this->getRequest()->getParam('total_price');
 $no_of_milestone=$this->getRequest()->getParam('no_of_milestone');
 $milestone_name=$this->getRequest()->getParam('milestone_name');
 $milestone_price=$this->getRequest()->getParam('milestone_price');
 $milestone_eta=$this->getRequest()->getParam('milestone_eta');
 $milestone_remark=$this->getRequest()->getParam('milestone_remark');
 
 $discount_type=$this->getRequest()->getParam('discount_type');
 $discount_value=$this->getRequest()->getParam('discount_value');
 $coupon_code=$this->getRequest()->getParam('coupon_code');
 $payment_website=$this->getRequest()->getParam('payment_website');
 $expiry_date=$this->getRequest()->getParam('expiry_date');
 $order_summary=$this->getRequest()->getParam('order_summary');
 $extraFiles=$this->getRequest()->getParam('extraFiles');
 
 if($this->getRequest()->getParam('file_permission'))
 {
 $file_permission=$this->getRequest()->getParam('file_permission');
 }
 else
 {
$file_permission="No";	 
 }
   
  $templateQuoteInfo=$this->template->getQuoteTemplateInfo($quote_service_id);  
		//////////////////////////////////////////////////////////////////////////
		$locatie = 'public/UploadFolder/'; 
            $time=time();
  	        $upload_file=array();
  		 	 $count= count($_FILES['extraFiles']['name']['upload_file']); 
			if ($count!="" && $count > 0) {
			for($i=0;$i<$count;$i++){
				$doc.=$_FILES['extraFiles']['name']['upload_file'][$i].',';
				$base_name=time().'_'.basename(preg_replace("/[^a-zA-Z0-9.]/","",$_FILES['extraFiles']['name']['upload_file'][$i]));
 				$folder = $locatie.$base_name; 
			if(move_uploaded_file($_FILES['extraFiles']['tmp_name']['upload_file'][$i], $folder)){
				$j=$i+1;
				$file_title_name=$extraFiles['file_title_name'][$i];
				$upload_file[]=array("filename"=>($file_title_name)?$file_title_name:$base_name,"file_path"=>$base_name);
   			 }
			}
            }
 //print_r($upload_file);die;
 $quoteInfo=$this->PriceQuoteObj->checkExistPriceQuoteService($ref_id,$quote_service_id);
 if(!$quoteInfo['id'])
 {
$array=array(
"ref_id"=>$ref_id,
"user_id"=>$this->userInfo->id,
"quote_service_id"=>$quote_service_id,
"service_name"=>$templateQuoteInfo['quote_service_name'],
"total_price"=>$total_price,
"milestone"=>$no_of_milestone,
"discount_type"=>$discount_type,
"discount_value"=>$discount_value,
"coupon_code"=>$coupon_code,
"payment_website"=>$payment_website,
"expiry_date"=>strtotime($expiry_date),
"order_summary"=>$order_summary,
"document_file"=>($upload_file)?json_encode($upload_file):"",
"file_permission"=>$file_permission,
"templateUploadFile"=>$templateQuoteInfo['upload_file'],
"status"=>0,
"created_date"=>time(),
);
$insertid=$this->PriceQuoteObj->insertPriceQuote($array);

for($i=0; $i<count($milestone_price);$i++)
{
$mArray=array("ref_id"=>$ref_id,"service_id"=>$insertid,"milestone_name"=>$milestone_name[$i],"milestone_price"=>$milestone_price[$i],"milestone_eta"=>strtotime($milestone_eta[$i]),"milestone_remark"=>$milestone_remark[$i],"m_date"=>time());
$this->PriceQuoteObj->insertMilestone($mArray);	
}
 echo $insertid;die;  
 }
die;	
}
	 
	 
function getUserCategoryType($category)
{
if($this->userInfo->category=='Sales')
{
$not_mess='Sales';	
}
else
{
$not_mess='CRM';		
}	
}	

function getServiceDetailsAction()
{
	$this->_helper->layout()->disableLayout();
$service_id=$this->getRequest()->getParam('service_id');
if($service_id)
{
$this->view->serviceInfo=$this->PriceQuoteObj->getServiceInfo($service_id);	
$this->view->serviceMilestoneData=$this->PriceQuoteObj->getServiceMilestoneData($service_id);	
}
	
} 

function getServicePackageAction()
{
$this->_helper->layout()->disableLayout();
$service_id=$this->getRequest()->getParam('service_id');
if($service_id)
{
$this->view->serviceInfo=$this->PriceQuoteObj->getServiceInfo($service_id);	
$this->view->servicePackageData=$this->PriceQuoteObj->getServicePackageData($service_id);	
}
	
}
public function getServicePackageDetailsAction()
{
$this->_helper->layout()->disableLayout();
$package_id=$this->getRequest()->getParam('package_id');
if($package_id)
{
$this->view->servicePackageInfo=$this->PriceQuoteObj->getServicePackageInfo($package_id);	
$this->view->servicePackageMilestoneData=$this->PriceQuoteObj->getServicePackageMilestoneData($package_id);	
}	
}

function changeMilestoneStatusAction()
{
$service_id=$this->getRequest()->getParam('service_id');
$milest_id=$this->getRequest()->getParam('milest_id');	
$status=$this->getRequest()->getParam('status');	
if($service_id!="" and $milest_id!="")
{
	$this->PriceQuoteObj->MilestoneUpdate(array("status"=>$status),$milest_id);
	
	$pendingMilestone = $this->PriceQuoteObj->FetchPendingMilestone($service_id);
	if(count($pendingMilestone) < 1 )
	{
       $this->PriceQuoteObj->ServiceUpdateStatus(array("status"=>1),$service_id);
	   $mainstatus='<span class="label label-success">Paid</span>';
    }
	else
    {
       $this->PriceQuoteObj->ServiceUpdateStatus(array("status"=>0),$service_id);
       $mainstatus='<span class="label label-warning">Pending</span>';	   
    }
	echo $mainstatus;die;
}


die;
}

public function deleteMilestoneAction()
{
$milest_id=$this->getRequest()->getParam('milest_id');
if($milest_id)
{
$this->view->serviceInfo=$this->PriceQuoteObj->deleteMilestone($milest_id);	
echo $milest_id;
}	

die;
}

function addMilestoneAction()
{
$service_id=$this->getRequest()->getParam('service_id');
$milestone_name=$this->getRequest()->getParam('milestone_name');
$milestone_price=$this->getRequest()->getParam('milestone_price');
$milestone_eta=$this->getRequest()->getParam('milestone_eta');
$milestone_remark=$this->getRequest()->getParam('milestone_remark');

$ref_id=$this->getRequest()->getParam('ref_id');
if($service_id!="" and $milestone_price!="" and $ref_id!="")
{
$mArray=array("ref_id"=>$ref_id,"service_id"=>$service_id,"milestone_name"=>$milestone_name,"milestone_price"=>$milestone_price,"milestone_eta"=>strtotime($milestone_eta),"milestone_remark"=>$milestone_remark,"m_date"=>time());
echo $insert=$this->PriceQuoteObj->insertMilestone($mArray);	

}	
	
	die;
}
public function updateServiceAmountAction()
{
$service_id=$this->getRequest()->getParam('service_id');
$service_amount=$this->getRequest()->getParam('service_amount');
if($service_id!="" and $service_amount!="")
{
$sArray=array("total_price"=>$service_amount);
 $this->PriceQuoteObj->ServiceUpdateStatus($sArray,$service_id);	
echo 1;die;
}

die;	
}

public function deleteServiceAction()
{
$service_id=$this->getRequest()->getParam('service_id');
if($service_id)
{
$this->view->serviceInfo=$this->PriceQuoteObj->deleteService($service_id);	
echo $service_id;
}	

die;	
}

public function updateMilestoneAmountAction()
{
$milest_id=$this->getRequest()->getParam('milest_id');
$milestone_price=$this->getRequest()->getParam('milestone_price');
if($milest_id!="" and $milestone_price!="")
{
$sArray=array("milestone_price"=>$milestone_price);
 $this->PriceQuoteObj->MilestoneUpdate($sArray,$milest_id);	
echo 1;die;
}

die;	
}

public function updateServiceWorkscopeAction()
{
$service_id=$this->getRequest()->getParam('service_id');
$work_scope=$this->getRequest()->getParam('work_scope');
if($service_id!="" and $work_scope!="")
{
$sArray=array("order_summary"=>$work_scope);
 $this->PriceQuoteObj->ServiceUpdateStatus($sArray,$service_id);	
echo 1;die;
}

die;	
}

public function updateServiceLinkExpirydateAction()
{

if($this->getRequest()->getParam('service_id')!="" and $this->getRequest()->getParam('expiry_date')!="")
{
$service_id=$this->getRequest()->getParam('service_id');
$expiry_date=strtotime($this->getRequest()->getParam('expiry_date'));	
$sArray=array("expiry_date"=>$expiry_date);
 $this->PriceQuoteObj->ServiceUpdateStatus($sArray,$service_id);	
echo date("d M, Y",$expiry_date);die;
}

die;	
}

public function submitPackagePriceQuoteAction()
{
 $ref_id=$this->getRequest()->getParam('ref_id');
 $quote_service_id=$this->getRequest()->getParam('quote_service_id');
 $discount_type=$this->getRequest()->getParam('discount_type');
 $discount_value=$this->getRequest()->getParam('discount_value');
 $coupon_code=$this->getRequest()->getParam('coupon_code');
 $payment_website=$this->getRequest()->getParam('payment_website');
 $expiry_date=$this->getRequest()->getParam('expiry_date');
 $extraFiles=$this->getRequest()->getParam('extraFiles');	
 $packageData=$this->getRequest()->getParam('packageData');	
 
 $templateQuoteInfo=$this->template->getQuoteTemplateInfo($quote_service_id);
 
 $locatie = 'public/UploadFolder/'; 
            $time=time();
  	        $upload_file=array();
  		 	 $count= count($_FILES['extraFiles']['name']['upload_file']); 
			if ($count!="" && $count > 0) {
			for($i=0;$i<$count;$i++){
				$doc.=$_FILES['extraFiles']['name']['upload_file'][$i].',';
				$base_name=time().'_'.basename(preg_replace("/[^a-zA-Z0-9.]/","",$_FILES['extraFiles']['name']['upload_file'][$i]));
 				$folder = $locatie.$base_name; 
			if(move_uploaded_file($_FILES['extraFiles']['tmp_name']['upload_file'][$i], $folder)){
				$j=$i+1;
				$file_title_name=$extraFiles['file_title_name'][$i];
				$upload_file[]=array("filename"=>($file_title_name)?$file_title_name:$base_name,"file_path"=>$base_name);
   			 }
			}
            }
 //////////////////////////////////////////////////////////////////////////////////////////////
 $quoteInfo=$this->PriceQuoteObj->checkExistPriceQuoteService($ref_id,$quote_service_id);
 if(!$quoteInfo['id'])
 {
$array=array(
"ref_id"=>$ref_id,
"user_id"=>$this->userInfo->id,
"quote_service_id"=>$quote_service_id,
"service_name"=>$templateQuoteInfo['quote_service_name'],
"discount_type"=>$discount_type,
"discount_value"=>$discount_value,
"coupon_code"=>$coupon_code,
"payment_website"=>$payment_website,
"expiry_date"=>strtotime($expiry_date),
"document_file"=>($upload_file)?json_encode($upload_file):"",
"status"=>0,
"created_date"=>time(),
);
$insertid=$this->PriceQuoteObj->insertPriceQuote($array);

if($insertid)
{
 foreach($packageData['package_name'] as $key=>$value)
 {
 
$arrayData=array(
"service_id"=>$insertid,
"ref_id"=>$ref_id,
"package_name"=>$packageData['package_name'][$key],
"package_price"=>$packageData['package_price'][$key],
"p_milestone"=>$packageData['no_of_milestone'][$key],
"service_scope"=>$packageData['service_scope'][$key],
"templateUploadFile"=>$packageData['templateUploadFile'][$key],
"p_file_permission"=>($packageData['file_permission'][$key])?$packageData['file_permission'][$key]:"No",
); 
$insertPid=$this->PriceQuoteObj->insertPriceQuotePackage($arrayData);
if($insertPid)
{
	foreach($packageData['milestoneData'][$key]['milestone_name'] as $key2=>$value2)
	{
	$milestoneData=array(
	"ref_id"=>$ref_id,
	"service_id"=>$insertid,
	"package_id"=>$insertPid,
	"milestone_name"=>$packageData['milestoneData'][$key]['milestone_name'][$key2],
	"milestone_price"=>$packageData['milestoneData'][$key]['milestone_price'][$key2],
	"milestone_eta"=>strtotime($packageData['milestoneData'][$key]['milestone_eta'][$key2]),
	"milestone_remark"=>$packageData['milestoneData'][$key]['milestone_remark'][$key2],
	"m_date"=>time()
	);
	$this->PriceQuoteObj->insertPriceQuotePackageMilestone($milestoneData);
	  
	}	
}
 }	
 echo $insertid; die;
}
 }
die;	
 ///////////////////////////////////////////////////////////////////////////////////////////////
 
}

public function changePackageMilestoneStatusAction()
{
$service_id=$this->getRequest()->getParam('service_id');
$package_id=$this->getRequest()->getParam('package_id');
$milest_id=$this->getRequest()->getParam('milest_id');	
$status=$this->getRequest()->getParam('status');	
if($service_id!="" and $milest_id!="")
{
	$this->PriceQuoteObj->PackageMilestoneUpdate(array("status"=>$status),$milest_id);
	
	$pendingMilestone = $this->PriceQuoteObj->FetchPendingPackageMilestone($package_id);
	if(count($pendingMilestone) < 1 )
	{
       $this->PriceQuoteObj->ServiceUpdateStatus(array("status"=>1),$service_id);
	   $mainstatus='<span class="label label-success">Paid</span>';
    }
	else
    {
       $this->PriceQuoteObj->ServiceUpdateStatus(array("status"=>0),$service_id);
       $mainstatus='<span class="label label-warning">Pending</span>';	   
    }
	echo $mainstatus;die;
}

die;	
}
}