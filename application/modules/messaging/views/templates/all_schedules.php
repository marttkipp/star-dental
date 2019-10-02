<?php


// $schedules_query = $this->messaging_model->get_message_schedules($message_batch_id);



$result = '';
$message_template_id = $message_template[0]->message_template_id;
		
//if users exist display them
if ($schedules_query->num_rows() > 0)
{
	$count = $page;
	
	$result .= 
	'<table class="table table-bordered table-striped table-condensed">
		<thead>
			<tr>
				<th>#</th>
				<th><a>Schedule Period</a></th>
				<th><a>Schedule Date</a></th>
				<th><a>Schedule Time</a></th>
				<th><a>Created</a></th>
				<th><a>Status</a></th>
				<th colspan="2">Actions</th>
			</tr>
		</thead>
		  <tbody>';
	
	
	foreach ($schedules_query->result() as $schedule_row)
	{
		$schedule_id = $schedule_row->schedule_id;
		$schedule_date = $schedule_row->schedule_date;
		$schedule_time = $schedule_row->schedule_time;
		$created = $schedule_row->created;
		$last_modified = $schedule_row->last_modified;
		$schedule_status = $schedule_row->schedule_status;
		$schedule_period_name = $schedule_row->schedule_period_name;


		$schedule_date = date('jS M Y',strtotime($schedule_date));
		$created = date('jS M Y',strtotime($created));

		if($schedule_date == '1st Jan 1970')
		{
			$schedule_date = '-';
		}
		
		

		//create deactivated status display
		if($schedule_status == 0)
		{
			$status = '<span class="label label-default"> Deactivated</span>';

			$button = '<td><a class="btn btn-success" href="'.site_url().'activate-schedule/'.$schedule_id.'/'.$message_batch_id.'/'.$message_template_id.'" onclick="return confirm(\'Do you want to activate '.$schedule_period_name.'?\');" title="Activate '.$schedule_period_name.'"><i class="fa fa-thumbs-up"></i></a></td>';
			$delete_button = '<td><a href="'.site_url().'delete-schedule/'.$schedule_id.'/'.$message_batch_id.'/'.$message_template_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$schedule_period_name.'?\');" title="Delete '.$schedule_period_name.'"><i class="fa fa-trash"></i></a></td>';
		}
		//create activated status display
		else if($schedule_status == 1)
		{
			$status = '<span class="label label-success">Active</span>';
			$button = '<td><a class="btn btn-default" href="'.site_url().'deactivate-schedule/'.$schedule_id.'/'.$message_batch_id.'/'.$message_template_id.'" onclick="return confirm(\'Do you want to deactivate '.$schedule_period_name.'?\');" title="Deactivate '.$schedule_period_name.'"><i class="fa fa-thumbs-down"></i></a></td>';
			$delete_button = '<td><a href="'.site_url().'delete-schedule/'.$schedule_id.'/'.$message_batch_id.'/'.$message_template_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$schedule_period_name.'?\');" title="Delete '.$schedule_period_name.'"><i class="fa fa-trash"></i></a></td>';

		}
			
		$count++;
		$result .= 
		'
			<tr>
				<td>'.$count.'</td>
				<td>'.$schedule_period_name.'</td>
				<td>'.$schedule_date.'</td>
				<td>'.$schedule_time.'</td>
				<td>'.$created.'</td>
				<td>'.$status.'</td>
				'.$button.'
				'.$delete_button.'
			</tr> 
		';
	}
	
	$result .= 
	'
				  </tbody>
				</table>
	';
}

else
{
	$result .= "There are no leases created";
}
$message_template_id = $message_template[0]->message_template_id;
$message_template_code = $message_template[0]->message_template_code;
$message_template_description = $message_template[0]->message_template_description;
$message_template_status = $message_template[0]->message_template_status;


$sample_text = $this->messaging_model->get_sample_text($message_template_description);

?>
<div class="row" style="margin-top:0px">
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
                             $search_template = $this->session->userdata('search_template');
                            if(!empty($search_template))
                            {
                            	?>
                            	<hr>
                            	  <div class="info center-align">
		                            	<strong>Serach Template:</strong><?php echo $this->session->userdata('search_title');?>
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
                            }
                            ?>

                        </div>

                    </div>
                </div>

            </div>
        </section>
	</div>
	
</div>
<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title">Schedules
	     <a href="<?php echo site_url();?>template-detail/<?php echo $message_template_id;?>" class="btn btn-sm btn-info pull-right" style="margin-top:-5px"> Back to messaging section</a>     
        </h2>
    </header>
    <div class="panel-body">
    	<div class="row" style="margin-bottom:20px;">
            <div class="col-lg-12">
                </div>
        </div>
            
        <!-- Adding Errors -->
        <?php
			
			$validation_errors = validation_errors();
			
			if(!empty($validation_errors))
			{
				echo '<div class="alert alert-danger"> Oh snap! '.$validation_errors.' </div>';
			}
        
			$validation_errors = validation_errors();
			
			if(!empty($validation_errors))
			{
				echo '<div class="alert alert-danger"> Oh snap! '.$validation_errors.' </div>';
			}
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
        
        <?php echo form_open('create-new-schedule/'.$message_batch_id.'/'.$message_template_id, array("class" => "form-horizontal", "role" => "form"));?>
			<div class="row">
				 <div class="col-md-4">
			         <div class="form-group">
			            <label class="col-lg-4 control-label">Schedule Period: </label>
			            
			            <div class="col-lg-8">
			            	<select  class="form-control" name="schedule_period_id">
			            		<option value="0">Select a schedule period</option>
			            		<?php
			            		$periods_query = $this->messaging_model->all_schedule_period();
			            		if($periods_query->num_rows() > 0)
			            		{
			            			foreach ($periods_query->result() as $value) {
			            				# code...
			            				echo "<option value='".$value->schedule_period_id."'>".$value->schedule_period_name."</option>";
			            			}
			            		}
			            		?>
			            	</select>
			            </div>
			        </div>	        
			    </div>
				<div class="col-md-4">
			        <div class="form-group">
			            <label class="col-lg-4 control-label"> Schedule Date: </label>
			            <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="schedule_date" placeholder="Schedule date">
                            </div>
                        </div>
			        </div>			        
				</div>
			   
			    <div class="col-md-4">
			    	<div class="form-group">
			            <label class="col-lg-4 control-label">Schedule Times: </label>
			            
			            <div class="col-lg-8">
			            
                            <input type="text" data-plugin-options="{ &quot;showMeridian&quot;: false }" class="form-control" data-plugin-timepicker="" name="schedule_time" placeholder="Schedule time" value="">
			            </div>
			        </div>	        
			    </div>
			</div>
			<div class="row" style="margin-top:10px;">
				<div class="col-md-12">
			        <div class="form-actions center-align">
			            <button class="submit btn btn-sm btn-primary" type="submit">
			                Create schedule
			            </button>
			        </div>
			    </div>
			</div>
			

			
        <?php echo form_close();?>
        <hr>
        <div class="row" style="margin-top:10px;">
			<div class="col-md-12">
				<?php echo $result;?>
			</div>
		</div>
    </div>
</section>