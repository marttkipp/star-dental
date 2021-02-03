<!-- search -->
<?php echo $this->load->view('search_creditor_amounts', '', TRUE);?>
<?php echo $this->load->view('creditors_statistics', '', TRUE);?>
<!-- end search -->
<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title"><?php echo $title;?> </h2>
        <a href="<?php echo site_url()?>print-creditors-report" target="_blank" class="btn btn-warning btn-sm pull-right" style="margin-top: -25px;"><i class="fa fa-print"></i> Print Report</a>
      
    </header>

    <!-- Widget content -->
    <div class="panel-body">
    	<div class="padd">
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
            
           	<div style="">
            	<div class="pull-right">
                	<?php
					$search = $this->session->userdata('search_hospital_creditors_list');
		
					if(!empty($search))
					{
						echo '<a href="'.site_url().'accounts/creditors/close_search_values_creditors" class="btn btn-warning btn-sm">Close Search</a>';
					}
					?>
                </div>
            </div>
                
<?php
		
		$result = '';
		
				// var_dump($query->result()); die();
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;
			
			$result .= '
					<table class="table table-hover table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Company Name</th>
						  <th>Invoice Number</th>
						  <th>Date</th>
						  <th>Amount</th>
						</tr>
					  </thead>
					  <tbody>
				';
			
			foreach ($query->result() as $row)
			{
				$count++;
				$creditor_id = $row->creditor_id;
				$creditor_name = $row->creditor_name;
				$opening_balance = $row->opening_balance;
				$debit_id = $row->debit_id;
				$creditor_account_amount = $row->creditor_account_amount;
				$creditor_account_date = $row->creditor_account_date;
				$transaction_code = $row->transaction_code;
				
				$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$creditor_name.'</td>
							<td>'.$transaction_code.'</td>
							<td>'.$creditor_account_date.'</td>
							<td>'.number_format($creditor_account_amount, 2).'</td>
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
			$result .= "There are no creditors";
		}
		
		echo $result;
?>
          </div>
          
          <div class="widget-foot">
                                
				<?php if(isset($links)){echo $links;}?>
            
                <div class="clearfix"></div> 
            
            </div>
        </div>
        <!-- Widget ends -->

      </div>
</section>