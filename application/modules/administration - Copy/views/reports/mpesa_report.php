<?php
	echo $this->load->view('reports/search/mpesa_search',"", true);
?>
<div class="row">
    <div class="col-md-12">
		<!-- Widget -->
		<section class="panel">
        	
			<!-- Widget head -->
			<header class="panel-heading">
				<h2 class="panel-title"><?php echo $title;?></h2>
			</header> 
            <?php
			
			$mpesa_search = $this->session->userdata('mpesa_search');
			if(!empty($mpesa_search))
			{
				?>
                <a href="<?php echo site_url().'administration/reports/close_mpesa_search';?>" class="btn btn-warning pull-left">Close Search</a>
                <?php
			}
			$mpesa_report_result = '';
			if($query->num_rows() > 0)
			{
				$count = 0;
			
				$mpesa_report_result.=  
					'
						<a href="'.site_url().'administration/reports/mpesa_reports_export" class="btn btn-success pull-right">Export</a>
						<table class="table table-hover table-bordered ">
				  			<thead>
								<tr>
								  <th>#</th>
								  <th>MPESA TX Code</th>
								  <th>Invoice Number</th>
								  <th>Amount</th>
								  <th>Payment Date</th>
								  <th>Patient Full Names</th>
								</tr>
							</thead>
							<tbody>
						';
				
				foreach($query->result() as $res)
				{//var_dump($query);die();
					$transaction_code = $res->transaction_code;
					$payment_created = $res->payment_created;
					$visit_id = $res->visit_id;
					$payment_for_name = $res->payment_for_name;
					$payment_amount = $res->amount_paid;
					$invoice_number = $this->session->userdata('branch_code').'-INV-00'.$visit_id;
					if(empty($payment_for_name))
					{
						$patient_fname = $res->patient_surname;
						$patient_oname = $res->patient_othernames;
						$patient_name = $patient_fname.' '.$patient_oname;
						
					}
					else
					{
						$patient_name = $payment_for_name;
					}
					$count++;
					
					
					$mpesa_report_result.= '
						<tr>
							<td>'.$count.'</td>
							<td>'.strtoupper($transaction_code).'</td>
							<td>'.$invoice_number.'</td>
							<td>'.number_format($payment_amount,2).'</td>
							<td>'.date('jS M Y',strtotime($payment_created)).'</td>
							<td>'.$patient_name.'</td>
						</tr>
					';
				}
			}
			else
			{
				$mpesa_report_result .= 'There are no MPESA payments';
			}
			?>            

			<!-- Widget content -->
			<div class="panel-body">
          		<?php
					echo $mpesa_report_result;
				?>
       		</div>
		</section>
        
        <div class="widget-footer">
                            
            <?php if(isset($links)){echo $links;}?>
        
            <div class="clearfix"></div> 
        
        </div>
	</div>
</div>