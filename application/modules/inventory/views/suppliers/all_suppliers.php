 <?php echo $this->load->view('search/search_supplier', '', TRUE);?><!-- search -->

 <section class="panel">
    <header class="panel-heading">
          <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?></h4>
          <div class="widget-icons pull-right">
            	<a href="<?php echo base_url();?>procurement/add-supplier" class="btn btn-primary pull-right btn-sm">Add supplier</a>
          </div>
          <div class="clearfix"></div>
        </header>
      	<div class="panel-body">
		<?php 
		$v_data['view_type'] = 0;
		//echo $this->load->view('inventory-setup/search/search_categories', $v_data, TRUE); ?>
		<?php

				$error = $this->session->userdata('error_message');
				$success = $this->session->userdata('success_message');
				$search_result ='';
				$search_result2  ='';
				if(!empty($error))
				{
					$search_result2 = '<div class="alert alert-danger">'.$error.'</div>';
					$this->session->unset_userdata('error_message');
				}
				
				if(!empty($success))
				{
					$search_result2 ='<div class="alert alert-success">'.$success.'</div>';
					$this->session->unset_userdata('success_message');
				}
						
				$search = $this->session->userdata('search_suppliers');
				
				if(!empty($search))
				{
					$search_result = '<a href="'.site_url().'inventory/suppliers/close_search_suppliers" class="btn btn-danger">Close Search</a>';
				}


				$result = '<div class="padd">';	
				$result .= ''.$search_result2.'';
				$result .= '
							<div class="row" style="margin-bottom:8px;">
								<div class="pull-left">
								'.$search_result.'
								</div>
			            		<div class="pull-right">
								
								
								</div>
							</div>
						';
					
					
					//if users exist display them
					if ($query->num_rows() > 0)
					{
						$count = $page;
						
						$result .= 
						'
						<div class="row">
								<div class="col-md-12">
								<table class="example table-autosort:0 table-stripeclass:alternate table table-hover table-bordered " id="TABLE_2">
								  <thead>
									<tr>
									  <th class="table-sortable:default table-sortable" title="Click to sort">#</th>
									  <th class="table-sortable:default table-sortable" title="Click to sort">Supplier Name</th>
									  <th class="table-sortable:default table-sortable" title="Click to sort">Date Created</th>
									  <th class="table-sortable:default table-sortable" title="Click to sort">Last Modified</th>
									  <th>Status</th>
									  <th colspan="3">Actions</th>
									</tr>
								  </thead>
								  <tbody>
							';
							
							//get all administrators
							$personnel_query = $this->personnel_model->get_all_personnel();
							
							
							foreach ($query->result() as $row)
							{
								$creditor_id = $row->creditor_id;
								$creditor_name = $row->creditor_name;
								$creditor_status = $row->creditor_status;
								$creditor_phone = $row->creditor_phone;
								$created_by = $row->created_by;
								$modified_by = $row->modified_by;
								$creditor_email = $row->creditor_email;

								
								//status
								if($creditor_status == 1)
								{
									$status = 'Active';
								}
								else
								{
									$status = 'Disabled';
								}
								$creditor_parent = '-';
								
								
								//create deactivated status display
								if($creditor_status == 0)
								{
									$status = '<span class="label label-danger">Deactivated</span>';
									$button = '<a class="btn btn-info" href="'.site_url().'procurement/activate-supplier/'.$creditor_id.'" onclick="return confirm(\'Do you want to activate '.$creditor_name.'?\');">Activate</a>';
								}
								//create activated status display
								else if($creditor_status == 1)
								{
									$status = '<span class="label label-success">Active</span>';
									$button = '<a class="btn btn-default" href="'.site_url().'procurement/deactivate-supplier/'.$creditor_id.'" onclick="return confirm(\'Do you want to deactivate '.$creditor_name.'?\');">Deactivate</a>';
								}
								
								//creators & editors
								//creators and editors
							if($personnel_query->num_rows() > 0)
							{
								$personnel_result = $personnel_query->result();
								
								foreach($personnel_result as $adm)
								{
									$personnel_id2 = $adm->personnel_id;
									
									if($created_by == $personnel_id2)
									{
										$created_by = $adm->personnel_fname;
										break;
									}
									
									else
									{
										$created_by = '-';
									}
								}
							}
							
							else
							{
								$created_by = '-';
							}
								
								
								$count++;
								$result .= 
								'
									<tr>
										<td>'.$count.'</td>
										<td>'.$creditor_name.'</td>
										<td>'.date('jS M Y H:i a',strtotime($row->created)).'</td>
										<td>'.date('jS M Y H:i a',strtotime($row->last_modified)).'</td>
										<td>'.$status.'</td>
										<td><a href="'.site_url().'procurement/edit-supplier/'.$creditor_id.'" class="btn btn-sm btn-success">Edit</a></td>
										<td>'.$button.'</td>
										<td><a href="'.site_url().'procurement/delete-supplier/'.$creditor_id.'" class="btn btn-sm btn-danger">Delete</a></td>
										
									
									</tr> 
								';
							}
							
							$result .= 
							'
										  </tbody>
										</table>
								</div>
							</div>
						';
					}
					
					else
					{
						$result .= "There are no categories";
					}
					$result .= '</div>';
					echo $result;
			?>
			<div class="widget-foot">
		    <?php
		    if(isset($links)){echo $links;}
		    ?>
		    </div>
		</div>
	</section>
