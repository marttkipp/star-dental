<?php
	$month = $this->payroll_model->get_months();
	$bank_names = $this->personnel_model->get_bank_names();
	$branches = $this->branches_model->all_branches();
	$bank_branch_names = $this->personnel_model->get_bank_branch_names();
?>	
<div class="row">
    <section class="panel">
		<header class="panel-heading">						
			<h2 class="panel-title">Generate Bank Report</h2>
		</header>
		<div class="panel-body">
			<?php
			$error = $this->session->userdata('error_message');
			
			if(!empty($error))
			{
				echo '<div class="alert alert-danger">'.$error.'</div>';
				$this->session->unset_userdata('error_message');
			}
			?>
			<div class="padd">
				<div class="col-sm-8" align="center">
					<section class="panel">
						<header class="panel-heading">						
							<h2 class="panel-title">Generate Bank Report</h2>
						</header>
						<div class="panel-body">
							<?php 
							echo form_open('payroll/generate-bank-report', array('target' => '_blank'));
							?>
							<div class="form-group">
								<label class="col-lg-5 control-label">Branch (Client) Name: </label>
								
								<div class="col-lg-7">
									<select class="form-control" name="branch_id">
										<?php
											if($branches->num_rows() > 0)
											{
												$status = $branches->result();
												
												foreach($status as $res)
												{
													$branch_id = $res->branch_id;
													$branch_name = $res->branch_name;
													
													if($branch_id == $branch_id2)
													{
														echo '<option value="'.$branch_id.'" selected>'.$branch_name.'</option>';
													}
													
													else
													{
														echo '<option value="'.$branch_id.'">'.$branch_name.'</option>';
													}
												}
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-5 control-label">Bank Name: </label>
								
								<div class="col-lg-7">
									<select class="form-control" name="bank_id">
										<?php
											if($bank_names->num_rows() > 0)
											{
												$status = $bank_names->result();
												
												foreach($status as $res)
												{
													$bank_id = $res->bank_id;
													$bank_name = $res->bank_name;
													
													if($bank_id == $bank_id2)
													{
														echo '<option value="'.$bank_id.'" selected>'.$bank_name.'</option>';
													}
													
													else
													{
														echo '<option value="'.$bank_id.'">'.$bank_name.'</option>';
													}
												}
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-5 control-label"> Bank Branch Name: </label>
								
								<div class="col-lg-7">
									<select class="form-control" name="bank_branch_id">
										<?php
											if($bank_branch_names->num_rows() > 0)
											{
												$status = $bank_branch_names->result();
												
												foreach($status as $res)
												{
													$bank_branch_id = $res->bank_branch_id;
													$branch_name = $res->bank_branch_name;
													$branch_code = $res->bank_branch_code;
													
													if($bank_branch_id == $bank_branch_id2)
													{
														echo '<option value="'.$bank_branch_id.'" selected>'.$branch_name.'</option>';
													}
													
													else
													{
														echo '<option value="'.$bank_branch_id.'">'.$branch_name.'</option>';
													}
												}
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
										if($month->num_rows() > 0){
											foreach ($month->result() as $row):
												$mth = $row->month_name;
												$mth_id = $row->month_id;
												if($mth == date("M")){
													echo "<option value=".$mth_id." selected>".$row->month_name."</option>";
												}
												else{
													echo "<option value=".$mth_id.">".$row->month_name."</option>";
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
										Create
									</button>
								</div>
							</div>
						</div>
							<?php echo form_close();?>
						</div>
					</section>
				</div>
			</div>
		</div>
	</section>
</div>