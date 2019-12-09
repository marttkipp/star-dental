<?php
$message_template_id = $message_template[0]->message_template_id;
$message_template_code = $message_template[0]->message_template_code;
$message_template_description = $message_template[0]->message_template_description;
$message_template_status = $message_template[0]->message_template_status;
$contact_type = $message_template[0]->contact_type;
$contact_category_id = $message_template[0]->contact_category_id;

if($contact_type == 0)
{	
	$sample_text = $this->messaging_model->get_sample_text($message_template_description,0);
}
else
{
	$sample_text = $this->messaging_model->get_sample_text($message_template_description,1);

}
// var_dump($message_template_status); die();

?>
<section class="panel">
		<div class="row" style="margin-top:40px;">
		    <div class="col-lg-12">
		    	<a href="<?php echo site_url();?>messaging/message-templates" class="btn btn-sm btn-success pull-right" style="margin-top:-25px">Back to Message Template</a>

		    </div>
		</div>
</section>

<div class="row" style="margin-top:-20px">
	<div class="col-md-12">
		<section class="panel panel-featured-left panel-featured-primary">
            <div class="panel-body">
                <div class="widget-summary">
                    
                    <div class="widget-summary-col">
                        <div class="summary" >
                            <!-- <h4 class="title">Message Template</h4> -->
                            <div class="info ">
                            	<strong>Template:</strong> <?php echo $message_template_description;?>
                               <p><strong> Example text : <span style="text-style:intalic;">"<?php echo $sample_text;?>"</span></strong></p>
                            </div>
                            <?php
                            echo form_open("create-batch-items/".$message_template_id, array("class" => "form-horizontal", "role" => "form"));
                            	?>
                            	<input type="hidden" name="message_template_description" value="<?php echo $message_template_description?>">
                            	<div class="row" style="margin-top:10px;">
									<div class="col-md-12">
								        <div class="form-actions center-align">
								            <button class="submit btn btn-sm btn-primary" type="submit">
								                Create Batch Items
								            </button>
								        </div>
								    </div>
								</div>
                            	<?php
                            	echo form_close();
                            ?>

                        </div>

                    </div>
                </div>

            </div>
        </section>
	</div>
	
