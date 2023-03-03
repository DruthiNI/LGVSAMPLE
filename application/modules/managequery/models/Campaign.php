<?php
class Managequery_Model_Campaign {
  function __construct() {
  $this->campaign = new Managequery_Model_DbTable_Campaign();
 
  
 		$this->db = Zend_Registry::get('db');
     }
     /*

      * 

      * Date: 14-OCT-2022

      * Developer: Niranjan Singh

      * Modified By: Niranjan Singh

      * @param: id: int

      */

 	  public function insertCampaign($data) {

         $id = $this->campaign->insert($data);

         return $id;

     }
	 
	 public function updateCampaign($data, $id)
	 {
		 if (Zend_Validate::is($id, 'Int'))
		  {
	       $this->campaign->update($data, 'id = ' . $id);
	       }
	 }
   public function getAllCampaign($whereStr)
     {
		  $select = $this->db->select();
		  $select->from(array('camp'=>'tbl_campaign'))
		 ->joinLeft(array('web' => 'tbl_website'), 'camp.camp_website = web.id', array('website as website_name'))
		 ->join(array('user' => 'tbl_users'), 'camp.campaign_user_id = user.id', array('username','name as user_name'));
		 if($whereStr)
		 {
		  $select->where($whereStr);	 
		 }
		 $select->order('camp.assign_campaign_date DESC');
		 $temps = $this->db->fetchAll($select);
		 return $temps;
		 
	 }
	public function getPendingCampaignExceddedDeadline($user_id)
	{
	 $select = $this->campaign->select()->where('camp_date < ?',date("Y-m-d"))->where("campaign_user_id =?",$user_id)->where("status =?", 0);
	 $temps = $this->campaign->fetchAll($select);
	 return $temps;	
	}
	public function getCampaignInfo($campId)
	{
	 $select = $this->db->select();
		  $select->from(array('camp'=>'tbl_campaign'))
		 ->joinLeft(array('web' => 'tbl_website'), 'camp.camp_website = web.id', array('website as website_name'))
		 ->join(array('user' => 'tbl_users'), 'camp.campaign_user_id = user.id', array('username','name as user_name'))
		 ->where("camp.id =?",$campId);
		 $temps = $this->db->fetchRow($select);
		 return $temps;
	}
	
	public function DeleteCampaign($campIds)
	{
		$id=implode(',',$campIds);  
  	  $this->db->delete('tbl_campaign','id in('.$id.')');
	}
	
	public function getCampaignWithQuery($ref_id)
	{
		$select = $this->campaign->select('camp_title');
		$select->where('find_in_set("'.$ref_id.'", queryIds) <> 0');
		$temps = $this->campaign->fetchAll($select);
		return $temps;
	}
 
 
 }