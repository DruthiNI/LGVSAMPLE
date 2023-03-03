<?php
class Managequery_Model_Boxquery {
  function __construct() {
  $this->boxquery = new Managequery_Model_DbTable_Boxquery();
  $this->box_tag = new Managequery_Model_DbTable_Boxtag();
 
  
 		$this->db = Zend_Registry::get('db');
     }
     /*

      * 

      * Date: 14-OCT-2022

      * Developer: Niranjan Singh

      * Modified By: Niranjan Singh

      * @param: id: int

      */

 	  public function addboxQuery($data) {

         $id = $this->boxquery->insert($data);

         return $id;

     }
   public function CheckExistBoxQuery($name,$email_id,$website)
  {
	  
	  $select = $this->boxquery->select()
	  ->where('email_id = ?', ($email_id))
	  ->where('website_id = ?', ($website))
	  ->order("id desc");
      $Result =  $this->boxquery->fetchRow($select);
 	 return $Result;
	  
	     
	}
	
	public function getBoxQuery($whereStr="",$search_keywords="",$tags=array())
	{
		
	$select = $this->db->select();
    $select->from(array('box'=>'tbl_box_query'))
	->joinLeft(array('web' => 'tbl_website'), 'box.website_id = web.id', array("website"));
	  
	    if($search_keywords)
	   {
		$select->where(" box.name LIKE '%".$search_keywords."%' or box.email_id LIKE '%".$search_keywords."%' or box.phone LIKE '%".$search_keywords."%'");   
	   }
	    if(count($tags) > 0)
		 {
			$i=0;
			foreach($tags as $tag)
			{
				
			$i++;
			if($i ==1)
			{
			$select->where('find_in_set("'.$tag.'", tags) <> 0');
			}
			else
			{
			$select->ORwhere('find_in_set("'.$tag.'", tags) <> 0');
			}	
			 
			} 
		 }
		 if($whereStr)
	     {
		 $select->where($whereStr);   
	     }
	   
	   $select->order('id desc');
     $temps = $this->db->fetchAll($select);
     return $temps;
	 
	}
	
	public function DeleteBoxQuery($queryid)
	{
		$id=implode(',',$queryid);  
  	  $this->db->delete('tbl_box_query','id in('.$id.')');
	}
	public function DeleteBoxQuerySingleId($queryid)
	{
	$this->db->delete('tbl_box_query','id ='.$queryid);	
	}
	public function getBoxQueryDetails($query_id)
	{
	$select = $this->db->select();
    $select->from(array('box'=>'tbl_box_query'))
	->joinLeft(array('web' => 'tbl_website'), 'box.website_id = web.id', array("website"))
	->where("box.id =?", $query_id)
	   ->order('box.id desc');
     $temps = $this->db->fetchRow($select);
     return $temps;	
	}
	
	 public function getAllBoxTag()
     {
	  $select = $this->box_tag->select()
	  ->order("id desc");
      $Result =  $this->box_tag->fetchAll($select);
 	  return $Result;
	 }
function TagNameMatchingId($ids)
 {
 $ids=explode(",",$ids);
 $select = $this->box_tag->select()->where("id IN(?)",$ids)->order('id DESC');
 $temps = $this->box_tag->fetchAll($select);
 return $temps; 
 }
 
 public function getBoxQueryUnclaimedOver2Hours()
     {
     $new_time = date("Y-m-d H:i:s", strtotime('-2 hours', time()));
	  $select = $this->boxquery->select()
	  ->where("created_on <=?", strtotime($new_time))
	  ->where("if_generic_query !='yes'")
	  ->order("id desc");
      $Result =  $this->boxquery->fetchAll($select);
 	  return $Result;
	 }
	 
 function getAllBoxTags()
 {
	 $select = $this->box_tag->select()->order('id DESC');
         $temps = $this->box_tag->fetchAll($select);
         return $temps; 
 }
 
 function getBoxTagId($id)
 {
	$select = $this->box_tag->select()->where('id = ?', ($id));
      $Result =  $this->box_tag->fetchRow($select);
 	 return $Result; 
 }
 
 public function addBoxTags($data) {
         $id = $this->box_tag->insert($data);
         return $id;
     }
	 
  public function updateBoxTags($data, $id) {
	
         if (Zend_Validate::is($id, 'Int')) {
              $this->box_tag->update($data, 'id = ' . $id);

        }

    }
	
	public function CheckExistBoxTax($box_tag_name)
  {
	  
	  $select = $this->box_tag->select()
	  ->where('box_tag_name = ?', ($box_tag_name));
      $Result =  $this->box_tag->fetchAll($select);
 	 return $Result;
	  
	     
	}
	
	public function DeleteBoxTags($id)
	{
		$id=implode(',',$id);
 	  $this->db->delete('tbl_box_tags','id in('.$id.')');
	}
 
 }