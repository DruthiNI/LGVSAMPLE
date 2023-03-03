<?php
class Managequery_Model_Status {

  function __construct() {

  $this->status = new Managequery_Model_DbTable_Status();
  		$this->db = Zend_Registry::get('db');
      }

     /*
      * 
      * Date: 18-NOV-2016
      * Developer: Niranjan Singh
      * Modified By: Niranjan Singh
      * @param: id: int
      */
	 public function getAllStatus() {
         $select = $this->status->select();
         $temps = $this->status->fetchAll($select);
         return $temps;
      }
	  
	public function FetchStatusName($id)
	{  
		$select = $this->status->select()->where("id = ?",$id);
          $temps = $this->status->fetchRow($select);
         return $temps;
	}
	
	public function FetchCurrentUserName($query_id)
	{
		 
	$select = $this->db->select();
    $select->from(array('ass_qr'=>'tbl_assign_query'), array('user_id','user_status','transfer_type'))
 	   ->join(array('user' => 'tbl_users'), 'ass_qr.user_id = user.id', array('username','name as user_name'))
 	   ->where("ass_qr.query_id = ?", $query_id)
	   ->where("ass_qr.user_status = ?",1);
        $temps = $this->db->fetchRow($select);
        return $temps;		
	}

}