</div>
<?php
		
		$result = '';
		
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;
			
			$result .= 
			'
			<table class="table table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th>#</th>
						<th>Batch Code</th>
						<th>Total Contacts</th>
						<th>Sent Messages</th>
						<th>Unsent Messages</th>
						<th>Date Created</th>
						<th>Status</th>
						<th colspan="4">Actions</th>
					</tr>
				</thead>
				  <tbody>
				  
			';
			foreach ($query->result() as $row)
			{
				$message_batch_id = $row->message_batch_id;
				$message_batch_code = $row->message_batch_code;
				$message_batch_status = $row->message_batch_status;
				$created_by = $row->created_by;
				$search_template = $row->search_template;
				$search_title = $row->search_title;
				$message_template_description = $row->message_template_description;
				
				if($contact_type == 0)
				{
					$where = 'patient_delete = 0';
					$total_contacts = $this->messaging_model->count_items('patients',$where);

				}
				else
				{
					$where = 'entryid > 0';
					if($contact_category_id > 0)
					{
						$where .= ' AND contact_category_id ='.$contact_category_id;
					}
					$total_contacts = $this->messaging_model->count_items('allcounties',$where);
				}

				if($contact_category_id > 0)
				{
					$checked = ' AND contact_category_id = '.$contact_category_id;
				}
				else
				{
					$checked = '';
				}
				
				$sent_where = 'message_status = 1 AND message_batch_id = '.$message_batch_id.' AND message_template_id ='.$message_template_id;
				$sent_messages = $this->messaging_model->count_items('messages',$sent_where);
				$unsent_where = 'message_status = 0 AND message_batch_id = '.$message_batch_id.' AND message_template_id ='.$message_template_id;
				$unsent_messages = $this->messaging_model->count_items('messages',$unsent_where);;
				//status
				if($message_batch_status == 1)
				{
					$status = 'Completed';
				}
				else
				{
					$status = 'No action';
				}
			
				//create deactivated status display
				if($message_batch_status == 0)
				{
					$status = '<span class="label label-warning">Deactivated</span>';
					$button = '<a class="btn btn-info btn-sm" href="'.site_url().'send-messages/'.$message_batch_id.'/'.$message_template_id.'" onclick="return confirm(\'Do you want to send messages to '.$message_batch_code.'?\');" title="send messages '.$message_batch_code.'">Send message</a>';
				}
				//create activated status display
				else if($message_batch_status == 1)
				{
					$status = '<span class="label label-success">Completed</span>';
					$button = '<span class="label label-warning">Create another batch to send messages</span>';
				}
			
				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$message_batch_code.'</td>
						<td>'.$total_contacts.'</td>
						<td>'.$sent_messages.'</td>
						<td>'.$unsent_messages.'</td>
						<td>'.date('jS M Y H:i a',strtotime($row->created)).'</td>
						<td>'.$status.'</td>
						
						<td>'.$button.'</td>
					
						
						</tr> 
				';
			

				// <td><a href="'.site_url().'view-schedules/'.$message_batch_id.'/'.$message_template_id.'" class="btn btn-sm btn-primary" title="Edit '.$message_template_code.'"><i class="fa fa-calendar"></i></a></td>
				// 		<td><a href="'.site_url().'view-senders/'.$message_batch_id.'/'.$message_template_id.'" class="btn btn-sm btn-warning" title="Edit '.$message_template_code.'"><i class="fa fa-eye"></i></a></td>
			}
			
			$result .= 
			'
						  </tbody>
						</table>
			';
		}
		
		else
		{
			$result .= "There are no message batches created";
		}
?>
<section class="panel">
	<header class="panel-heading">
					
		<h2 class="panel-title"><?php echo $message_template_code;?> Batches</h2>
	</header>
	<div class="panel-body">
    	<div class="row" style="margin-bottom:20px;">
            <div class="col-lg-12">
             <?php
                $error = $this->session->userdata('error_message');
                $validation_error = validation_errors();
                $success = $this->session->userdata('success_message');
                
                if(!empty($error))
                {
                  echo '<div class="alert alert-danger">'.$error.'</div>';
                  $this->session->unset_userdata('error_message');
                }
                
                if(!empty($validation_error))
                {
                  echo '<div class="alert alert-danger">'.$validation_error.'</div>';
                }
                
                if(!empty($success))
                {
                  echo '<div class="alert alert-success">'.$success.'</div>';
                  $this->session->unset_userdata('success_message');
                }
              ?>
            </div>
        </div>
		<div class="table-responsive">
        	
			<?php echo $result;?>
	
        </div>
	</div>
</section>
<script type="text/javascript">
	$(function() {
	    $("#countyname").customselect();
	    $("#gender").customselect();
	    $("#constituencyname").customselect();
	    $("#Pollingstationname").customselect();
	    $("#CAWname").customselect();
	    
	});
	$(document).ready(function(){
		$(function() {
			$("#countyname").customselect();
			$("#gender").customselect();
			$("#constituencyname").customselect();
			$("#Pollingstationname").customselect();
			$("#CAWname").customselect();
		});
	});

	function get_new_search(){

		var myTarget2 = document.getElementById("new_search");
		var button = document.getElementById("open_new_search");
		var button2 = document.getElementById("close_new_search");

		myTarget2.style.display = '';
		button.style.display = 'none';
		button2.style.display = '';
	}
	function close_new_search(){

		var myTarget2 = document.getElementById("new_search");
		var button = document.getElementById("open_new_search");
		var button2 = document.getElementById("close_new_search");

		myTarget2.style.display = 'none';
		button.style.display = '';
		button2.style.display = 'none';
	}
</script>

