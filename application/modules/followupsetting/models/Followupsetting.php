<?php
 
class Followupsetting_Model_Followupsetting {
  function __construct() {
  $this->followup = new Followupsetting_Model_DbTable_Followupsetting();
 $this->followDate = new Managequery_Model_DbTable_Followupdate();
		$this->db = Zend_Registry::get('db');
     }
     /*
      * 
      * Date: 14-OCT-2015
      * Developer: Niranjan Singh
      * Modified By: Niranjan Singh
      * @param: id: int
      */
 	  public function addPriority($data) {
         $id = $this->followup->insert($data);
         return $id;
     }
 
 	 public function getPriorityId($id)
 	 {
  	 $select = $this->followup->select()->where('id = ?', ($id));
      $Result =  $this->followup->fetchRow($select);
 	 return $Result;
      }
	   public function getPriorityInfo($priority)
 	 {
  	 $select = $this->followup->select()->where('priority = ?', ($priority));
      $Result =  $this->followup->fetchRow($select);
 	 return $Result;
      }
 
    public function updatePriority($data, $id) {
	
         if (Zend_Validate::is($id, 'Int')) {
              $this->followup->update($data, 'id = ' . $id);

        }

    }
 
	public function DeletePriority($id)
 	{
	 $id=implode(',',$id);
 	  $this->db->delete('tbl_priority','id in('.$id.')');
  	}
 

	 public function getAllPriority() {
         $select = $this->followup->select()->order('id DESC');
         $temps = $this->followup->fetchAll($select);
         return $temps;

    }
 
 public function updateFollowupDate($data, $id)
 {
	if (Zend_Validate::is($id, 'Int')) {
              $this->followDate->update($data, 'id = ' . $id);

        }
 }

}



