<?php
		$result = form_open('bulk-add-contacts/'.$message_batch_id.'/'.$message_template_id);
		
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;
			
			$result .= 
			
			'
			<table class="table table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th></th>
						<th>#</th>
						<th><a >Company</a></th>
						<th><a >Number</a></th>
						<th><a >First name</a></th>
						<th><a >Last name</a></th>
						<th><a >Phone</th>
						<th><a >Email</th>
						
					</tr>
				</thead>
				  <tbody>
				  
			';
			
			foreach ($query->result() as $row)
			{
				$company_name = $row->company_name;
				$member_id = $row->member_id;
				$member_first_name = $row->member_first_name;
				$last_name = $row->member_surname;
				$phone = $row->member_phone;
				$email = $row->member_email;
				$member_number = $row->member_number;
				$member_status = $row->member_status;
				$identification_result = $display_invoices = '';
				
				$checkbox_data = array(
								  'name'        => 'contacts[]',
								  'id'          => 'checkbox'.$member_id,
								  'class'          => 'css-checkbox lrg',
								  'value'       => $member_id
								);
				
				
				//create deactivated status display
				if($member_status == 0)
				{
					$status = '<span class="label label-default">Deactivated</span>';
					$button = '<a class="btn btn-info" href="'.site_url().'admin/activate-member/'.$member_id.'" onclick="return confirm(\'Do you want to activate '.$member_first_name.'?\');" title="Activate '.$member_first_name.'"><i class="fa fa-thumbs-up"></i></a>';
				}
				//create activated status display
				else if($member_status == 1)
				{
					$status = '<span class="label label-success">Active</span>';
					$button = '<a class="btn btn-default" href="'.site_url().'admin/deactivate-member/'.$member_id.'" onclick="return confirm(\'Do you want to deactivate '.$member_first_name.'?\');" title="Deactivate '.$member_first_name.'"><i class="fa fa-thumbs-down"></i></a>';
				}
				
				$count++;
				$result .= 
				'
					<tr>
						<td>'.form_checkbox($checkbox_data).'<label for="checkbox'.$member_id.'" name="checkbox79_lbl" class="css-label lrg klaus"></label>'.'</td>
						<td>'.$count.'</td>
						<td>'.$company_name.'</td>
						<td>'.$member_number.'</td>
						<td>'.$member_first_name.'</td>
						<td>'.$last_name.'</td>
						<td>'.$phone.'</td>
						<td>'.$email.'</td>
					</tr> 
				';
			}
			
			$result .= 
			'
						  </tbody>
						</table>
			';
			$result .= '
			<br>
			<div class="center-align">
				<button type="submit" class="btn btn-sm btn-success">Add Selected Contacts</button>
			</div>
			'.form_close();
		}
		
		else
		{
			$result .= "There are no members";
		}
