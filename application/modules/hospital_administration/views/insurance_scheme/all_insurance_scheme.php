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
						<th><a href="'.site_url().'admin/insurance_scheme/insurance_scheme_name/'.$order_method.'/'.$page.'">Scheme</a></th>
						<th>Insurance company</th>
						<th><a href="'.site_url().'admin/insurance_scheme/last_modified/'.$order_method.'/'.$page.'">Last modified</a></th>
						<th><a href="'.site_url().'admin/insurance_scheme/modified_by/'.$order_method.'/'.$page.'">Modified by</a></th>
						<th><a href="'.site_url().'admin/insurance_scheme/insurance_scheme_status/'.$order_method.'/'.$page.'">Status</a></th>
						<th colspan="5">Actions</th>
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
				$insurance_scheme_id = $row->insurance_scheme_id;
				$insurance_scheme_name = $row->insurance_scheme_name;
				$visit_type_name = $row->visit_type_name;
				$insurance_scheme_status = $row->insurance_scheme_status;
				$branch_id = $row->branch_id;
				$created_by = $row->created_by;
				$modified_by = $row->modified_by;
				$last_modified = date('jS M Y H:i a',strtotime($row->last_modified));
				$created = date('jS M Y H:i a',strtotime($row->created));
				if($branch_id == 2)
				{
					$invoice_type = "Prime Care Clinic";
				}
				else
				{
					$invoice_type = "Dr Joseph Karanja";
				}
				//create deactivated status display
				if($insurance_scheme_status == 0)
				{
					$status = '<span class="label label-default">Deactivated</span>';
					$button = '<a class="btn btn-info btn-sm" href="'.site_url().'hospital-administration/activate-insurance-scheme/'.$insurance_scheme_id.'" onclick="return confirm(\'Do you want to activate '.$insurance_scheme_name.'?\');" title="Activate '.$insurance_scheme_name.'"><i class="fa fa-thumbs-up"></i> Activate</a>';
				}
				//create activated status display
				else if($insurance_scheme_status == 1)
				{
					$status = '<span class="label label-success">Active</span>';
					$button = '<a class="btn btn-default btn-sm" href="'.site_url().'hospital-administration/deactivate-insurance-scheme/'.$insurance_scheme_id.'" onclick="return confirm(\'Do you want to deactivate '.$insurance_scheme_name.'?\');" title="Deactivate '.$insurance_scheme_name.'"><i class="fa fa-thumbs-down"></i> Deactivate</a>';
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
						<td>'.$insurance_scheme_name.'</td>
						<td>'.$visit_type_name.'</td>
						<td>'.$last_modified.'</td>
						<td>'.$modified_by.'</td>
						<td>'.$status.'</td>
						<td><a href="'.site_url().'hospital-administration/edit-insurance-scheme/'.$insurance_scheme_id.'" class="btn btn-sm btn-info" title="Edit '.$insurance_scheme_name.'"><i class="fa fa-pencil"></i> Edit</a></td>
						<td>'.$button.'</td>
						<td><a href="'.site_url().'hospital-administration/delete-insurance-scheme/'.$insurance_scheme_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$insurance_scheme_name.'?\');" title="Delete '.$insurance_scheme_name.'"><i class="fa fa-trash"></i> Delete</a></td>
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
			$result .= "There are no visit types";
		}
?>

						<section class="panel">
							<header class="panel-heading">
								<div class="panel-actions">
									<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
								</div>
						
								<h2 class="panel-title"><?php echo $title;?></h2>
							</header>
							<div class="panel-body">
                            	<div class="row" style="margin-bottom:20px;">
                                    <div class="col-lg-12">
                                    	<a href="<?php echo site_url();?>hospital-administration/add-insurance-scheme" class="btn btn-success btn-sm pull-right">Add Insurance Scheme</a>
                                    </div>
                                </div>
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
								?>
								<div class="table-responsive">
                                	
									<?php echo $result;?>
							
                                </div>
							</div>
                            
                            <div class="panel-foot">
                                
								<?php if(isset($links)){echo $links;}?>
                            
                                <div class="clearfix"></div> 
                            
                            </div>
						</section>