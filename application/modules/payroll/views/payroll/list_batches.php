<?php
		
		$result = '';
		
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $personnel_count = $page;
			
			$result .= 
			'
			<table class="table table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th>#</th>
						<th>Personnel Number</th>
						<th>First Name</th>
						<th>Last Name</th>
						<th>ID Number</th>
						<th>Email Address</th>
						<th colspan = "3">Actions</th>
					</tr>
				</thead>
				<tbody>
				  
			';
			
			foreach ($query->result() as $row)
			{
				$personnel_id = $row->personnel_id;
				$personnel_fname = $row->personnel_fname;
				$personnel_onames = $row->personnel_onames;
				$personnel_number = $row->personnel_number;
				$personnel_email = $row->personnel_email;
				$personnel_national_id_number = $row->personnel_national_id_number;
				$personnel = $personnel_fname.' '.$personnel_onames;
				$count++;
				
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$personnel_number.'</td>
						<td>'.$personnel_fname.'</td>
						<td>'.$personnel_onames.'</td>
						<td>'.$personnel_national_id_number.'</td>
						<td>'.$personnel_email.'</td>
						<td><a href="'.site_url().'payroll/payroll/generate-batch-payroll/'.$payroll_id.'/1/'.$personnel_count.'/'.$page.'" class="btn btn-sm btn-info" title="Recreate payroll for '.$personnel.'" onclick="return confirm(\'Are you sure you would like to generate the payroll for '.$personnel.'\');">Create</a></td>
						<td><a href="'.site_url().'payroll/payroll/view-batch-payslip/'.$payroll_id.'/'.$personnel_id.'" target="_blank" class="btn btn-sm btn-success" title="View payslip for '.$personnel.'">View Payslip</a></td>
						<td><a href="'.site_url().'payroll/payroll/send-batch-payslip/'.$payroll_id.'/'.$personnel_id.'" target="_blank" class="btn btn-sm btn-warning" title="View payslip for '.$personnel.'">Send Payslip</a></td>
					</tr> 
				';
				
				$personnel_count++;
			}
			
			if($page == 0)
			{
				$batch_number = 1;
			}
			
			else
			{
				$batch_number = ($page / $per_page)+ 1;
			}
			
			$total_batches = ceil(($total_rows / $per_page));
			
			$result .= 
			'
						  </tbody>
						</table>
			';
			$generate = '<a href="'.site_url().'accounts/payroll/generate-batch-payroll/'.$payroll_id.'/'.$per_page.'/'.$page.'" class="btn btn-lg btn-primary">Generate batch '.$batch_number.' / '.$total_batches.'</a>';
		}
		
		else
		{
			$result .= "There are no more batches";
			$generate = '';
		}
?>
						
						<section class="panel">
							<header class="panel-heading">						
                            	<a href="<?php echo site_url().'accounts/payroll';?>" class="btn btn-success pull-right btn-sm">Back</a>
								<h2 class="panel-title"><?php echo $title;?></h2>
							</header>
							<div class="panel-body">
                            	<?php
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
                                <div class="center-align">
                                	<?php echo $generate;?>
                                </div>
                            	
								<div class="table-responsive">
                                	
									<?php echo $result;?>
							
                                </div>
                                <div class="panel-footer center-align">
                            		<?php if(isset($links)){echo $links;}?>
                            	</div>
							</div>
                            
						</section>