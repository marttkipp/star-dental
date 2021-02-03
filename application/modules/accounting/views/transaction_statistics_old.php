<div class="row statistics">
    
    <div class="col-md-12 col-sm-12">
    	 <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	<h2 class="panel-title">Transaction breakdown for <?php echo $total_visits;?> patients</h2>
            </header>             
        
              <!-- Widget content -->
              <div class="panel-body">
                <div class="row">
                   
                    <div class="col-md-3">
						<?php
                        	$total_invoices_revenue = $this->accounting_model->get_visit_invoice_totals();
                            $total_transfers = 0;//$this->accounting_model->get_visit_invoice_children_totals();
                            $total_payments_revenue = $this->accounting_model->get_visit_payment_totals();
                            $total_waiver_revenue = 0;//$this->accounting_model->get_visit_waiver_totals();
                            $total_debits_revenue = 0;//$this->accounting_model->get_visit_debits_totals();
                      
                            $all_payments_period =  $this->accounting_model->all_payments_period();
                            $receivable = $all_payments_period - $total_payments_revenue;
                            $total_rejected_amounts = 0;// $this->accounting_model->get_rejected_amounts();
                            // $total_invoices_revenue -= $total_waiver_revenue;


                            // get payments done today and visits not for today
                            // var_dump($total_payments_revenue); die();
                            $cash_debt_repayments = 0;//$this->accounting_model->get_all_visit_payments_totals(1,1);
                            $insurance_debt_repayments = 0;//$this->accounting_model->get_all_visit_payments_totals(2,1);

                            // var_dump($receivable);die();


                            if($receivable < 0)
                            {
                                $receivable = ($receivable);
                            }

                        ?>
                        <?php
                        // var_dump($receivable);die();
                        $patients = $this->accounting_model->get_patients_visits(1);

                        // $returning_patients = $this->accounting_model->get_patients_visits(0);
                        ?>
                        <h5>VISIT TYPE BREAKDOWN</h5>
                        <table class="table table-striped table-hover table-condensed">
                            <tbody>
                                 <tr>
                                    <th>TOTAL INVOICES</th>
                                    <td><?php echo number_format($total_invoices_revenue, 2);?></td>
                                </tr>
                                
                               <!-- <tr>
                                    <th>TOTAL DEBITS</th>
                                    <td><?php echo number_format($total_debits_revenue, 2);?></td>
                                </tr> -->
                                <tr>
                                    <th>TOTAL COLLECTION</th>
                                    <td>(<?php echo number_format($total_payments_revenue, 2);?>)</td>
                                </tr>
                                 <!-- <tr>
                                    <th>TOTAL WAIVERS</th>
                                    <td>(<?php echo number_format($total_waiver_revenue, 2);?>)</td>
                                </tr> -->

                                <tr>
                                    <th>DEBT BALANCE</th>
                                    <td><?php echo number_format(($total_invoices_revenue +$total_debits_revenue - $total_payments_revenue - $total_waiver_revenue), 2);?></td>
                                </tr>
                            </tbody>
                        </table>
                   
                    

                         <h5>VISIT  BREAKDOWN</h5>
                        <table class="table table-striped table-hover table-condensed">
                            <tbody>
                                <tr>
                                    <th>NEW PATIENTS</th>
                                    <td><?php echo $patients['total_new'];?></td>
                                </tr>
                                <tr>
                                    <th>RETURNING PATIENTS</th>
                                    <td><?php echo $patients['total_old'];?></td>
                                </tr>
                                 <tr>
                                    <th>TOTAL PATIENTS</th>
                                    <td><?php echo $patients['total_new'] + $patients['total_old'];?></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="clearfix"></div>
            		</div>
                    <!-- End Transaction Breakdown -->
                   
                   
                    <div class="col-md-2">
                       
                        <h5>PAYMENT METHODS BREAKDOWN</h5>
                        <table class="table table-striped table-hover table-condensed">
                            <tbody>
								<?php
								$total_cash_breakdown = 0;

								$payment_methods = $this->accounting_model->get_payment_methods();
                                if($payment_methods->num_rows() > 0)
                                {
                                    foreach($payment_methods->result() as $res)
                                    {
                                        $method_name = $res->payment_method;
                                        $payment_method_id = $res->payment_method_id;

                                        $total = $this->accounting_model->get_amount_collected($payment_method_id);
                                        
                                        
                                        echo 
										'
										<tr>
											<th>'.$method_name.'</th>
											<td>'.number_format($total, 2).'</td>
										</tr>
										';
										$total_cash_breakdown += $total;
                                    }
                                    
									echo 
									'
									<tr>
										<th>Total</th>
										<td>'.number_format($total_cash_breakdown, 2).'</td>
									</tr>
									';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                   
                   <div class="col-md-7">
                        <div class="row">
                            <div class="col-md-4 center-align">
                                <h4>INVOICED</h4>
                                <h3>Ksh <?php echo number_format($total_invoices_revenue, 2);?></h3>
                                <!-- <p><?php echo $title;?></p> -->
                            </div>
                            <div class="col-md-4 center-align">
                                 <h4>TODAYS PAYMENTS</h4>
                                <h3>Ksh <?php echo number_format($total_payments_revenue, 2);?></h3>
                                <!-- <p><?php echo $title;?></p> -->
                            </div>
                            <div class="col-md-4 center-align">
                                 <h4>BALANCE</h4>
                                <h3>Ksh <?php echo number_format($total_invoices_revenue  - $total_payments_revenue, 2);?></h3>
                                <!-- <p><?php echo $title;?></p> -->
                            </div>
                            
                        </div>
                       <!-- <hr>
                        <div class="row" style="margin-top: 20px;">
                              <div class="center-align">
                                
                              <h4>DEBT REPAYMENT</h4>
                            </div>
                            <br>
                            <div class="col-md-6 center-align">
                                <h4>CASH</h4>
                                <h3>Ksh <?php echo number_format($cash_debt_repayments, 2);?></h3>
                            </div>
                            
                            <div class="col-md-6 center-align">
                                <h4>INSURANCE </h4>
                                <h3>Ksh <?php echo number_format($insurance_debt_repayments, 2);?></h3>
                            </div>
                        </div> 
                        <hr>
                        <div class="row" style="margin-top: 20px;">
                            <div class="center-align">
                                
                              <h4>DEBT REPAYMENT</h4>
                                <h3>Ksh <?php echo number_format($receivable, 2);?></h3>
                            </div>
                        </div> -->
                        <hr>
                        <div class="row" style="margin-top: 20px;">
                            <div class="center-align">
                                
                              <h4>TOTAL PAYMENTS</h4>
                                <h3>Ksh <?php echo number_format($total_payments_revenue, 2);?></h3>
                            </div>
                        </div>
                     
                    </div>
                </div>
          	</div>
		</section>
    </div>
</div>