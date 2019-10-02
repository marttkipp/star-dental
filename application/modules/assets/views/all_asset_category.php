 <?php  
$result = ''; $count = 0;

$result .= 
			'
			<table class="table table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th>#</th>
						<th>Category Name</th>
						<th>Created</th>
						<th>Status</th>
						<th colspan="5">Actions</th>
					</tr>
				</thead>
				  <tbody>
				  
			';

   foreach ($query->result() as $row)
			{
				$asset_category_id = $row->asset_category_id;
				$asset_category_name = $row->asset_category_name;
				$asset_category_status = $row->asset_category_status;
				$created_by = $row->created_by;
				$created = date('jS M Y H:i a',strtotime($row->created));
				
				//create deactivated status display
				if($asset_category_status == 0)
				{
					$status = '<span class="label label-default">Deactivated</span>';
					$button = '<a class="btn btn-info btn-sm" href="'.site_url().'asset-category/activate-asset-category/'.$asset_category_id.'" onclick="return confirm(\'Do you want to activate '.$asset_category_name.'?\');" title="Activate '.$asset_category_name.'"><i class="fa fa-thumbs-up"></i> Activate</a>';
				}
				//create activated status display
				else if($asset_category_status == 1)
				{
					$status = '<span class="label label-success">Active</span>';
					$button = '<a class="btn btn-default btn-sm" href="'.site_url().'asset-category/deactivate-asset-category/'.$asset_category_id.'" onclick="return confirm(\'Do you want to deactivate '.$asset_category_name.'?\');" title="Deactivate '.$asset_category_name.'"><i class="fa fa-thumbs-down"></i> Deactivate</a>';
				}
				
				//creators & editors
				
				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$asset_category_name.'</td>
						<td>'.$created.'</td>
						<td>'.$status.'</td>
						<td><a href="'.site_url().'asset-category/edit-asset-category/'.$asset_category_id.'" class="btn btn-sm btn-info" title="Edit '.$asset_category_name.'"><i class="fa fa-pencil"></i> Edit</a></td>
						<td>'.$button.'</td>
						<td><a href="'.site_url().'asset-category/delete-asset-category/'.$asset_category_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$asset_category_name.'?\');" title="Delete '.$asset_category_name.'"><i class="fa fa-trash"></i> Delete</a></td>
					</tr> 
				';
			}
			
			$result .= 
			'
						</tbody>
						</table>
			';
		
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
                                    	<a href="<?php echo site_url();?>asset/add-asset-category" class="btn btn-success btn-sm pull-right">Add Asset Category</a>
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