<!-- search -->

<div class="row">
    <div class="col-md-12">

        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	 <h2 class="panel-title"><?php echo $title;?></h2>
            	  <a href="<?php echo site_url();?>company-financials/profit-and-loss"  class="btn btn-sm btn-warning pull-right" style="margin-top:-25px;margin-left:5px" > Back to P & L </a>
            	 <a href="<?php echo site_url();?>company-financials/export-salary"  class="btn btn-sm btn-success pull-right" style="margin-top:-25px;" download> Export Payroll </a>
            </header>             

          <!-- Widget content -->
                <div class="panel-body">
          <h5 class="center-align"><?php echo $this->session->userdata('search_title');?></h5>
<?php
		$result = '';
		// $search = $this->session->userdata('debtors_search_query');
		// if(!empty($search))
		// {
		// 	echo '<a href="'.site_url().'accounting/reports/close_reports_search" class="btn btn-sm btn-warning">Close Search</a>';
		// }
		
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;
			
			$result .= 
				'
					<table class="table table-hover table-bordered table-striped table-responsive col-md-12">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Payrol Period</th>
						  <th>Amount</th>
						  
				';
				
			$result .= '
						
						</tr>
					  </thead>
					  <tbody>
			';
			
			// $personnel_query = $this->accounting_model->get_all_personnel();
			$total_payroll_amount = 0;
			foreach ($query->result() as $row)
			{
				$total_invoiced = 0;
				$period = date('M Y',strtotime($row->payroll_created_for));
				$total_payroll = $row->total_payroll;

				$total_payroll_amount += $total_payroll;
				$count++;
				
				$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$period.'</td>
							<td>'.number_format($total_payroll,2).'</td>

							';
					
				$result .= '
							
						</tr> 
				';
				
			}

			$result .= 
					'
						<tr>
							<td colspan=2> Totals</td>
							<td><strong>'.number_format($total_payroll_amount,2).'</strong></td>
						</tr> 
				';
			
			$result .= 
			'
						  </tbody>
						</table>
			';
		}
		
		else
		{
			$result .= "There are no visits";
		}
		
		echo $result;
?>
          </div>
          
          <div class="widget-foot">
                                
				<?php if(isset($links)){echo $links;}?>
            
                <div class="clearfix"></div> 
            
            </div>
        
		</section>
    </div>
  </div>