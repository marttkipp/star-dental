
<!-- search -->
<?php echo $this->load->view('search_accounts', '', TRUE);?>


        

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
						<th>Account</th>
						<th>Parent Account</th>
						<th>Account Type</th>
						<th>Opening Balance</th>						
						<th colspan="4">Actions</th>
					</tr>
				</thead>
				  <tbody>
				  
			';
			foreach ($query->result() as $row)
			{
				$account_id = $row->account_id;
				$account_name = $row->account_name;
				$account_type_id = $row->account_type_id;
				$account_type_name = $row->account_type_name;
				$account_opening_balance = $row->account_opening_balance;
				$account_status = $row->account_status;
				$parent_account = $row->parent_account;
				$parent_account_name = '';
				if($parent_account > 0)
				{
					$parent_account_name = $this->company_financial_model->get_parent_account($parent_account);
				}
				
				//status
				if($account_status == 1)
				{
					$status = 'Active';
				}
				else
				{
					$status = 'Disabled';
				}
				
				//create deactivated status display
				if($account_status == 0)
				{
					$status = '<span class="label label-default">Deactivated</span>';
					$button = '<a class="btn btn-info" href="'.site_url().'accounting/charts-of-accounts/activate-account/'.$account_id.'" onclick="return confirm(\'Do you want to activate '.$account_name.'?\');" title="Activate '.$account_name.'"><i class="fa fa-thumbs-up"></i></a>';
				}
				//create activated status display
				else if($account_status == 1)
				{
					$status = '<span class="label label-success">Active</span>';
					$button = '<a class="btn btn-default" href="'.site_url().'accounting/charts-of-accounts/deactivate-account/'.$account_id.'" onclick="return confirm(\'Do you want to deactivate '.$account_name.'?\');" title="Deactivate '.$account_name.'"><i class="fa fa-thumbs-down"></i></a>';
				}
				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$account_name.'</td>
						<td>'.$parent_account_name.'</td>
						<td>'.$account_type_name.'</td>
						<td>'.$account_opening_balance.'</td>
						<td>'.$status.'</td>
						<td><a href="'.site_url().'accounting/charts-of-accounts/edit-account/'.$account_id.'" class="btn btn-sm btn-success" title="Edit '.$account_name.'"><i class="fa fa-pencil"></i></a></td>
						<td>'.$button.'</td>
						<td><a href="'.site_url().'accounting/charts-of-accounts/delete-account/'.$account_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete '.$account_name.'?\');" title="Delete '.$account_name.'"><i class="fa fa-trash"></i></a></td>
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
			$result .= "There are no personnel";
		}
?>

<section class="panel">
    <header class="panel-heading">						
        <h2 class="panel-title"><?php echo $title;?></h2>
        <div class="pull-right">
        	 <a href="<?php echo site_url();?>accounting/add-account" style="margin-top:-40px;" class="btn btn-sm btn-info"><i class="fa fa-plus"></i> Add Account</a>
        </div>
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
        <?php
					$search = $this->session->userdata('search_petty_cash1');
		
					if(!empty($search))
					{
						echo '<a href="'.site_url().'accounting/petty_cash/close_search_petty_cash" class="btn btn-warning btn-sm">Close Search</a>';
					}
					?>
       
        <div class="table table-bordered table-striped table-condensed">
            
            <?php echo $result;?>
    
        </div>
    </div>
    <div class="panel-footer">
        <?php if(isset($links)){echo $links;}?>
    </div>
</section>