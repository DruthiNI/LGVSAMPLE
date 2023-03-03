

<div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1" data-toggle="tab">Assign Consultant </a></li>
              <li><a href="#tab_2" data-toggle="tab">Submit Price</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
                 <form method="post" name="assQuoteForm" id="assQuoteForm" class="form-horizontal">
				 <input type="hidden" name="ref_id" value="<?=$this->quoteInfo['ref_id'];?>">
			  <input type="hidden" name="quote_id" value="<?=$this->quoteInfo['id'];?>">
              <div class="box-body">
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-3 control-label">Assign to</label>

                  <div class="col-sm-9">
                   <select name="assign_to_userid" id="assign_to_userid" class="form-control">

                            <option value="">Select User</option>	
                          <?php
						 foreach($this->ConsultantUserData as $userData)
						 {
						  ?>
                     <option value="<?=$userData['id']?>"><?=ucwords($userData['name'])?></option>
						 <?php }?>						  
                        </select>
					<div class="error" id="assign_to_useridError"></div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputPassword3" class="col-sm-3 control-label">Comments </label>
                  <div class="col-sm-9">
                    <textarea name="admin_comments" id="admin_comments" placeholder="Comments" class="form-control"></textarea>
					<div class="error" id="admin_commentsError"></div>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
			  <input type="button" name="assygn_to_user" value="Assign" class="btn btn-info pull-right" onclick="return ValidateAssignTask()">
               </div>
              <!-- /.box-footer -->
            </form>
         
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_2">
              <form method="post" name="submitQuoteForm" id="submitQuoteForm" class="form-horizontal">
			  <input type="hidden" name="ref_id" value="<?=$this->quoteInfo['ref_id'];?>">
			  <input type="hidden" name="quote_id" value="<?=$this->quoteInfo['id'];?>">
              <div class="box-body">
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-3 control-label">Amount (<?=$this->quoteInfo['currency']?>)</label>

                  <div class="col-sm-9">
                   <input type="text" name="quote_amount" id="quote_amount" class="form-control">
				   <div class="error" id="quote_amountError"></div>
				   
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputPassword3" class="col-sm-3 control-label">Comments </label>
                  <div class="col-sm-9">
                    <textarea name="comment" id="comment" placeholder="Comments" class="form-control"></textarea>
					<div class="error" id="commentError"></div>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <input type="button" name="priceSubmitted" class="btn btn-info pull-right" value="Submit" onclick="return PriceSubmitValidate()">
              </div>
              <!-- /.box-footer -->
            </form>
         
              </div>
              
            </div>
            <!-- /.tab-content -->
          </div>
 			
<script>
function ValidateAssignTask()
{
var error="";
if($("#assign_to_userid").val()=="")
{
$("#assign_to_useridError").html("Please select assign to user");	
document.getElementById("assign_to_userid").focus();
error=1;
}	
/*if($("#admin_comments").val()=="")
{
$("#admin_commentsError").html("Please enter comments");	
document.getElementById("admin_comments").focus();
error=1;
}
*/
if(error==1)
{
return false;	
}
else
{
$.ajax({
		type : 'POST',
		url : '<?=SITEURL?>managequote/index/submit-assign-quote',
		data : $('#assQuoteForm').serialize(),
		cache:false,
		beforeSend: function()
		{
		},
		success: function(response)
		{
		 showAdminQuoteDetails();
		 
		}
	  });	
}

}

function PriceSubmitValidate()
{
var error="";

if($("#quote_amount").val()=="")
{
$("#quote_amountError").html("Please enter quote amount");	
document.getElementById("quote_amount").focus();
error=1;
}
if($("#comment").val()=="")
{
$("#commentError").html("Please enter comments");	
document.getElementById("comment").focus();
error=1;
}

if(error==1)
{
return false;	
}
else
{
$.ajax({
		type : 'POST',
		url : '<?=SITEURL?>managequote/index/submitted-to-admin-quote',
		data : $('#submitQuoteForm').serialize(),
		cache:false,
		beforeSend: function()
		{
		},
		success: function(response)
		{
		 showAdminQuoteDetails();
		 
		}
	  });	
}
	
}

 $("input").keypress(function(){
 var id=this.id;
 $("#"+id+"Error").html('');
});
$("textarea").keypress(function(){
 var id=this.id;
 $("#"+id+"Error").html('');
});
 $("input").click(function(){
 var id=this.id;
 $("#"+id+"Error").html('');
});
 $("select").change(function(){
 var id=this.id;
 $("#"+id+"Error").html('');
});
</script>	