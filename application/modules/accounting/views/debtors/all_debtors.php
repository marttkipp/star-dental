<!-- search -->
<?php //echo $this->load->view('search/debtor_search', '', TRUE);?>

<!-- end search -->
<section class="panel">
    <header class="panel-heading">
        <h2 class="panel-title"><?php echo $title;?> </h2>
        <!-- <a href="<?php echo site_url();?>accounting/creditors/add_creditor" class="btn btn-sm btn-primary pull-right" style="margin-top: -25px;"><i class="fa fa-plus"></i> Add creditors</a> -->
                	
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
					$search = $this->session->userdata('close_search_hospital_debtors');
		
					if(!empty($search))
					{
						echo '<a href="'.site_url().'accounting/debtors/search_hospital_debtors" class="btn btn-warning btn-sm">Close Search</a>';
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
						  <th>Creditor name</th>
						  <th>Opening</th>
						  <th>Current</th>
						  <th>30 Days</th>
						  <th>60 Days</th>
						  <th> 90 Days</th>
						  <th>120 Days</th>
						  <th>> 120 days</th>
						  <th>Payments</th>
						  <th>Balance</th>
						  <th colspan="3">Actions</th>
						</tr>
					  </thead>
					  <tbody>
				';
				$total_this_month = 0;
				$total_three_months = 0;
				$total_six_months = 0;
				$total_nine_months = 0;
				$total_payments = 0;
				$total_invoices =0;
				$total_balance = 0;
				$total_one_twenty_months = 0;
				$total_over_one_twenty_months = 0;
			
			foreach ($query->result() as $row)
			{
				$count++;
				$visit_type_id = $row->visit_type_id;
				$visit_type_name = $row->visit_type_name;
				$opening_balance = $row->opening_balance;
				$debit_id = $row->debit_id;
				$opening_date = $row->created;

				// $invoice_total = $this->creditors_model->get_invoice_total($visit_type_id);
				// $payments_total = $this->creditors_model->get_payments_total($visit_type_id);
				//$payments_total = 0;

				// var_dump($visit_type_id); die();
				$visit_type_status = $row->visit_type_status;
				
				if($visit_type_status == 1)
				{
					$checked_active = 'checked';
					$checked_inactive = '';
				}
				else
				{
					$checked_active = '';
					$checked_inactive = 'checked';
				}
				// var_dump($invoice_total);
				if($debit_id == 2)
				{
					$opening_balance = $opening_balance;	
				}
				else
				{
					$opening_balance = -$opening_balance;
				}

				$payments_total = $this->debtors_model->get_debtor_total_payments($visit_type_id);


				$date = date('Y-m-d');
	            $this_month = $this->debtors_model->get_debtor_statement_value($visit_type_id,$date,1);
	            $three_months = $this->debtors_model->get_debtor_statement_value($visit_type_id,$date,2);
	            $six_months = $this->debtors_model->get_debtor_statement_value($visit_type_id,$date,3);
	            $nine_months = $this->debtors_model->get_debtor_statement_value($visit_type_id,$date,4);
	            $one_twenty_days = $this->debtors_model->get_debtor_statement_value($visit_type_id,$date,5);
	            $over_one_twenty_days = $this->debtors_model->get_debtor_statement_value($visit_type_id,$date,6);

	            $total_this_month +=$this_month;
	            $total_three_months +=$three_months;
	            $total_six_months +=$six_months;
	            $total_nine_months +=$nine_months;
	            $total_one_twenty_months +=$one_twenty_days;
	            $total_over_one_twenty_months +=$over_one_twenty_days;
	            $total_payments += $payments_total;
	            $total_invoices += $invoice_total;
	            $invoice_total = $this_month + $three_months + $six_months + $nine_months + $one_twenty_days + $over_one_twenty_days;
	            $total_balance += $invoice_total-$payments_total;


				$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.strtoupper($visit_type_name).'</td>
							<td>'.number_format($opening_balance, 2).'</td>
							<td>'.number_format($this_month, 2).'</td>
							<td>'.number_format($three_months, 2).'</td>
							<td>'.number_format($six_months, 2).'</td>
							<td>'.number_format($nine_months, 2).'</td>
							<td>'.number_format($one_twenty_days, 2).'</td>
							<td>'.number_format($over_one_twenty_days, 2).'</td>
							<td>'.number_format($payments_total, 2).'</td>
							<td>'.number_format($invoice_total+$opening_balance, 2).'</td>
							<td><a href="'.site_url().'accounting/debtor-statement/'.$visit_type_id.'" class="btn btn-sm btn-info" >Account</a></td>
						
							<td><button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#book-appointment'.$visit_type_id.'"><i class="fa fa-plus"></i> Balance </button>
								<div class="modal fade " id="book-appointment'.$visit_type_id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
								    <div class="modal-dialog modal-lg" role="document">
								        <div class="modal-content ">
								            <div class="modal-header">
								            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								            	<h4 class="modal-title" id="myModalLabel">Update Balance '.$personnel_fname.' '.$personnel_onames.'</h4>
								            </div>
								            '.form_open("update-debtor-balance/".$visit_type_id, array("class" => "form-horizontal")).'

								            <div class="modal-body">
								            	<div class="row">
								            		<input type="hidden" name="redirect_url" id="redirect_url'.$visit_type_id.'" value="'.$this->uri->uri_string().'">
								            		<div class="col-md-12">
								            			<div class="col-md-4">
								            				<div class="form-group">
																<label class="col-lg-4 control-label">From: </label>
																
																<div class="col-lg-8">
							                                        <div class="input-group">
							                                            <span class="input-group-addon">
							                                                <i class="fa fa-calendar"></i>
							                                            </span>
							                                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="start_date" id="scheduledate" placeholder="Date" value="'.$opening_date.'" required>
							                                        </div>
																</div>
															</div>
														</div>
														<div class="col-md-4">
								            				<div class="form-group">
																<label class="col-lg-4 control-label">Amount: </label>
																
																<div class="col-lg-8">
							                                        <input class="form-control" name="opening_balance" id="procedure_done" value="'.$opening_balance.'">
											                           
																</div>
															</div>
														</div>
														<div class="col-md-4">
								            				<div class="form-group">
																<label class="col-lg-5 control-label">Prepayment ?</label>
																<div class="col-lg-3">
																	<div class="radio">
																		<label>
																		<input id="optionsRadios5" type="radio" value="1" name="debit_id" checked="'.$payment.'">
																		Yes
																		</label>
																	</div>
																</div>
																<div class="col-lg-3">
																	<div class="radio">
																		<label>
																		<input id="optionsRadios6" type="radio" value="2" name="debit_id" checked="'.$invoice.'">
																		No
																		</label>
																	</div>
																</div>
															</div>
														</div> 
								            			
								            		</div>
								            	</div>
								            	
														
								              	
								            </div>
								            <div class="modal-footer">
								            	<button  class="btn btn-sm btn-success" type="submit">Update Opening Balance</a>
								                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
								            </div>

								               '.form_close().'
								        </div>
								    </div>
								</div>

							</td>
							</tr>
							';
				
			}
			
			$result .= '<tr>
							<td colspan=3></td>
							<td>'.number_format($total_this_month, 2).'</td>
							<td>'.number_format($total_three_months, 2).'</td>
							<td>'.number_format($total_six_months, 2).'</td>
							<td>'.number_format($total_nine_months, 2).'</td>
							<td>'.number_format($total_one_twenty_months, 2).'</td>
							<td>'.number_format($total_over_one_twenty_months, 2).'</td>
							<td>'.number_format($total_payments, 2).'</td>
							<td>'.number_format($total_invoices, 2).'</td>
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