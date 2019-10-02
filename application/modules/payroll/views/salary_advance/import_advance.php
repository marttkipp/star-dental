<section class="panel">

 
        <!-- Widget head -->
        <header class="panel-heading">
          <h4 class="page-title"><?php echo $title;?></h4>
        </header>             

        <!-- Widget content -->
		<div class="panel-body">
        <div class="padd">
            
        <div class="row">
        <div class="col-md-12">
		<?php
		$error = $this->session->userdata('error_message');
		$success = $this->session->userdata('success_message');
		
		if(!empty($error))
		{
			echo '<div class="alert alert-danger">'.$error.'</div>';
			$this->session->unset_userdata('error_message');
		}
		
		if(!empty($success))
		{
			echo '<div class="alert alert-success">'.$success.'</div>';
			$this->session->unset_userdata('success_message');
		}
		?>
            <?php
                if(isset($import_response))
                {
                    if(!empty($import_response))
                    {
                        echo $import_response;
                    }
                }
                
                if(isset($import_response_error))
                {
                    if(!empty($import_response_error))
                    {
                        echo '<div class="center-align alert alert-danger">'.$import_response_error.'</div>';
                    }
                }
            ?>
                
            
            <?php echo form_open_multipart('import/import-salary-advances', array("class" => "form-horizontal", "role" => "form"));?>
            
            <div class="row">
                <div class="col-md-12">
                    <ul>
                        <li>Download the import template <a href="<?php echo site_url().'import/advance-template';?>">here.</a></li>
                        
                        <li>Save your file as a <strong>CSV (Comma Delimited)</strong> file before importing</li>
                        <li>After adding your personnel to the import template please import them using the button below</li>
                    </ul>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3 pull-left">
                    <?php
					$branches = $this->branches_model->all_branches();
					?>
                    <label class="col-lg-4 control-label">Branch <span class="required">*</span></label>
                    <div class="col-lg-8">
                    <select name="branch_id" id="branch_id" class="form-control">
                        <?php
                        echo '<option value="0">No Branch </option>';
                        if($branches->num_rows() > 0)
                        {
                        	$result = $branches->result();
                            foreach($result as $res)
							{
								if($res->branch_id == $branch_id)
								{
									echo '<option value="'.$res->branch_id.'" selected>'.$res->branch_name.' '.$res->branch_id.'</option>';
								}
								else
								{
									echo '<option value="'.$res->branch_id.'">'.$res->branch_name.' </option>';
								}
							 }
                        }
                           ?>
                        </select>
                           
                    </div>
                </div>
			</div>
			<br/>
			<div class="row">
				<div class="col-md-3 pull-left">
                    <?php
					$month = $this->salary_advance_model->all_months();
					?>
                    <label class="col-lg-4 control-label">Month <span class="required">*</span></label>
                    <div class="col-lg-8">
                    <select name="month_id" id="month_id" class="form-control">
                        <?php
                        echo '<option value="0">Select Month </option>';
                        if($month->num_rows() > 0)
                        {
                        	$result = $month->result();
                            foreach($result as $res)
							{
								if($res->month_id == $month_id)
								{
								echo '<option value="'.$res->month_id.'" selected>'.$res->month_name.' '.$res->month_id.'</option>';
								}
								else
								{
								echo '<option value="'.$res->month_id.'">'.$res->month_name.' </option>';
								}
							}
						}
                           ?>
                        </select>
                           
                    </div>
                </div>
				<br/>
				<div class="col-md-12" style="margin-top:10px">
					<div class="fileUpload btn btn-primary">
                        <span>Import Salary Advance</span>
						<input type="file" class="upload"  name="import_csv"/>
						
						<input type="submit" onChange="this.form.submit();" onclick="return confirm('Do you really want to upload the selected file?')">
                    </div>
				</div>
            </div>
                   
                    
        </div>
        </div>
            <?php echo form_close();?>
		</div>
		</div>

</section>