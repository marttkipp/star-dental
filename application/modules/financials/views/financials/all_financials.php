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
						<th>First Name</th>
						<th>Last Name</th>
						<th>Phone</th>
					
						<th>Vehicle Number Plate</th>

						<th>Amount</th>
						<th>Status</th>
						<th colspan="2">Actions</th>
					</tr>
				</thead>
				  <tbody>
				  
			';
			
			//get all administrators
			$administrators = $this->users_model->get_active_users();
			if ($administrators->num_rows() > 0)
			{
				$admins = $administrators->result();
			}
			
			else
			{
				$admins = NULL;
			}
			
			foreach ($query->result() as $row)
			{
				
				$personnel_id = $row->personnel_id;
				$personnel_onames = $row->personnel_onames;
				$personnel_fname = $row->personnel_fname;
				$personnel_dob = $row->personnel_dob;
				$personnel_email = $row->personnel_email;
				$personnel_phone = $row->personnel_phone;
				$job_title_id = $row->job_title_id;
				$personnel_job_status = $row->personnel_job_status;

				$query2 = $this->driver_model->get_personnel_vehicles($personnel_id);
				if ($query2->num_rows() > 0)
		        {
		        	foreach ($query2->result() as $row2)
			            {
                              $vehicle_plate = $row2->vehicle_plate;
				             
			            }
			    }else{
			    	$vehicle_plate = 'Unassigned';
			    }

				
				
				//status
				if($personnel_job_status == 1)
				{
					$status = 'Active';
				}
				else
				{
					$status = 'Disabled';
				}
				
				//create deactivated status display
				if($personnel_job_status == 0)
				{
					$status = '<span class="label label-default">Deactivated</span>';
					}
				//create activated status display
				else if($personnel_job_status == 1)
				{
					$status = '<span class="label label-success">Active</span>';
					
					}				
				
				
				$amount = 500;
				
				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$personnel_fname.'</td>
						<td>'.$personnel_onames.'</td>
						<td>'.$personnel_phone.'</td>
						
						<td>'.$vehicle_plate.'</td>
						<td>'.$amount.'</td>
						<td>'.$status.'</td>
			          	<td><a href="'.site_url().'financials/disburse-mpesa/'.$personnel_id.'" class="btn btn-sm btn-success" title="M-PESA '.$personnel_fname.'"><i class="fa fa-money"></i> M-PESA</a></td>
						
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
			$result .= "There are no Drivers";
		}
?>






<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
			<div class="panel-heading">
                <div class="panel-tools" style="color: #fff;">
                   
                </div>
                <?php echo $title;?>
                
            </div>

			<div class="panel-body">
		    	<?php
				$search = $this->session->userdata('customer_search_title2');
				
				if(!empty($search))
				{
					echo '<h6>Filtered by: '.$search.'</h6>';
					echo '<a href="'.site_url().'hr/customer/close_search" class="btn btn-sm btn-info pull-left">Close search</a>';
				}
		        $success = $this->session->userdata('success_message');

				if(!empty($success))
				{
					echo '<div class="alert alert-success"> <strong>Success!</strong> '.$success.' </div>';
					$this->session->unset_userdata('success_message');
				}
				
				$error = $this->session->userdata('error_message');
				
				if(!empty($error))
				{
					echo '<div class="alert alert-danger"> <strong>Oh snap!</strong> '.$error.' </div>';
					$this->session->unset_userdata('error_message');
				}
				?>
				<div class="row" style="margin-bottom:20px;">
                                   <div class="col-lg-2 col-lg-offset-10">
                                        <a href="<?php echo site_url();?>financials" class="btn btn-sm btn-success pull-right">Send ALL M-PESA</a>
                                     </div>
                                     
                            </div>       
				<div class="table-responsive">
		        	
					<?php echo $result;?>
			
		        </div>
			</div>
		    <div class="panel-footer">
		    	<?php if(isset($links)){echo $links;}?>
		    </div>
		 </div>
	</div>
</div>

