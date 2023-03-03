<?php
    /*
     * class Template_IndexController
     * Date: 15-April-2013
     * Developer: Niranjan Singh
     * Modified By: Niranjan Singh
     * 
     */
 class template_IndexController extends Zend_Controller_Action {
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
		/////check User type
         $this->view->leftsection='manageuser';
          $this->teamsObj = new Teams_Model_Teams();
		  $this->userObj=$userObj = new Manageuser_Model_Manageuser();
		  $this->template = new Template_Model_Template();
		  $this->QueryObj = new Managequery_Model_Managequery();
		  $this->tagsObj = new Tags_Model_Tags();
		  
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
   function emailtemplateAction()
 	 { 
	 $this->view->tagData=$this->tagsObj->getAllPrimaryTags();
	 $this->view->userData=$this->userObj->fetchActiveUsers('Lead Validator');
	 
	 if($this->userInfo->user_type=='admin')
	 {
	 $this->view->templateData=$this->template->getAllEmailTemplate();	 
	 $this->view->websiteData=$this->template->getAllWebsite();
	 $this->view->serviceData=$this->template->getAllService();	 
	 }
	 else if($this->userInfo->user_type=='Lead Validator')
	 {
		$whereStr = " em.category LIKE '%".$this->userInfo->category."%'";
		 if($whereStr!="")
			   {
	         $whereStr .= " AND  assign_user ='".$this->userInfo->id."'";
		       }
			 else
		     {
		     $whereStr .= "  assign_user ='".$this->userInfo->id."'";
		     }
		$this->view->templateData=$this->template->getAllEmailTemplate($whereStr); 
$this->view->websiteData=$this->QueryObj->fetchAllCategoryWebsite($this->userInfo->category);		
	 }
	 else
	 {
	$whereStr = " em.category LIKE '%".$this->userInfo->category."%'";	 
	$this->view->templateData=$this->template->getAllEmailTemplate($whereStr); 
	$this->view->websiteData=$this->QueryObj->fetchAllCategoryWebsite($this->userInfo->category);
	$this->view->serviceData=$this->template->getAllCategoryService($this->userInfo->category);
	 }

	  
	 
	 
	 
		 
		 
     }
public function saveemailTemplateAction()
{
$temp_id=$this->getRequest()->getParam('temp_id');
$category=$this->getRequest()->getParam('category');
$template_name=$this->getRequest()->getParam('template_name');
$website_id=$this->getRequest()->getParam('website_id');
$mail_subject=$this->getRequest()->getParam('mail_subject');
$mail_body=$this->getRequest()->getParam('mail_body');



if($temp_id)
{
	$array=array(
"added_by"=>$this->userInfo->id,
"category"=>$category,
"template_name"=>$template_name,
"website_id"=>$website_id,
"mail_subject"=>$mail_subject,
"mail_body"=>$mail_body,
);
	$this->template->updateEmail($array,$temp_id);
}
else
{
$array=array(
"added_by"=>$this->userInfo->id,
"category"=>$category,
"template_name"=>$template_name,
"website_id"=>$website_id,
"mail_subject"=>$mail_subject,
"mail_body"=>$mail_body,
"assign_user"=>$this->userInfo->id,
"created_date"=>time(),
);
$this->template->addEmail($array);	
}

echo $this->emailtemplateAction();
die;	
}


public function getTemplateInfoAction() 
{
$id=$this->getRequest()->getParam('id'); 
$table=$this->getRequest()->getParam('table'); 
$templateInf=$this->template->getTemplateInfo($id,$table);
echo json_encode($templateInf);die;
}

function smstemplateAction()
 	 { 
	 
	 if($this->userInfo->user_type=='admin')
	 {
	 $this->view->templateData=$this->template->getAllSMSTemplate();	 
	 $this->view->websiteData=$this->template->getAllWebsite();
	 $this->view->serviceData=$this->template->getAllService();	 
	 }
	 else
	 {
	$whereStr = " sm.category LIKE '%".$this->userInfo->category."%'";	 
	$this->view->templateData=$this->template->getAllSMSTemplate($whereStr); 
	$this->view->websiteData=$this->QueryObj->fetchAllCategoryWebsite($this->userInfo->category);
	$this->view->serviceData=$this->template->getAllCategoryService($this->userInfo->category);
	 }
  
		 
     }	
 
public function savesmsTemplateAction()
{
$temp_id=$this->getRequest()->getParam('temp_id');
$category=$this->getRequest()->getParam('category');
$template_name=$this->getRequest()->getParam('template_name');
$service_id=$this->getRequest()->getParam('service_id');
$website_id=$this->getRequest()->getParam('website_id');
$sms_body=$this->getRequest()->getParam('sms_body');

$array=array(
"added_by"=>$this->userInfo->id,
"category"=>$category,
"template_name"=>$template_name,
"service_id"=>$service_id,
"website_id"=>$website_id,
"sms_body"=>$sms_body,
"created_date"=>time(),
);
if($temp_id)
{
	$this->template->updateSMS($array,$temp_id);
}
else
{
$this->template->addSMS($array);	
}

echo $this->smstemplateAction();
die;	
}

function deleteTemplateInfoAction()
{
$id=$this->getRequest()->getParam('id'); 
$table=$this->getRequest()->getParam('table'); 
$templateInf=$this->template->DeleteEmail($id,$table);
echo json_encode(array("id"=>$id,"message"=>"Template deleted successfully"));die; 
}


//////////////////////////////////Quote Template///////////////////////////////////////////
public function quotemplateAction()
{
$this->view->templateQuoteData=$this->template->getAllQuoteTemplate();	 
	
}

public function addQuoteTemplateAction()
{
$this->view->websiteData=$this->template->getAllWebsite();	
 
if($this->getRequest()->isPost('submitBtn') and $this->getRequest()->getParam('submitBtn')!="")
 			{
			  $website_id=$this->getRequest()->getParam('website_id');
			  $quote_service_name=$this->getRequest()->getParam('quote_service_name');
			 
			  /////////////////////////////////////////////////////////////////////
			   $locatie = 'public/UploadFolder/'; 
			   
			   if($website_id==8)
			   {
				 $packageArray=array();
				 $packageData=$this->getRequest()->getParam('packageData');
				 foreach($packageData['package_name'] as $key=>$value)
				 {
				$file_title_name="";	 
				$file_path="";	 
				//////////////////////////////////////////////////////
                $base_name=time().'_'.basename(preg_replace("/[^a-zA-Z0-9.]/","",$_FILES['packageData']['name']['upload_file'][$key]));
 				$folder = $locatie.$base_name; 
			if(move_uploaded_file($_FILES['packageData']['tmp_name']['upload_file'][$key], $folder)){
				$file_title_name=($packageData['file_title_name'][$key])?$packageData['file_title_name'][$key]:$base_name;
				$file_path=$base_name;
   			 }
               //////////////////////////////////////////////////////				
					 
				$packageArray[]=array(
				"package_name"=>$packageData['package_name'][$key],
				"service_scope"=>$packageData['service_scope'][$key],
				"file_title_name"=>$file_title_name,
				"file_path"=>$file_path,
				);	 
				 }
				 //echo "<pre>";
				// print_r($packageArray);die;
				 $array=array("website_id"=>$website_id,"quote_service_name"=>$quote_service_name,"service_package"=>json_encode($packageArray),"created_date"=>time());
			   }
			   else
			   {
			  $service_scope=$this->getRequest()->getParam('service_scope');
			  $file_title_name=$this->getRequest()->getParam('file_title_name');
			  $upload_file="";
			  $fileArray=array();
			if ($_FILES['upload_file']['name']) {
				$upload_file= $_FILES['upload_file']['name']; 
				$base_name=time().'_'.basename(preg_replace("/[^a-zA-Z0-9.]/","",$_FILES['upload_file']['name']));
 				$folder = $locatie.$base_name; 
			if(move_uploaded_file($_FILES['upload_file']['tmp_name'], $folder)){
  				$upload_file=$base_name;
               $fileArray=array("file_title_name"=>($file_title_name)?$file_title_name:$upload_file,"upload_file"=>$upload_file);
   			 }
             }
			  $array=array("website_id"=>$website_id,"quote_service_name"=>$quote_service_name,"service_scope"=>$service_scope,"upload_file"=>json_encode($fileArray),"created_date"=>time());
			   }
			  
			  $this->template->insertQuoteTemplate($array);
			  $this->_s->message ='<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Quote template added successfully.</div>';
            $this->_redirect('template/quotemplate');	
			}
}

public function editQuoteTemplateAction()
{

$this->view->websiteData=$this->template->getAllWebsite();	

if($this->getRequest()->getParam('id')!="")
{
$id=$this->getRequest()->getParam('id');
$this->view->templateQuoteInfo=$templateQuoteInfo=$this->template->getQuoteTemplateInfo($id);	
}


if($this->getRequest()->isPost('submitBtn') and $this->getRequest()->getParam('submitBtn')!="")
 			{
			  $website_id=$this->getRequest()->getParam('website_id');
			  $quote_service_name=$this->getRequest()->getParam('quote_service_name');
			 
			  $id=$this->getRequest()->getParam('id');
			  
			  if($website_id==8)
			   {
				 $packageArray=array();
				 $packageData=$this->getRequest()->getParam('packageData');
				 foreach($packageData['package_name'] as $key=>$value)
				 {
				$file_title_name="";	 
				$file_path="";	 
				//////////////////////////////////////////////////////
				if($_FILES['packageData']['name']['upload_file'][$key])
				{
                $base_name=time().'_'.basename(preg_replace("/[^a-zA-Z0-9.]/","",$_FILES['packageData']['name']['upload_file'][$key]));
 				$folder = $locatie.$base_name; 
			if(move_uploaded_file($_FILES['packageData']['tmp_name']['upload_file'][$key], $folder)){
				$file_title_name=($packageData['file_title_name'][$key])?$packageData['file_title_name'][$key]:$base_name;
				$file_path=$base_name;
   			 }
			   }
			   else
			   {
				$file_title_name=$packageData['file_title_name'][$key];
				$file_path=$packageData['old_file_name'][$key];   
			   }
               //////////////////////////////////////////////////////				
					 
				$packageArray[]=array(
				"package_name"=>$packageData['package_name'][$key],
				"service_scope"=>$packageData['service_scope'][$key],
				"file_title_name"=>$file_title_name,
				"file_path"=>$file_path,
				);	 
				 }
				 //echo "<pre>";
				 //print_r($packageArray);die;
				 $array=array("website_id"=>$website_id,"quote_service_name"=>$quote_service_name,"service_package"=>json_encode($packageArray),"created_date"=>time());   
			   }
			   else
			   {
			  $service_scope=$this->getRequest()->getParam('service_scope');
			  $file_title_name=$this->getRequest()->getParam('file_title_name');   
			  $array=array("website_id"=>$website_id,"quote_service_name"=>$quote_service_name,"service_scope"=>$service_scope,"service_package"=>"","created_date"=>time());
			  $fileArray=array();
			  if($templateQuoteInfo['upload_file'])
			  {
				$uploadFileArr=json_decode($templateQuoteInfo['upload_file'], true);  
				$fileArray=array("file_title_name"=>($file_title_name)?$file_title_name:$uploadFileArr['upload_file'],"upload_file"=>$uploadFileArr['upload_file']); 
			  }
			  
			  /////////////////////////////////////////////////////////////////////
			   $locatie = 'public/UploadFolder/'; 
			if ($_FILES['upload_file']['name']) {
				$upload_file= $_FILES['upload_file']['name']; 
 				$base_name=time().'_'.basename(preg_replace("/[^a-zA-Z0-9.]/","",$_FILES['upload_file']['name']));
 				$folder = $locatie.$base_name;
			  if(move_uploaded_file($_FILES['upload_file']['tmp_name'], $folder)){
  				$upload_file=$base_name;
				
				if($file_title_name=="")
				{
				$file_title_name=$upload_file;	
				}
				
               $fileArray=array("file_title_name"=>($file_title_name)?$file_title_name:$upload_file,"upload_file"=>$upload_file);
   			 }
             }
			 $array=array("website_id"=>$website_id,"quote_service_name"=>$quote_service_name,"service_scope"=>$service_scope,"upload_file"=>json_encode($fileArray),"created_date"=>time());
			   }
			// echo "<pre>";
			// print_r($array);die;
		  /////////////////////////////////////////////////////////////////////////////////////
			  $this->template->updateQuoteTemplate($array, $id);
			  $this->_s->message ='<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Quote template added successfully.</div>';
             $this->_redirect('template/quotemplate');	
			}	
}

public function getServiceQuoteTemplateinfoAction()
{
$quote_service_id=$this->getRequest()->getParam('quote_service_id');
$templateQuoteInfo=$this->template->getQuoteTemplateInfo($quote_service_id);
$templateQuoteInfo['upload_file']=	json_decode($templateQuoteInfo['upload_file']);
echo json_encode($templateQuoteInfo);die;
}
}