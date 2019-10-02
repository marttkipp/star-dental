<!-- <?php //echo $this->load->view('services/search/department_account_search', '', TRUE);?> -->
<div class="row">
   
	      
 <section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title"><?php echo $title;?></h2>
      </header>             

          <!-- Widget content -->
                <div class="panel-body">
                <div class="row">
			        <div class="col-md-12">
					    	<div class="col-md-12">
						    	<div class="pull-left">
						        	<?php
									$search = $this->session->userdata('department_account_search');
									
									if(!empty($search))
									{
										echo '<a href="'.site_url().'hospital_administration/department_accounts/close_department_account_search" class="btn btn-sm btn-warning"><i class="fa fa-times"></i> Close Search</a>';
									}
									?>
						        </div>
						        
								<div class="pull-right">

								 <a href="<?php echo site_url()?>hospital-administration/add-department-account" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Add department account </a>

								</div>
					</div>
				</div>
				 <div class="row">
				 		<div class="col-md-12">
						<?php
								$error = $this->session->userdata('error_message');
								$success = $this->session->userdata('success_message');
								
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
								$result = '';
								
								//if users exist display them
								if ($query->num_rows() > 0)
								{
									$count = $page;
									
									$result .= 
										'
											<table class="table table-hover table-bordered ">
											  <thead>
												<tr>
												  <th>#</th>
												  <th>Department</th>
												  <th>Account name</th>
												  <th>Account Type</th>
												  <th>Status</th>
												  <th colspan="4">Actions</th>
												</tr>
											  </thead>
											  <tbody>
										';
									
									//get all administrators
									$administrators = $this->personnel_model->retrieve_personnel();
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
										
										$department_account_id = $row->department_account_id;
										$department_account_status = $row->department_account_status;
										$account_name = $row->account_name;
										$account_type_name = $row->account_type_name;
										$department_name = $row->department_name;
										$created_by = $row->created_by;
										$modified_by = $row->modified_by;
										$last_modified = date('jS M Y H:i a',strtotime($row->last_modified));
										$created = date('jS M Y H:i a',strtotime($row->created));
										
										//create deactivated status display
										if($department_account_status == 0)
										{
											$status = '<span class="label label-important">Deactivated</span>';
											$button = '<a class="btn btn-info btn-sm" href="'.site_url().'hospital-administration/activate-department-accounts/'.$department_account_id.'" onclick="return confirm(\'Do you want to activate ?\');" title="Activate "><i class="fa fa-thumbs-up"></i> Activate</a>';
										}
										//create activated status display
										else if($department_account_status == 1)
										{
											$status = '<span class="label label-success">Active</span>';
											$button = '<a class="btn btn-default btn-sm" href="'.site_url().'hospital-administration/deactivate-department-accounts/'.$department_account_id.'" onclick="return confirm(\'Do you want to deactivate ?\');" title="Deactivate "><i class="fa fa-thumbs-down"></i> Deactivate</a>';
										}
										
										//creators & editors
										if($admins != NULL)
										{
											foreach($admins as $adm)
											{
												$user_id = $adm->personnel_id;
												
												if($user_id == $created_by)
												{
													$created_by = $adm->personnel_fname;
												}
												
												if($user_id == $modified_by)
												{
													$modified_by = $adm->personnel_fname;
												}
											}
										}
										
										else
										{
										}
										
										$count++;
										$result .= 
											'
												<tr>
													<td>'.$count.'</td>
													<td>'.$department_name.'</td>
													<td>'.$account_name.'</td>
													<td>'.$account_type_name.'</td>
													<td>'.$status.'</td>
													<td>'.$button.'</td>												
													<td><a href="'.site_url().'hospital-administration/delete-department-accounts/'.$department_account_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete this service?\')"><i class="fa fa-trash"></i> Delete</a></td>
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
									$result .= "There are no department accounts";
								}
								?>
						            <?php echo $result; ?>
						           </div>
						        </div>
						          </div>
						          
						          <div class="widget-foot">
						                                
										<?php if(isset($links)){echo $links;}?>
						            
						                <div class="clearfix"></div> 
						            
						            </div>
						        
								</section>
						        </div>
				</div>
						        