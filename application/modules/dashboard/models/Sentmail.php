<?php 
class Dashboard_Model_Sentmail extends Zend_Db_Table_Abstract
{
    function __construct()
	{
	 	$this->sentmail = new Dashboard_Model_DbTable_Sentmail();
		$this->db = Zend_Registry::get('db');
		
		//// Check for Samtp settings..
		$sql = $this->db->select()->from('tbl_email_setting')->where('id=?',1);
		$this->res = $this->db->fetchRow($sql);
	}
	
  public function fetchSentmail()
  {
   $res = $this->sentmail->select();
    
  return $this->sentmail->fetchAll($res);
  }
  
  public function insertSentmail($array)
  {
    $id = $this->sentmail->insert($array);
        return $id;
  }
  
  public function addAttachment($data)
  {
	$sql = $this->db->insert('tbl_attachments',$data);  
  }
  
  public function sendMail($fromEmail, $fromName, $toEmail, $toName, $message, $replyTo, $insertid)     
	{
		//echo $fromEmail.'/'. $fromName.'/'.  $toEmail.'/'.  $toName.'/'. $message.'/'. $replyTo.'/'. $insertid;die;  
      set_time_limit(0); 
	   try{
                			 
		$res1 = $this->sentmail->select()->where('id = ?', $insertid);
		$ressub= $this->sentmail->fetchRow($res1);
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers.= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers.= 'From: '.$fromName.'<' .$replyTo.">\r\n" .'Reply-To:'.$replyTo . "\r\n" .'X-Mailer: PHP/' . phpversion();
                mail($toEmail, $ressub['subject'], stripslashes($message), $headers);


                /*echo"<pre>";		
		//print_r($ressub);die;
		$mail = new Zend_Mail();
		$mail->setBodyText(stripslashes(strip_tags($message)));
		$mail->setBodyHtml(stripslashes(strip_tags($message)));
		//$mail->setFrom($fromEmail, $fromName);
		$mail->addTo($toEmail, $toName);
		$mail->setSubject($ressub['subject']);
		//$mail->setReplyTo($replyTo);
		//$mail->setReturnPath('rs8666@gmail.com');		
				
		if(trim($this->res['email_method'])=='SMTP')
		{
			$config = array('auth' => 'login','username'=>$this->res['smtp_username'],'password'=>$this->res['smtp_password']); 
			$transport = new Zend_Mail_Transport_Smtp($this->res['smtp_host_name'], $config);
			Zend_Mail::setDefaultTransport($transport); 
			
		}
		$mail->send(); */
              }catch (Zend_Exception $e) {
                echo "Caught exception: " . get_class($e) . "\n";
                echo "Message: " . $e->getMessage() . "\n";die;
                // Other code to recover from the error
             }
	
	}
	
	public function getScheduleMail()
	{
	  $cdate=date('Y-m-d H:i:s',time());
	  $res = $this->sentmail->select()->where('status=?','schedule')->where('schedule_date <=?',$cdate);
      return $this->sentmail->fetchAll($res);	
	}
	
	public function updateMailStatus($mailid)
	{
		$data=array('status'=>'sent');
	$sql = $this->db->update('tbl_sent_mail',$data,'id='.$mailid);	
	}
	
	public function getsentmailbeforeHour($userid,$beforehour)
	{
	   $select=$this->db->select();
       $select->from(array('sent'=>'tbl_sent_mail'), array('from_id','sent_date'))
	   ->join(array('rec' => 'tbl_receiver'), 'sent.id = rec.mail_id', array('id'))
	   ->where('sent.from_id=?',$userid)
	   ->where('sent.sent_date >=?',$beforehour);
	   return $select = $this->db->fetchAll($select);	
	}
	
}
?>