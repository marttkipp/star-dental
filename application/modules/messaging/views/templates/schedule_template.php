<?php


$schedules_query = $this->messaging_model->get_message_schedules($message_batch_id);



$result = '';
		
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
		$schedule_period_name = $schedule_row->schedule_period_name;


		$created = date('jS M Y',strtotime($created));
		
		

		//create deactivated status display
		if($schedule_status == 0)
		{
			$status = '<span class="label label-default"> Deactivated</span>';

			$button = '';
			$delete_button = '';
		}
		//create activated status display
		else if($schedule_status == 1)
		{
			$status = '<span class="label label-success">Active</span>';
			$button = '<td><a class="btn btn-default" href="'.site_url().'deactivate-rental-unit/'.$schedule_id.'" onclick="return confirm(\'Do you want to deactivate '.$schedule_period_name.'?\');" title="Deactivate '.$schedule_period_name.'"><i class="fa fa-thumbs-down"></i></a></td>';
			$delete_button = '<td><a href="'.site_url().'deactivate-rental-unit/'.$schedule_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$schedule_period_name.'?\');" title="Delete '.$schedule_period_name.'"><i class="fa fa-trash"></i></a></td>';

		}
			
		$count++;
		$result .= 
		'
			<tr>
				<td>'.$count.'</td>
				<td>'.$schedule_period_name.'</td>
				<td>'.$created.'</td>
				<td>'.$schedule_time.'</td>
				<td>'.$status.'</td>
				<td><a  class="btn btn-sm btn-primary" ><i class="fa fa-folder"></i> View Lease</a></td>
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
?>  
<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title">Active Lease
	          
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
        ?>
        
        <?php echo form_open('create-new-schedule/'.$message_batch_id, array("class" => "form-horizontal", "role" => "form"));?>
			<div class="row">
				 <div class="col-md-4">
			         <div class="form-group">
			            <label class="col-lg-4 control-label">Schedule Period: </label>
			            
			            <div class="col-lg-8">
			            	<select  class="form-control" name="">
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
			            
                            <input type="text" data-plugin-options="{ &quot;showMeridian&quot;: false }" class="form-control" data-plugin-timepicker="" name="schedule_time" placeholder="Schedule time" value="<?php echo set_value("schedule_time");?>">
			            </div>
			        </div>	        
			    </div>
			</div>
			<div class="row" style="margin-top:10px;">
				<div class="col-md-12">
			        <div class="form-actions center-align">
			            <button class="submit btn btn-sm btn-primary" type="submit">
			                Create Lease
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