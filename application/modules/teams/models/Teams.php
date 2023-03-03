<?php
 
class Teams_Model_Teams {
  function __construct() {
  $this->team = new Teams_Model_DbTable_Teams();

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
 	  public function addTeams($data) {
         $id = $this->team->insert($data);
         return $id;
     }
 
 	 public function getTeamId($id)
 	 {
  	 $select = $this->team->select()->where('id = ?', ($id));
     $Result =  $this->team->fetchRow($select);
 	 return $Result;
      }
 
    public function updateTeams($data, $id) {
	
         if (Zend_Validate::is($id, 'Int')) {
              $this->team->update($data, 'id = ' . $id);

        }

    }
 
	public function DeleteTeams($id)
 	{
	 $id=implode(',',$id);
 	  $this->db->delete('tbl_teams','id in('.$id.')');
  	}
 

	 public function getAllTeams($whereStr="") {
		 $select = $this->db->select();
         $select->from(array('tm'=>'tbl_teams'))
		->order('tm.id DESC');
		if($whereStr)
		{
		$select->where($whereStr);	
		}
 
        $temps = $this->db->fetchAll($select);
        return $temps;
		 

    }
 

}



