<?php
//personnel data
$row = $personnel->row();

// var_dump($row) or die();
$personnel_onames = $row->personnel_onames;
$personnel_fname = $row->personnel_fname;
$personnel_dob = $row->personnel_dob;
$personnel_email = $row->personnel_email;
$personnel_phone = $row->personnel_phone;
$personnel_address = $row->personnel_address;
$civil_status_id = $row->civilstatus_id;
$personnel_locality = $row->personnel_locality;
$title_id = $row->title_id;
$gender_id = $row->gender_id;
$personnel_username = $row->personnel_username;
$personnel_kin_fname = $row->personnel_kin_fname;
$personnel_kin_onames = $row->personnel_kin_onames;
$personnel_kin_contact = $row->personnel_kin_contact;
$personnel_kin_address = $row->personnel_kin_address;
$kin_relationship_id = $row->kin_relationship_id;
$job_title_idd = $row->job_title_id;
$staff_id = $row->personnel_staff_id;
$personnel_id = $row->personnel_id;

//echo $gender_id;
//repopulate data if validation errors occur
$validation_error = validation_errors();
				
if(!empty($validation_error))
{
	$personnel_onames =set_value('personnel_onames');
	$personnel_fname =set_value('personnel_fname');
	$personnel_dob =set_value('personnel_dob');
	$personnel_email =set_value('personnel_email');
	$personnel_phone =set_value('personnel_phone');
	$personnel_address =set_value('personnel_address');
	$civil_status_id =set_value('civil_status_id');
	$personnel_locality =set_value('personnel_locality');
	$title_id =set_value('title_id');
	$gender_id =set_value('gender_id');
	$personnel_username =set_value('personnel_username');
	$personnel_kin_fname =set_value('personnel_kin_fname');
	$personnel_kin_onames =set_value('personnel_kin_onames');
	$personnel_kin_contact =set_value('personnel_kin_contact');
	$personnel_kin_address =set_value('personnel_kin_address');
	$kin_relationship_id =set_value('kin_relationship_id');
	$job_title_id =set_value('job_title_id');
	$staff_id =set_value('staff_id');
}
	$result ='';
	if($leave_requests->num_rows() > 0)
	{
		$count = 0;
			
		$result .= 
		'
		<br/>
		<table class="table table-bordered table-striped table-condensed">
			<thead>
				<tr>
					<th>#</th>
					<th>Type</a></th>
					<th>Start date</a></th>
					<th>End date</a></th>
					<th>Days</a></th>
					<th>Status</a></th>
					<th colspan="5">Actions</th>
				</tr>
			</thead>
			  <tbody>
			  
		';
		
		foreach ($leave_requests->result() as $row)
		{
			$leave_days = $row->leave_days;
			$leave_type_name = $row->leave_type_name;
			$leave_duration_status = $row->leave_duration_status;
			$leave_duration_id = $row->leave_duration_id;
			$leave_type_count = $row->leave_type_count;
			$start_date = date('jS M Y',strtotime($row->start_date));
			$end_date = date('jS M Y',strtotime($row->end_date));
			$days_taken = $this->site_model->calculate_leave_days($start_date, $end_date, $leave_type_count);
			
			//create deactivated status display
			if($leave_duration_status == 0)
			{
				$status = '<span class="label label-danger">Unclaimed</span>';
				$button = '<a class="btn btn-sm btn-info" href="'.site_url().'human-resource/activate-leave/'.$leave_duration_id.'/'.$personnel_id.'/1" onclick="return confirm(\'Do you want to activate '.$start_date.' Leave?\');" title="Activate '.$start_date.' Leave"><i class="fa fa-thumbs-up"></i></a>';
			}
			//create activated status display
			else if($leave_duration_status == 1)
			{
				$status = '<span class="label label-success">Claimed</span>';
				$button = '<a class="btn btn-sm btn-default" href="'.site_url().'human-resource/deactivate-leave/'.$leave_duration_id.'/'.$personnel_id.'/1" onclick="return confirm(\'Do you want to deactivate '.$start_date.' Leave?\');" title="Deactivate '.$start_date.' Leave"><i class="fa fa-thumbs-down"></i></a>';
			}
			
			$count++;
			$result .= 
			'
				<tr>
					<td>'.$count.'</td>
					<td>'.$leave_type_name.'</td>
					<td>'.$start_date.'</td>
					<td>'.$end_date.'</td>
					<td>'.$days_taken.'</td>
					<td>'.$status.'</td>
					<td>'.$button.'</td>
					<td><a href="'.site_url().'human-resource/delete-personnel-leave/'.$leave_duration_id.'/'.$personnel_id.'/1" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete?\');" title="Delete"><i class="fa fa-trash"></i></a></td>
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
		$result = "<p>No leave have been assigned</p>";
	}
	

//repopulate data if validation errors occur
$validation_error = validation_errors();
				
if(!empty($validation_error))
{
	$start_date = set_value('start_date');
	$end_date = set_value('end_date');
	$leave_type_id = set_value('leave_type_id');
}

else
{
	$start_date = '';
	$end_date = '';
	$leave_type_id = '';
}
?>
<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title"><?php echo $personnel_fname.' '.$personnel_onames;?> Leave Balance</h2>
    </header>
    <div class="panel-body">

<div class="row">
	
    <?php 
	$row = $personnel->row();
	$gender_id = $row->gender_id;
	
	if($leave_types->num_rows() > 0)
	{
		foreach($leave_types->result() as $res)
		{
			$leave_type_id = $res->leave_type_id;
			$leave_type_name = $res->leave_type_name;
			$leave_balance = $res->leave_days;
			
			if($leave->num_rows() > 0)
			{
				foreach($leave->result() as $row)
				{
					$leave_type_id2 = $row->leave_type_id;
					
					if($leave_type_id == $leave_type_id2)
					{
						$leave_type_count = $row->leave_type_count;
						$start_date = date('jS M Y',strtotime($row->start_date));
						$end_date = date('jS M Y',strtotime($row->end_date));
						$days_taken = $this->site_model->calculate_leave_days($start_date, $end_date, $leave_type_count);
						$leave_balance -= $days_taken;
					}
				}
			}
			
			//maternity & femail
			if(($leave_type_id == 2) && ($gender_id == 2))
			{
				echo '
				<div class="col-md-3 col-lg-3 col-xl-3">
					<section class="panel panel-featured-left panel-featured-tertiary">
						<div class="panel-body">
							<div class="widget-summary">
								<div class="widget-summary-col widget-summary-col-icon">
									<div class="summary-icon bg-tertiary">
										<i class="fa fa-calendar"></i>
									</div>
								</div>
								<div class="widget-summary-col">
									<div class="summary">
										<h4 class="title">'.$leave_type_name.' Leave</h4>
										<div class="info">
											<strong class="amount">'.$leave_balance.' days</strong>
										</div>
									</div>
									<div class="summary-footer">
										<!--<a class="text-muted text-uppercase">(statement)</a>-->
									</div>
								</div>
							</div>
						</div>
					</section>
				</div>
				';
			}
			
			//paternity & male
			else if(($leave_type_id == 1) && ($gender_id == 1))
			{
				echo '
				<div class="col-md-3 col-lg-3 col-xl-3">
					<section class="panel panel-featured-left panel-featured-tertiary">
						<div class="panel-body">
							<div class="widget-summary">
								<div class="widget-summary-col widget-summary-col-icon">
									<div class="summary-icon bg-tertiary">
										<i class="fa fa-calendar"></i>
									</div>
								</div>
								<div class="widget-summary-col">
									<div class="summary">
										<h4 class="title">'.$leave_type_name.' Leave</h4>
										<div class="info">
											<strong class="amount">'.$leave_balance.' days</strong>
										</div>
									</div>
									<div class="summary-footer">
										<!--<a class="text-muted text-uppercase">(statement)</a>-->
									</div>
								</div>
							</div>
						</div>
					</section>
				</div>
				';
			}
			
			else if($leave_type_id > 2)
			{
				echo '
				<div class="col-md-3 col-lg-3 col-xl-3">
					<section class="panel panel-featured-left panel-featured-tertiary">
						<div class="panel-body">
							<div class="widget-summary">
								<div class="widget-summary-col widget-summary-col-icon">
									<div class="summary-icon bg-tertiary">
										<i class="fa fa-calendar"></i>
									</div>
								</div>
								<div class="widget-summary-col">
									<div class="summary">
										<h4 class="title">'.$leave_type_name.' Leave</h4>
										<div class="info">
											<strong class="amount">'.$leave_balance.' days</strong>
										</div>
									</div>
									<div class="summary-footer">
										<!--<a class="text-muted text-uppercase">(statement)</a>-->
									</div>
								</div>
							</div>
						</div>
					</section>
				</div>
				';
			}
		}
	}
	?>
    
</div>
	</div>
</section>

<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title">Add Leave</h2>
    </header>
    <div class="panel-body">
    	<!-- Adding Errors -->
		<?php
        if(isset($error)){
            echo '<div class="alert alert-danger"> Oh snap! Change a few things up and try submitting again. </div>';
        }
        if(!empty($validation_errors))
        {
            echo '<div class="alert alert-danger"> Oh snap! '.$validation_errors.' </div>';
        }
        
        ?>
        
        <?php echo form_open('human-resource/add-personnel-leave/'.$personnel_id.'/1', array("class" => "form-horizontal", "role" => "form"));?>
<div class="row">
	<div class="col-md-4">
        
        
        <div class="form-group">
            <label class="col-lg-5 control-label">Start date: </label>
            
            <div class="col-lg-7">
            	<div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="start_date" placeholder="Start date">
                </div>
            </div>
        </div>
    </div>
	<div class="col-md-4">
        
        <div class="form-group">
            <label class="col-lg-5 control-label">End date: </label>
            
            <div class="col-lg-7">
            	<div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="end_date" placeholder="End date">
                </div>
            </div>
        </div>
    </div>
	<div class="col-md-4">
        
        <div class="form-group">
            <label class="col-lg-5 control-label">Leave type: </label>
            
            <div class="col-lg-7">
            	<select class="form-control" name="leave_type_id">
                	<?php
                    	if($leave_types->num_rows() > 0)
						{
							foreach($leave_types->result() as $res)
							{
								$leave_type_id = $res->leave_type_id;
								$leave_type_name = $res->leave_type_name;
								
								echo '<option value="'.$leave_type_id.'" >'.$leave_type_name.'</option>';
							}
						}
					?>
                </select>
            </div>
        </div>
        
	</div>
</div>
<div class="row" style="margin-top:10px;">
	<div class="col-md-12">
        <div class="form-actions center-align">
        	<input type="hidden" name="personnel_id" value="<?php echo $personnel_id;?>"/>
            <button class="btn btn-primary" type="submit">
                Add leave
            </button>
        </div>
    </div>
</div>
<?php echo form_close();?>
	</div>
</section>

<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title">Leave List</h2>
    </header>
    <div class="panel-body">
            <?php echo $result;?>
    </div>
</section>