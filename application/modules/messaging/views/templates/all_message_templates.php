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
						<th>Message Template Code</th>
						<th>Template Description</th>
						<th>Date Created</th>
						<th>Last modified</th>
						<th>Status</th>
						<th colspan="5">Actions</th>
					</tr>
				</thead>
				  <tbody>
				  
			';
			// $this->load->model('messaging/users_model');
			// //get all administrators
			// $administrators = $this->users_model->get_all_administrators();
			// if ($administrators->num_rows() > 0)
			// {
			// 	$admins = $administrators->result();
			// }
			
			// else
			// {
			// 	$admins = NULL;
			// }
			
			foreach ($query->result() as $row)
			{
				$message_template_id = $row->message_template_id;
				$message_template_code = $row->message_template_code;
				$message_template_status = $row->message_template_status;
				$created_by = $row->created_by;
				$modified_by = $row->modified_by;
				$message_template_description = $row->message_template_description;
				
				//status
				if($message_template_status == 1)
				{
					$status = 'Active';
				}
				else
				{
					$status = 'Disabled';
				}
			
				//create deactivated status display
				if($message_template_status == 0)
				{
					$status = '<span class="label label-warning">Deactivated</span>';
					$button = '<a class="btn btn-info" href="'.site_url().'messaging/activate-message-template/'.$message_template_id.'" onclick="return confirm(\'Do you want to activate '.$message_template_code.'?\');" title="Activate '.$message_template_code.'"><i class="fa fa-thumbs-up"></i></a>';
				}
				//create activated status display
				else if($message_template_status == 1)
				{
					$status = '<span class="label label-success">Active</span>';
					$button = '<a class="btn btn-default" href="'.site_url().'messaging/deactivate-message-template/'.$message_template_id.'" onclick="return confirm(\'Do you want to deactivate '.$message_template_code.'?\');" title="Deactivate '.$message_template_code.'"><i class="fa fa-thumbs-down"></i></a>';
				}
				
				//creators & editors
				// if($admins != NULL)
				// {
				// 	foreach($admins as $adm)
				// 	{
				// 		$user_id = $adm->user_id;
						
				// 		if($user_id == $created_by)
				// 		{
				// 			$created_by = $adm->first_name;
				// 		}
						
				// 		if($user_id == $modified_by)
				// 		{
				// 			$modified_by = $adm->first_name;
				// 		}
				// 	}
				// }
				
				// else
				// {
				// }
				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$message_template_code.'</td>
						<td>'.$message_template_description.'</td>
						<td>'.date('jS M Y H:i a',strtotime($row->created)).'</td>
						<td>'.date('jS M Y H:i a',strtotime($row->last_modified)).'</td>
						<td>'.$status.'</td>
						
						<td><a href="'.site_url().'template-detail/'.$message_template_id.'" class="btn btn-sm btn-warning" title="Template Detail '.$message_template_code.'">Detail</a></td>
						<td>
							
							<!-- Button to trigger modal -->
							<a href="#user'.$message_template_id.'" class="btn btn-primary" data-toggle="modal" title="Expand '.$message_template_code.'"><i class="fa fa-plus"></i></a>
							
							<!-- Modal -->
							<div id="user'.$message_template_id.'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
											<h4 class="modal-title">'.$message_template_code.'</h4>
										</div>
										
										<div class="modal-body">
											<table class="table table-stripped table-condensed table-hover">
												<tr>
													<th>Message Template Code</th>
													<td>'.$message_template_code.'</td>
												</tr>
												<tr>
													<th>Template Description</th>
													<td>'.$message_template_description.'</td>
												</tr>
												<tr>
													<th>Status</th>
													<td>'.$status.'</td>
												</tr>
											
												<tr>
													<th>Date Created</th>
													<td>'.date('jS M Y H:i a',strtotime($row->created)).'</td>
												</tr>
												
												
											</table>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
											<a href="'.site_url().'messaging/edit-message-template/'.$message_template_id.'" class="btn btn-sm btn-success" title="Edit '.$message_template_code.'"><i class="fa fa-pencil"></i></a>
											'.$button.'
											<a href="'.site_url().'messaging/delete-message-template/'.$message_template_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$message_template_code.'?\');" title="Delete '.$message_template_code.'"><i class="fa fa-trash"></i></a>
										</div>
									</div>
								</div>
							</div>
						
						</td>
						<td><a href="'.site_url().'messaging/edit-message-template/'.$message_template_id.'" class="btn btn-sm btn-success" title="Edit '.$message_template_code.'"><i class="fa fa-pencil"></i></a></td>
						
						<td>'.$button.'</td>
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
			$result .= "There are no message templates";
		}
?>

<section class="panel">
	<header class="panel-heading">
					
		<h2 class="panel-title"><?php echo $title;?></h2>
		<a href="<?php echo site_url();?>messaging/add-template" class="btn btn-sm btn-success pull-right" style="margin-top:-25px">Add Message Template</a>

	</header>
	<div class="panel-body">
    	<div class="row" style="margin-bottom:20px;">
            <div class="col-lg-12">
            </div>
        </div>
		<div class="table-responsive">
        	
			<?php echo $result;?>
	
        </div>
	</div>
</section>

