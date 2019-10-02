 <?php  
$result = ''; $count = 0;

$result .= 
			'
			<table class="table table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th>#</th>
						<th>Asset Name</th>
						<th>Asset category</th>
						<th>Asset Value</th>
						<th>Description</th>
						<th>status</th>
						<th>Created</th>
						<th colspan="9">Actions</th>
					</tr>
				</thead>
				  <tbody>
				  
			';

   foreach ($query->result() as $row)
			{
				$asset_id = $row->asset_id;
				$asset_name = $row->asset_name;
				$asset_status = $row->asset_status;
				$asset_serial_no = $row->asset_serial_no;
				$asset_description = $row->asset_description;
				$asset_category_name = $row->asset_category_name;
				$asset_model_no = $row->asset_model_no;
				$asset_pd_period = $row->asset_pd_period;
				$ldl_type = $row->ldl_type;
				$ldl_date = $row->ldl_date;
				$asset_category = $row->ldl_date;
				$asset_supplier_no = $row->asset_supplier_no;
				$asset_project_no = $row->asset_project_no;
				$asset_owner_name = $row->asset_owner_name;
				$asset_number = $row->asset_number;
				$asset_value = $row->asset_value;
				$asset_inservice_period = $row->asset_inservice_period;
				$asset_disposal_period = $row->asset_disposal_period;
				$created = date('jS M Y',strtotime($row->created));
				
				//create deactivated status display
				if($asset_status == 0)
				{
					$status = '<span class="label label-default">Deactivated</span>';
					$button = '<a class="btn btn-info btn-sm" href="'.site_url().'assets/activate-asset/'.$asset_id.'" onclick="return confirm(\'Do you want to activate '.$asset_name.'?\');" title="Activate '.$asset_name.'"><i class="fa fa-thumbs-up"></i> Activate</a>';
				}
				//create activated status display
				else if($asset_status == 1)
				{
					$status = '<span class="label label-success">Active</span>';
					$button = '<a class="btn btn-default btn-sm" href="'.site_url().'assets/deactivate-asset/'.$asset_id.'" onclick="return confirm(\'Do you want to deactivate '.$asset_name.'?\');" title="Deactivate '.$asset_name.'"><i class="fa fa-thumbs-down"></i> Deactivate</a>';
				}
				
				//creators & editors
				
				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$asset_name.'</td>
						<td>'.$asset_category_name.'</td>
						<td>'.$asset_value.'</td>
						<td>'.$asset_description.'</td>
						<td>'.$status.'</td>
						<td>'.$created.'</td>
						<td><a href="'.site_url().'assets/edit-asset/'.$asset_id.'" class="btn btn-sm btn-info" title="Edit '.$asset_name.'"><i class="fa fa-pencil"></i> Edit</a></td>
						<td>'.$button.'</td>
						<td><a href="'.site_url().'assets/delete-asset/'.$asset_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$asset_name.'?\');" title="Delete '.$asset_name.'"><i class="fa fa-trash"></i> Delete</a></td>
					</tr> 
				';
			}
			
			$result .= 
			'
						</tbody>
						</table>
			';
		
?>
<?php echo $this->load->view('search_asset', '', TRUE);?>
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
            	<?php
					$search = $this->session->userdata('asset_search');
		
					if(!empty($search))
					{
						echo '<a href="'.site_url().'assets/assets/close_asset" class="btn btn-warning btn-sm pull-left">Close Search</a>';
					}
					?>
            	<a href="<?php echo site_url();?>assets/add-asset" class="btn btn-success btn-sm pull-right">Add Asset</a>
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