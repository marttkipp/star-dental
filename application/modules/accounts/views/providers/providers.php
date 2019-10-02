<!-- search -->

<!-- end search -->
<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title"><?php echo $title;?> </h2>
        <a href="<?php echo site_url();?>accounts/creditors/add_creditor" class="btn btn-sm btn-primary pull-right" style="margin-top: -25px;"><i class="fa fa-plus"></i> Add creditors</a>
                	
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
            
           	<div style="min-height:30px;">
            	<div class="pull-right">
                	<?php
					$search = $this->session->userdata('search_creditors');
		
					if(!empty($search))
					{
						echo '<a href="'.site_url().'accounts/creditors/close_search_creditors" class="btn btn-warning btn-sm">Close Search</a>';
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
						  <th>Provider name</th>
						  <th>Opening Balance</th>
						  <th>Total payments</th>
						  <th>Total invoice</th>
						  <th>Account Balance</th>
						  <th colspan="2">Actions</th>
						</tr>
					  </thead>
					  <tbody>
				';
			
			foreach ($query->result() as $row)
			{
				$count++;
				$personnel_id = $row->personnel_id;
				$personnel_fname = $row->personnel_fname;
				// var_dump($personnel_id);die();
				$response = $this->petty_cash_model->get_provider_statement($personnel_id);	
				$invoice_total = $response['total_arrears'];
				$payments_total = $response['total_payment_amount'];
				//$payments_total = 0;
				$creditor_status = $row->creditor_status;
				
				
				
				$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$personnel_fname.'</td>
							<td>'.number_format($opening_balance, 2).'</td>
							<td>'.number_format($payments_total, 2).'</td>
							<td>'.number_format($invoice_total, 2).'</td>
							<td>'.number_format($payments_total - $invoice_total, 2).'</td>
							<td><a href="'.site_url().'accounts/provider-statement/'.$personnel_id.'" class="btn btn-sm btn-info" >Statement</a></td>
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