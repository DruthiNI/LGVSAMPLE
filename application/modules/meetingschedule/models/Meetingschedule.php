<?php
 
class Meetingschedule_Model_Meetingschedule {
  function __construct() {
  $this->sms = new Meetingschedule_Model_DbTable_Meetingschedule();
 

		$this->db = Zend_Registry::get('db');
     }
     /*
      * 
      * Date: 14-OCT-2015
      * Developer: Niranjan Singh
      * Modified By: Niranjan Singh
      * @param: id: int
      */
 	  public function insert($data) {
         $id = $this->sms->insert($data);
         return $id;
     } 
	 
	 public function updateSMSStatus($data, $id) {
         if (Zend_Validate::is($id, 'Int')) {
              $this->sms->update($data, 'id = ' . $id);
         }
     }
	
  public function FetchAllMeetingSchedule() {
	  $select = $this->db->select();
	 $select->from(array('me_sh'=>'tbl_meeting_schedule'), array('id','date','time','created_date','status'))
 	   ->join(array('query' => 'tbl_query'), 'me_sh.client_id = query.id', array('name','email_id','phone'))
 	   ->join(array('user' => 'tbl_users'), 'me_sh.user_id = user.id', array('username','mobile_no','name as user_name'));
       $temps = $this->db->fetchAll($select);
       return $temps; 
     }
	 
	 function DeleteShedule($meetingid)
	 {
	  $id=implode(',',$meetingid);
 	  $this->db->delete('tbl_meeting_schedule','id in('.$id.')'); 
	 }
	 
	public function AutoSentSMS($todaydate)
	{
	  $select = $this->db->select();
	 $select->from(array('me_sh'=>'tbl_meeting_schedule'), array('id','date','time','created_date','status'))
 	   ->join(array('query' => 'tbl_query'), 'me_sh.client_id = query.id', array('name','email_id','phone'))
 	   ->join(array('user' => 'tbl_users'), 'me_sh.user_id = user.id', array('username','mobile_no','name as user_name'))
	   ->where('me_sh.date = ?',$todaydate)->where('me_sh.status = ?',0);
       $temps = $this->db->fetchAll($select);
	    
	   
	   foreach($temps as $meetingInfo)
	   {
		$date=date("d/m/Y",$meetingInfo['date']);
		$time=$meetingInfo['time']; 
		
		$template1='Dear '.$meetingInfo['user_name'].', Mr.'.$meetingInfo['name'].' will reach at '.$time.' as per scheduled meeting. Regards emarketz.';   
		   
		  $template2='Dear '.$meetingInfo['name'].', Mr.'.$meetingInfo['user_name'].' will reach at '.$time.' as per scheduled meeting. Regards emarketz.'; 
		 
		$this->SentSMS($template1,$meetingInfo['mobile_no']);
		$this->SentSMS($template2,$meetingInfo['phone']);
		$array=array('status'=>1);
		$update= $this->updateSMSStatus($array,$meetingInfo['id']);
	   }
	   
	   
	   
        
	}
	
 public function FetchSubadminMeetingSchedule($allocateduser)
 {  
	 $id=explode(',',$allocateduser); 
	 $select = $this->db->select();
	 $select->from(array('me_sh'=>'tbl_meeting_schedule'), array('id','date','time','created_date','status'))
 	   ->join(array('query' => 'tbl_query'), 'me_sh.client_id = query.id', array('name','email_id','phone'))
 	   ->join(array('user' => 'tbl_users'), 'me_sh.user_id = user.id', array('username','mobile_no','name as user_name'))
	   ->where("me_sh.user_id IN(?)", $id);
       $temps = $this->db->fetchAll($select);
       return $temps; 
 }
 public function FetchUserMeetingSchedule($user_id)
 {
 	 $select = $this->db->select();
	 $select->from(array('me_sh'=>'tbl_meeting_schedule'), array('id','date','time','created_date','status'))
 	   ->join(array('query' => 'tbl_query'), 'me_sh.client_id = query.id', array('name','email_id','phone'))
 	   ->join(array('user' => 'tbl_users'), 'me_sh.user_id = user.id', array('username','mobile_no','name as user_name'))
	   ->where("me_sh.user_id =?", $user_id);
       $temps = $this->db->fetchAll($select);
       return $temps; 	 
 }
 
 public function SentSMS($template,$mobile)
 {
  $data = array(
 'user' => "emarketz",
 'password'=> "emarketz#12",
 'msisdn' => $mobile,
 'sid' => "EMKRTZ",
 'msg' => $template,
 'fl' =>"0",
 'gwid'=> 2,
 );
 list($header, $content) = $this->PostRequest("http://cloud.smsindiahub.in/vendorsms/pushsms.aspx",
 // the url to post to
 "http://rapidcollaborate.com", // its your url
 $data
 );
 $data = json_decode($content); 
 }

private function PostRequest($url, $referer, $_data) {
// convert variables array to string:
$data = array();
while(list($n,$v) = each($_data)){
$data[] = "$n=$v";
}
$data = implode('&', $data);
// parse the given URL
$url = parse_url($url);
if ($url['scheme'] != 'http') {
die('Only HTTP request are supported !');
}
// extract host and path:
$host = $url['host'];
$path = $url['path'];
// open a socket connection on port 80
$fp = fsockopen($host, 80);
// send the request headers:
fputs($fp, "POST $path HTTP/1.1\r\n");
fputs($fp, "Host: $host\r\n");
fputs($fp, "Referer: $referer\r\n");
fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
fputs($fp, "Content-length: ".strlen($data) ."\r\n");
fputs($fp, "Connection: close\r\n\r\n");
fputs($fp, $data);
$result = '';
while(!feof($fp)) {
// receive the results of the request
$result .= fgets($fp, 128);
}
//HTTP API VER 1.1 
// close the socket connection:
fclose($fp);
// split the result header from the content
$result = explode("\r\n\r\n", $result, 2);
$header = isset($result[0]) ? $result[0] : '';
$content = isset($result[1]) ? $result[1] : '';
// return as array:
return array($header, $content);
}		 

}