?>
<div class="panel panel-default">
    <div class="panel-heading">Search members</div>
    <div class="panel-body">
    	<?php echo form_open('search-members/'.$message_batch_id.'/'.$message_template_id);?>
    	<div class="row">
    		<div class="col-md-6">
                <div class="form-group">
                        <label class="col-md-4 control-label">Company: </label>
                        
                        <div class="col-md-8">
                        	<input type="hidden" name="company_name" id="company_name" value="" />
                            <select name="company_id" id="company_id" class="form-control" onchange="document.getElementById('company_name').value=this.options[this.selectedIndex].text">
                                <option value="">----Select a company----</option>
                                <?php
                                                        
                                    if($companies_list_rs->num_rows() > 0){

                                        foreach($companies_list_rs->result() as $row):
                                            $company_name = $row->company_name;
                                            $company_id = $row->company_id;
                                            
                                            if($company_id == set_value('company_id'))
                                            {
                                                echo "<option value='".$company_id."' selected='selected'>".$company_name."</option>";
                                            }
                                            
                                            else
                                            {
                                                echo "<option value='".$company_id."'>".$company_name."</option>";
                                            }
                                        endforeach;
                                    }
                                ?>
                            </select>
                        </div>
                   </div>
              <div class="form-group">
                    <label class="col-md-4 control-label">Member number: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="member_number" placeholder="member number">
                    </div>
                </div>
                
                 <div class="form-group">
                    <label class="col-md-3 control-label">Gender: </label>
                    <div class="col-md-3">
                        <input type="radio" name="gender_id" value="0" checked="checked"> All
                    </div>
                    
                    <div class="col-md-3">
                        <input type="radio" name="gender_id" value="1"> Male
                    </div>
                    
                    <div class="col-md-3">
                        <input type="radio" name="gender_id" value="2"> Female
                    </div>
                </div>
             <div class="form-group">
                <label class="col-md-4 control-label">Phone No.: </label>
                
                <div class="col-md-8">
                    <input type="text" class="form-control" name="member_phone" placeholder="Phone No.">
                </div>
            </div>
            <div class="form-group">
                        <label class="col-md-3 control-label">Payment Status: </label>
                        
                        <div class="col-md-3">
                            <input type="radio" name="status" value="1" checked="checked"> All
                        </div>
                        
                        <div class="col-md-3">
                            <input type="radio" name="status" value="2"> Paid
                        </div>
                        
                        <div class="col-md-3">
                            <input type="radio" name="status" value="3"> Not Paid
                        </div>
               </div>
    		</div>
    		<div class="col-md-6">
    		<div class="form-group">
	            <label class="col-md-4 control-label">Date of Birth From: </label>
	            
	            <div class="col-md-8">
	                <div class="input-group">
	                    <span class="input-group-addon">
	                        <i class="fa fa-calendar"></i>
	                    </span>
	                    <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="dob_from" placeholder="Date of Birth From">
	                </div>
	            </div>
	          </div>
	          <div class="form-group">
                <label class="col-md-4 control-label">Date of Birth To: </label>
                
                <div class="col-md-8">
                     <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="dob_to" placeholder="Date of Birth To">
                    </div>
                </div>
            </div>
			 <div class="form-group">
                    <label class="col-md-4 control-label">First name: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="member_first_name" placeholder="First name">
                    </div>
                </div>
               <div class="form-group">
                    <label class="col-md-4 control-label">Last Name: </label>
                    
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="memmber_surname" placeholder="Other Names">
                    </div>
                </div>
              <div class="form-group">
                        <label class="col-md-4 control-label">Registration Status: </label>
                        <div class="col-md-8">
                            <select name="member_status" class="form-control">
                                <option value="">All</option>
                                <?php
                                                        
                                    if($member_statuses->num_rows() > 0){

                                        foreach($member_statuses->result() as $row):
                                            $member_status_name = $row->member_status_name;
                                            $member_status_id = $row->member_status_id;
                                            
                                            if($member_status_id == set_value('member_status'))
                                            {
                                                echo "<option value='".$member_status_id."' selected='selected'>".$member_status_name."</option>";
                                            }
                                            
                                            else
                                            {
                                                echo "<option value='".$member_status_id."'>".$member_status_name."</option>";
                                            }
                                        endforeach;
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                

    		</div>
    		
    	</div>
    	<br>
    		<div class="row" >
    			<div class="col-md-12">
	    			<div class="center-align">
						<button type="submit" class="btn btn-sm btn-warning">Search Contacts</button>
					</div>
    				
    			</div>
    		</div>
    	<?php echo form_close();?>
   	</div>
</div>
<section class="panel">
	<header class="panel-heading">						
		<h2 class="panel-title"><?php echo $title;?></h2>
		<a href="<?php echo site_url();?>template-detail/<?php echo $message_template_id;?>" class="btn btn-sm btn-info pull-right" style="margin-top:-25px"> Back to template</a>
		<a href="<?php echo site_url();?>senders-view/<?php echo $message_batch_id;?>/<?php echo $message_template_id;?>" class="btn btn-sm btn-success pull-right" target="_blank" style="margin-top:-25px;margin-right:5px;"> View Persons</a>
		<a href="<?php echo site_url();?>create-all-batch/<?php echo $message_batch_id;?>/<?php echo $message_template_id;?>" class="btn btn-sm btn-danger pull-right" style="margin-top:-25px;margin-right:25px;"> Add all contacts</a>
		<!-- Button to trigger modal -->
        <a href="#import-contacts" class="btn btn-sm btn-primary pull-right" data-toggle="modal" title="Import Contacts" style="margin-top:-25px;margin-right:25px;"><i class="fa fa-upload"></i> Import Contacts</a>
        
        <!-- Modal -->
        <div id="import-contacts" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Import Contacts</h4>
                    </div>
                    
                    <div class="modal-body">
                        <?php echo form_open_multipart('import/import-custom-contacts/'.$message_batch_id.'/'.$message_template_id, array("class" => "form-horizontal", "role" => "form"));?>
                        <div class="row">
                            <div class="col-md-12">
                                <ul>
                                    <li>Download the import template <a href="<?php echo site_url().'import/custom-contacts-template';?>">here.</a></li>
                                    
                                    <li>Save your file as a <strong>CSV (Comma Delimited)</strong> file before importing</li>
                                    <li>After adding payroll data to the import template please import them using the button below</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="fileUpload btn btn-primary">
                                    <span>Import Contacts</span>
                                    <input type="file" class="upload"  name="import_csv"/>
                                </div>
                            </div>
                        </div>
                        </br>
                        <div class="row">
                            
                            <div class="col-md-offset-3 col-md-3">
                                <input type="submit" class="btn btn-success" value="Import Contacts" onChange="this.form.submit();" onclick="return confirm('Do you really want to upload the selected file?')">
                            </div>
                        </div>
                        <?php echo form_close();?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                    </div>
                </div>
            </div>
        </div>
	</header>
	<div class="panel-body">
		<div class="col-md-12">
			<?php

		$member_search_item = $this->session->userdata('member_search_item');
			if(!empty($member_search_item))
			{
					echo '<a href="'.site_url().'close-search/'.$message_batch_id.'/'.$message_template_id.'" class="btn btn-sm btn-warning pull-left"> Close Search</a>';
				
			}
			?>
		</div>
		<?php

		// var_dump($member_search_item); die();
		
	    $success = $this->session->userdata('success_message');

		if(!empty($success)){

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
		<br/>
		<div class="col-md-12">
			<div class="table-responsive">
		    	
				<?php echo $result;?>

		    </div>
	    </div>
	</div>
	<div class="panel-footer">
		<?php if(isset($links)){echo $links;}?>
	</div>
</section>
