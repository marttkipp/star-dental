<?php
		
		$result = '';
		
		//if advances exist exist display them
		if ($salary_advance_query->num_rows() > 0)
		{
			$count = $page;
		
			$result .= 
			'
			<table class="table table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th>#</th>
						<th>Payroll Number</th>
						<th>Employee Name</th>
						<th>Bank Code</th>
						<th>Account Number</th>
						<th>Salary Advance Amount</th>
					</tr>
				</thead>
				<tbody>
				  
			';

			foreach($salary_advance_query->result() as $advance_details)
			{
				$payroll_number = $advance_details->personnel_number;
				$account_number = $advance_details->bank_account_number;
				$f_name = $advance_details->personnel_fname;
				$o_names = $advance_details->personnel_onames;
				$advance_amount = $advance_details->advance_amount;
				$bank_branch_id = $advance_details->bank_branch_id;
				if(!empty($bank_branch_id))
				{
					$bank_code = $this->salary_advance_model->get_branch_code($bank_branch_id);
				}
				else 
				{
					$bank_code = '';
				}
				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$payroll_number.'</td>
						<td>'.$f_name.' '.$o_names.'</td>
						<td>'.$bank_code.'</td>
						<td>'.$account_number.'</td>
						<td>'.$advance_amount.'</td>
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
			$result .= "There are no advances made";
		}
?>
		<div class="row" style ="center-align">
			<div class="col-sm-6">
				<section class="panel">
					<header class="panel-heading">						
						<h2 class="panel-title">Advances Search</h2>
					</header>
					<div class="panel-body">
						<?php 
						echo form_open('accounts/search-advances');
						?>
						<div class="form-group">
							<label class="col-lg-5 control-label">Branch: </label>
							
							<div class="col-lg-7">
								<select name="branch_id" class="form-control">
									<?php
										if($branches->num_rows() > 0){
											foreach ($branches->result() as $row):
												$branch_name = $row->branch_name;
												$branch_id = $row->branch_id;
												if($branch_id == $branch_id){
													echo "<option value=".$branch_id." selected>".$row->branch_name."</option>";
												}
												else{
													echo "<option value=".$branch_id.">".$row->branch_name."</option>";
												}
											endforeach;
										}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-5 control-label">Month: </label>
							
							<div class="col-lg-7">
								<select name="month" class="form-control">
									<?php
										if($months->num_rows() > 0){
											foreach ($months->result() as $row):
												$month_name = $row->month_name;
												$month_id = $row->month_id;
												if($month_id == $month_id){
													echo "<option value=".$month_id." selected>".$row->month_name."</option>";
												}
												else{
													echo "<option value=".$month_id.">".$row->month_name."</option>";
												}
											endforeach;
										}
									?>
								</select>
							</div>
						</div>
						<div class="row" style="margin-top:10px;">
							<div class="col-lg-7 col-lg-offset-5">
								<div class="form-actions center-align">
									<button class="submit btn btn-primary" type="submit">
										<i class='fa fa-search'></i> Search
									</button>
								</div>
							</div>
						</div>
						<?php echo form_close();?>
					</div>
				</section>
			</div>
		  
		</div>
				<header class="panel-heading">						
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
					<div class="row" style="margin-bottom:20px;">
						<div class="col-md-offset-6 col-md-2">
							<a href="<?php echo site_url();?>download-salary-advance" class="btn btn-sm btn-warning" target="_blank">Download Salary Advances</a>
						</div>
						<div class="col-md-2">
							<a href="<?php echo site_url();?>salary-advance/import-salary-advance" class="btn btn-sm btn-info pull-left">Import Salary Advances</a>
						</div>
						<div class="col-md-2">
							<a href="<?php echo site_url();?>close-salary-advance-search" class="btn btn-sm btn-info pull-right">All Branches Advances</a>
						</div>
							<?php
							$search = $this->session->userdata('advances_search');

							if(!empty($search))
							{
								?>
								<a href="<?php echo site_url();?>close-salary-advance-search" class="btn btn-sm btn-warning">Close search</a>
								<?php
							}
							
							?>
					</div>
				
					<div class="table-responsive">
						
						<?php echo $result;?>
				
					</div>
				</div>
					<div class="panel-footer">
					<?php if(isset($links)){echo $links;}?>
				</div>
				</div>
				