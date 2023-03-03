<?php
 
class Pricequote_Model_Pricequote {
  function __construct() {
  $this->price_service = new Pricequote_Model_DbTable_Priceservice();
  $this->miletone = new Pricequote_Model_DbTable_Milestone();
  
  $this->price_service_package = new Pricequote_Model_DbTable_PriceServicePackage();
  $this->package_miletone = new Pricequote_Model_DbTable_PackageMilestone();
  

		$this->db = Zend_Registry::get('db');
     }
     /*
      * 
      * Date: 12-AUG-2017
      * Developer: Niranjan Singh
      * Modified By: Niranjan Singh
      * @param: id: int
      */
public function insertPriceQuote($arr)
{	 
	$insert=$this->price_service->insert($arr);
	return $insert;
}	  
public function insertMilestone($arr)
{	 
	$insert=$this->miletone->insert($arr);
	return $insert;
}
function checkExistPriceQuoteService($ref_id,$quote_service_id)
   {
$select = $this->price_service->select()->where("ref_id=?",$ref_id)->where("quote_service_id=?",$quote_service_id);
         $temps = $this->price_service->fetchRow($select);
         return $temps;	  
   }
 function getPriceQuoteByRef($ref_id)
 {
/*$select = $this->price_service->select()->where("ref_id=?",$ref_id);
         $temps = $this->price_service->fetchAll($select);
         return $temps;	
*/
	$select = $this->db->select();
	 $select->from(array('ser'=>'tbl_client_services'))
	->joinLeft(array('temp' => 'tbl_quote_template'), 'ser.quote_service_id = temp.id', array('quote_service_name'))
	->where("ser.ref_id=?",$ref_id);
	$temps = $this->db->fetchAll($select);
	return $temps;		 
 }
 function getCheckoutServiceInfo($service_id)
 {
$select = $this->db->select();
	 $select->from(array('ser'=>'tbl_client_services'))
	->join(array('ass_q' => 'tbl_assign_query'), 'ser.ref_id = ass_q.id', array('query_id','website_id'))
	->join(array('query' => 'tbl_query'), 'ass_q.query_id = query.id', array('name','email_id','phone','location','city'))
	->where("ser.id=?",$service_id);
	$temps = $this->db->fetchRow($select);
	return $temps;	 
 }
 function getServiceInfo($service_id)
 {
/*$select = $this->price_service->select()->where("id=?",$service_id);
         $temps = $this->price_service->fetchRow($select);
         return $temps;	
*/	
$select = $this->db->select();
	 $select->from(array('ser'=>'tbl_client_services'))
	->joinLeft(array('temp' => 'tbl_quote_template'), 'ser.quote_service_id = temp.id', array('quote_service_name','upload_file'))
	->where("ser.id=?",$service_id);
	$temps = $this->db->fetchRow($select);
	return $temps;
	 
 }
  function getServiceMilestoneData($service_id)
 {
$select = $this->miletone->select()->where("service_id=?",$service_id);
$temps = $this->miletone->fetchAll($select);
return $temps;	 
 }
 function FetchCurrentMilestone($service_id)
 {
$select = $this->miletone->select()->where("service_id=?",$service_id)->where("status =?", 0);
$temps = $this->miletone->fetchRow($select);
return $temps; 
 }
 function ServiceUpdateStatus($arr,$id)
 {
	$query=$this->db->update("tbl_client_services", $arr, 'id ='.$id);
 }
 function MilestoneUpdate($arr,$id)
 {
	$query=$this->db->update("tbl_milestone_payment", $arr, 'id ='.$id); 
 }
 function FetchPendingMilestone($service_id)
 {
$select = $this->miletone->select()->where("service_id=?",$service_id)->where("status=?",0);
$temps = $this->miletone->fetchAll($select);
return $temps;	 
 }
 function deleteMilestone($id)
 {
$this->db->delete('tbl_milestone_payment','id in('.$id.')');	 
 }
 
function deleteService($ser_id)
{
$this->db->delete('tbl_client_services','id in('.$ser_id.')');	
$this->db->delete('tbl_milestone_payment','service_id in('.$ser_id.')');	
$this->db->delete('tbl_service_package','service_id in('.$ser_id.')');	
$this->db->delete('tbl_package_milestone_payment','service_id in('.$ser_id.')');	
}   
public function insertPriceQuotePackage($arr)
{	 
	$insert=$this->price_service_package->insert($arr);
	return $insert;
}
public function insertPriceQuotePackageMilestone($arr)
{
$insert=$this->package_miletone->insert($arr);
	return $insert;	
}
public function getServicePackageData($service_id)
{
$select = $this->price_service_package->select()->where("service_id=?",$service_id);
$temps = $this->price_service_package->fetchAll($select);
return $temps;	
}

public function getServicePackageInfo($package_id)
{
$select = $this->price_service_package->select()->where("id=?",$package_id);
$temps = $this->price_service_package->fetchRow($select);
return $temps; 	
}
public function getServicePackageMilestoneData($package_id)
{
$select = $this->package_miletone->select()->where("package_id=?",$package_id);
$temps = $this->package_miletone->fetchAll($select);
return $temps;	
}
public function PackageMilestoneUpdate($arr,$id)
{
$query=$this->db->update("tbl_package_milestone_payment", $arr, 'id ='.$id);	
}
 function FetchPendingPackageMilestone($package_id)
 {
$select = $this->package_miletone->select()->where("package_id=?",$package_id)->where("status=?",0);
$temps = $this->package_miletone->fetchAll($select);
return $temps;	 
 }
 
public function getfirstServicePackage($service_id)
{
$select = $this->price_service_package->select()->where("service_id=?",$service_id)->limit(1);
$temps = $this->price_service_package->fetchRow($select);
return $temps;	
}
public function FetchPackageCurrentMilestone($package_id)
{
$select = $this->package_miletone->select()->where("package_id=?",$package_id)->where("status =?", 0);
$temps = $this->package_miletone->fetchRow($select);
return $temps; 	
}
} 