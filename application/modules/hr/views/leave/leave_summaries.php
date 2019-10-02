<?php
	
	$title = 'Leave starting '.date('jS M Y');
	$date = date('Y-m-d');
	$personnel = $this->personnel_model->retrieve_personnel();
	$leave_types = $this->personnel_model->get_leave_types();

	$result ='';
	
		$count = 0;
			
		$result .= 
		'
		<br/>
		<table class="table table-bordered table-striped table-condensed">
			<thead>
				<tr>
					<th width="5%">#</th>
					<th >Personnel</th>
					<th width="10%">Annual</th>
					<th width="10%">Sick</th>
					<th width="10%">Maternity</th>
					<th width="10%">Compassionate</th>
					<th width="10%">Actions</th>
				</tr>
			</thead>
			  <tbody>
			  
		';
		// var_dump($personnel); die();
		foreach ($personnel->result() as $row)
		{
			$personnel_fname = $row->personnel_fname;
			$personnel_onames = $row->personnel_onames;
			$personnel_id = $row->personnel_id;
			$gender_id = $row->gender_id;

			$leave = $this->personnel_model->get_personnel_leave($personnel_id);

			// get the leave types
			if($leave_types->num_rows() > 0)
			{
				foreach($leave_types->result() as $res)
				{
					$leave_type_id = $res->leave_type_id;
					$leave_type_name = $res->leave_type_name;
					$leave_balance = $res->leave_days;
					
					if($leave->num_rows() > 0)
					{
						foreach($leave->result() as $row_end)
						{
							$leave_type_id2 = $row_end->leave_type_id;
							$leave_duration_status = $row_end->leave_duration_status;
							// var_dump($leave_duration_status); die();
							if(($leave_type_id == $leave_type_id2) && ($leave_duration_status == 1))
							{
								$leave_type_count = $row_end->leave_type_count;
								$start_date = date('jS M Y',strtotime($row_end->start_date));
								$end_date = date('jS M Y',strtotime($row_end->end_date));
								$days_taken = $this->site_model->calculate_leave_days($start_date, $end_date, $leave_type_count);
								$leave_balance -= $days_taken;
								
							}
						
							
					    }
					}

					//maternity & femail
					if(($leave_type_id == 2) && ($gender_id == 2))
					{
						$maternity = $leave_balance;
					}
					
					//paternity & male
					else if(($leave_type_id == 1) && ($gender_id == 1))
					{
						$maternity = $leave_balance;
					}
					// sick leave
					else if($leave_type_id == 3)
					{
						$sick = $leave_balance;
					}
					// annual
					else if($leave_type_id == 4)
					{
						$annual = $leave_balance;
					}
					// compassionat
					else if($leave_type_id == 6)
					{
						$compassionate = $leave_balance;
						
					}
				}
			}
			$pending = $this->personnel_model->check_pending_leave($personnel_id);

			if($pending)
			{
				$pending_status = 'info';
			}
			else
			{
				$pending_status = 'default';
			}
			$button = '';
			$count++;
			$result .= 
			'
				<tr class="'.$pending_status.'">
					<td>'.$count.'</td>
					<td>'.$personnel_fname.' '.$personnel_onames.'</td>
					<td>'.$annual.'</td>
					<td>'.$sick.'</td>
					<td>'.$maternity.'</td>
					<td>'.$compassionate.'</td>
					<td><a class="btn btn-sm btn-default" href="'.site_url().'human-resource/personnel-leave-detail/'.$personnel_id.'" title=" Leave"><i class="fa fa-folder-open"></i> Detail </a></td>
				</tr> 
			';
				
		}
		
		$result .= 
		'
					  </tbody>
					</table>
		';
	
	

//repopulate data if validation errors occur
$validation_errors = validation_errors();
				
if(!empty($validation_errors))
{
	$old_personnel_id = set_value('personnel_id');
	$start_date = set_value('start_date');
	$end_date = set_value('end_date');
	$old_leave_type_id = set_value('leave_type_id');
}

else
{
	$old_personnel_id = '';
	$start_date = $date;
	$end_date = '';
	$old_leave_type_id = '';
}
?>
          <section class="panel">
                <header class="panel-heading">
                    <h2 class="panel-title"><?php echo $title;?></h2>
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
          
            
            <?php
				$success = $this->session->userdata('success_message');
				$error = $this->session->userdata('error_message');
				
				if(!empty($success))
				{
					echo '
						<div class="alert alert-success">'.$success.'</div>
					';
					
					$this->session->unset_userdata('success_message');
				}
				
				if(!empty($error))
				{
					echo '
						<div class="alert alert-danger">'.$error.'</div>
					';
					
					$this->session->unset_userdata('error_message');
				}
				
			?>
            <?php echo $result;?>
                </div>
            </section>