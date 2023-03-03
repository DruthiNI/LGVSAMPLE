<?php
 
class Tags_Model_Tags {
  function __construct() {
  $this->tag = new Tags_Model_DbTable_Tags();

		$this->db = Zend_Registry::get('db');
     }
     /*
      * 
      * Date: 14-OCT-2015
      * Developer: Niranjan Singh
      * Modified By: Niranjan Singh
      * @param: id: int
      */
 	  public function addTags($data) {
         $id = $this->tag->insert($data);
         return $id;
     }
 
 	 public function getTagId($id)
 	 {
  	 $select = $this->tag->select()->where('id = ?', ($id));
      $Result =  $this->tag->fetchRow($select);
 	 return $Result;
      }
 
    public function updateTags($data, $id) {
	
         if (Zend_Validate::is($id, 'Int')) {
              $this->tag->update($data, 'id = ' . $id);

        }

    }
 
	public function DeleteTags($id)
 	{
	 $id=implode(',',$id);
 	  $this->db->delete('tbl_tags','id in('.$id.')');
  	}
 

	 public function getAllTags()
	 {
         $select = $this->tag->select()->order('id DESC');
         $temps = $this->tag->fetchAll($select);
         return $temps;
     }
 
 public function getAllCategoryTags($category, $userid="")
 {
 $select = $this->tag->select()->where("category LIKE '%".$category."%'")->order('id DESC');
 if($userid)
 {
$select	=$select->where("user_id =?",$userid);
 }
 $temps = $this->tag->fetchAll($select);
 return $temps;
 }
 
  public function getAllPrimaryTags()
 {
 $select = $this->tag->select()->where("tag_type =?",'Primary')->order('id DESC');
 $temps = $this->tag->fetchAll($select);
 return $temps;
 }
 
 function TagNameMatchingId($ids)
 {
 $ids=explode(",",$ids);
 $select = $this->tag->select()->where("id IN(?)",$ids)->order('id DESC');
 $temps = $this->tag->fetchAll($select);
 return $temps; 
 }
	
}



