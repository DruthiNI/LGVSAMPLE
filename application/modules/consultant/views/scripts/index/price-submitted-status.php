<?php
//print_r($AssignDetails);
?>
<div class="">
            <div class="box-header with-border">
              <h3 class="box-title">Submitted Details</h3>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
			  
              <table width="100%" class="table table-hover table-striped">
                  <tbody>
				  
				  <tr>
                    <td width="40%">Assigned date</td>
                    <td width="60%"><?=date("d M, Y h:i: A",$AssignDetails['assigned_date'])?></td>
				  </tr>
				  <?php if($AssignDetails['admin_comments']){?>
				  <tr>
                    <td width="40%">Admin Comments</td>
                    <td width="60%"><?=$AssignDetails['admin_comments']?></td>
				  </tr>
				  <?php }?>
				  <tr>
                    <td width="40%">Submitted Date</td>
                    <td width="60%"><?=date("d M Y H:i A",$AssignDetails['user_submitted_date'])?></td>
				  </tr>
				    <tr>
                    <td width="40%">Comments </td>
                    <td width="60%" id="commentDiv"><?=($AssignDetails['user_comments']!="")?$AssignDetails['user_comments']:$AssignDetails['admin_comments']?></td>
				   </tr>
				   
                  </tbody>
                </table>
            </div>
            <!-- /.box-body -->
          </div>
				