<?php
 
class Template_Model_Template {
  function __construct() {
  $this->email = new Template_Model_DbTable_Email();
  $this->sms = new Template_Model_DbTable_Sms();
  $this->quoteTemp = new Template_Model_DbTable_Quotetemplate();

		$this->db = Zend_Registry::get('db');
     }
     /*
      * 
      * Date: 14-OCT-2015
      * Developer: Niranjan Singh
	  * Modified Date: 03-SEP-2019
      * Modified By: Niranjan Singh
      * @param: id: int
      */
 	  public function addEmail($data) {
         $id = $this->email->insert($data);
         return $id;
     }
     public function updateEmail($data, $id) {
         if (Zend_Validate::is($id, 'Int')) {
              $this->email->update($data, 'id = ' . $id);
        }
    }
 	 public function getEmailId($id)
 	 {
  	 $select = $this->email->select()->where('id = ?', ($id));
     $Result =  $this->email->fetchRow($select);
 	 return $Result;
      }
 
    
	public function DeleteEmail($id,$table)
 	{
	 //$id=implode(',',$id);
 	  $this->db->delete($table,'id ='.$id);
  	}
	 public function getAllEmailTemplate($whereStr="") 
	 {
		 $select = $this->db->select();
         $select->from(array('em'=>'tbl_email_template'))
		 ->joinLeft(array('comp' => 'tbl_company'), 'em.company_id = comp.id', array('company_name'))
	   ->joinLeft(array('web' => 'tbl_website'), 'em.website_id = web.id', array('website as website_name'))
	   //->joinLeft(array('service' => 'tbl_requirement'), 'em.service_id = service.id', array('name as service_name'))
	  // ->joinLeft(array('tag' => 'tbl_tags'), 'em.tag_name = tag.id', array('tag_name'))
		->order('em.id DESC');
		if($whereStr)
		{
		$select->where($whereStr);	
		}
        $temps = $this->db->fetchAll($select);
        return $temps;
		 

    }
	public function getAllSMSTemplate($whereStr="")
	{
	 $select = $this->db->select();
         $select->from(array('sm'=>'tbl_sms_template'))
		 ->joinLeft(array('comp' => 'tbl_company'), 'sm.company_id = comp.id', array('company_name'))
	   ->joinLeft(array('web' => 'tbl_website'), 'sm.website_id = web.id', array('website as website_name'))
	   ->joinLeft(array('service' => 'tbl_requirement'), 'sm.service_id = service.id', array('name as service_name'))
		->order('sm.id DESC');
		if($whereStr)
		{
		$select->where($whereStr);	
		}
        $temps = $this->db->fetchAll($select);
        return $temps;	
	}
 function getTemplateInfo($id,$table)
 {
$select = $this->db->select()->from($table)->where('id = ?', ($id));
$Result =  $this->db->fetchRow($select);
 	 return $Result;	 
 }
 //////////////////////////////////////////////
function getAllCompany()
{
$select = $this->db->select()->from('tbl_company');
        $temps = $this->db->fetchAll($select);
        return $temps;	
}
function getAllWebsite()
{
$select = $this->db->select()->from('tbl_website')->where("status=?",1);
        $temps = $this->db->fetchAll($select);
        return $temps;	
}
function getWebsiteInfo($id)
{
$select = $this->db->select()->from('tbl_website')->where("id =?",$id);
        $temps = $this->db->fetchRow($select);
        return $temps;	
}
function getAllService()
{
$select = $this->db->select()->from('tbl_requirement');
        $temps = $this->db->fetchAll($select);
        return $temps;	
}
function getAllCategoryService($category)
{
$select = $this->db->select()->from('tbl_requirement')->where("category LIKE '%".$category."%'");
        $temps = $this->db->fetchAll($select);
        return $temps;	
}
function getServiceInfo($id)
{
$select = $this->db->select()->from('tbl_requirement')->where("id =?",$id);
        $temps = $this->db->fetchRow($select);
        return $temps;	
}
function getTemplateInfoDetails($table,$whereStr="")
{
$select = $this->db->select()->from($table);
if($whereStr)
{
$select->where($whereStr);	
}
$Result =  $this->db->fetchRow($select);
 	 return $Result;	
}

function getTemplateInfoAllDetails($table,$whereStr="",$tags="")
{	
$select = $this->db->select()->from($table);
if($whereStr)
{
$select->where($whereStr);	
}
if($tags)
{
//$select->where("tag_name IN(?)", $tags);	
}
$Result =  $this->db->fetchAll($select);
 	 return $Result;
}

//////////////////SMS Function////////////////////////////////
 public function addSMS($data) 
 {
         $id = $this->sms->insert($data);
         return $id;
 }
  public function updateSMS($data, $id) 
  {
         if (Zend_Validate::is($id, 'Int')) {
              $this->sms->update($data, 'id = ' . $id);
        }
 }
 
public function getAllQuoteTemplate($whereStr="") 
	 {
		 $select = $this->db->select();
         $select->from(array('em'=>'tbl_quote_template'))
	   ->joinLeft(array('web' => 'tbl_website'), 'em.website_id = web.id', array('website as website_name'))
		->order('em.id DESC');
		if($whereStr)
		{
		$select->where($whereStr);	
		}
        $temps = $this->db->fetchAll($select);
        return $temps;
		 

    } 
 public function insertQuoteTemplate($data)
 {  
	$id = $this->quoteTemp->insert($data);
         return $id; 
 }
 
 public function getQuoteTemplateInfo($id)
 {
	$select = $this->db->select()->from('tbl_quote_template')->where("id =?",$id);
        $temps = $this->db->fetchRow($select);
        return $temps; 
 }
 
 public function updateQuoteTemplate($data, $id)
 {
	if (Zend_Validate::is($id, 'Int')) {
              $this->quoteTemp->update($data, 'id = ' . $id);
        } 
 }
 
public function getServiceThisWebsite($web_id)
 {
  $select = $this->db->select()->from('tbl_quote_template')->where("website_id =?",$web_id);
        $temps = $this->db->fetchAll($select);
        return $temps;	 
 }
 
}



