<div class="">
            <div class="box-header with-border">
              <h3 class="box-title">Submit Details</h3>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <form method="post" class="form-horizontal">
			  <input type="hidden" name="task_id" value="<?=$AssignDetails['id']?>">
              <div class="box-body">
			   <div class="form-group">
                  <label for="inputEmail3" class="col-sm-4 control-label">Assigned date</label>
                  <div class="col-sm-8"><?=date("d M, Y h:i: A",$AssignDetails['assigned_date'])?></div>
               </div>
			   <?php if($AssignDetails['admin_comments']){?>
			   <div class="form-group">
                  <label for="inputEmail3" class="col-sm-4 control-label">Admin Comments</label>
                  <div class="col-sm-8"><?=$AssignDetails['admin_comments']?></div>
               </div>
			   <?php }?>
                <div class="form-group">
                  <label for="inputPassword3" class="col-sm-4 control-label">Comments </label>
                  <div class="col-sm-8">
                    <textarea name="comment" id="comment" placeholder="Comments" class="form-control"></textarea>
                  </div>
                </div>
				
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <input type="submit" name="ConsultantpriceSubmitted" class="btn btn-info pull-right" value="Submit" onclick="return PriceSubmitValidate()">
              </div>
              <!-- /.box-footer -->
            </form>
            </div>
            <!-- /.box-body -->
          </div>



 
			
<script>
function PriceSubmitValidate()
{
if($("#comment").val()=="")
{
alert("Please enter comments");	
document.getElementById("comment").focus();
return false;
}	
}
</script>	