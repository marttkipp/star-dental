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
                            $total_payments_revenue = $this->accounting_model->get_visit_payment_totals();
                            $total_waiver_revenue = $this->accounting_model->get_visit_waiver_totals();
                            $all_payments_period = $this->accounting_model->all_payments_period();
                            $receivable = $total_payments_revenue - $all_payments_period;

                            if($receivable < 0)
                            {
                                $receivable = ($receivable);
                            }

                        ?>
                        <h5>VISIT TYPE BREAKDOWN</h5>
                        <table class="table table-striped table-hover table-condensed">
                            <tbody>
                                <tr>
                                    <th>TOTAL COLLECTION</th>
                                    <td><?php echo number_format($total_payments_revenue, 2);?></td>
                                </tr>
                                <tr>
                                    <th>TOTAL INVOICES</th>
                                    <td><?php echo number_format($total_invoices_revenue, 2);?></td>
                                </tr>
                                 <tr>
                                    <th>TOTAL WAIVERS</th>
                                    <td><?php echo number_format($total_waiver_revenue, 2);?></td>
                                </tr>

                                <tr>
                                    <th>DEBT BALANCE</th>
                                    <td><?php echo number_format(($total_invoices_revenue - $total_payments_revenue - $total_waiver_revenue), 2);?></td>
                                </tr>
                            </tbody>
                        </table>
                        <?php
                            $total_cash_invoice = $this->accounting_model->get_all_visit_invoices(1);
                            $total_cash_payments = $this->accounting_model->get_all_visit_payments_totals(1);
                            $total_insurance_payments = $this->accounting_model->get_all_visit_payments_totals(0);
                            $total_insurance_invoice = $this->accounting_model->get_all_visit_invoices(0);
                            $total_rejected_amounts = $this->accounting_model->get_rejected_amounts();

                            $total_cash_waiver = $this->accounting_model->get_all_visit_waiver(1);
                            $total_insurance_waiver = $this->accounting_model->get_all_visit_waiver(0);
                            $total_cash_invoice = $total_rejected_amounts;
                            // $total_cash_invoice -= $total_cash_waiver;
                            $total_insurance_invoice -= $total_cash_invoice + $total_insurance_waiver;

                            $total_cash_balance = $total_cash_invoice - $total_cash_payments;
                            $total_insurance_balance = $total_insurance_invoice - $total_insurance_payments;
                            // var_dump($total_cash_payments); die();

                        ?>

                        <h5>CUSTOMER TYPE BREAKDOWN</h5>
                        <table class="table table-striped table-hover table-condensed">
                            <tbody>
                                <tr>
                                    <th>CASH INVOICES</th>
                                    <td><?php echo number_format($total_cash_invoice, 2);?></td>
                                </tr>
                                <tr>
                                    <th>CASH PAYMENTS</th>
                                    <td><?php echo number_format($total_cash_payments, 2);?></td>
                                </tr>
                                 <tr>
                                    <th>CASH BALANCE</th>
                                    <td><?php echo number_format($total_cash_balance, 2);?></td>
                                </tr>

                                <tr>
                                    <th>INSURANCE INVOICES</th>
                                    <td><?php echo number_format($total_insurance_invoice, 2);?></td>
                                </tr>
                                <tr>
                                    <th>INSURANCE PAYMENTS</th>
                                    <td><?php echo number_format($total_insurance_payments, 2);?></td>
                                </tr>
                                <tr>
                                    <th>INSURANCE BALANCE</th>
                                    <td><?php echo number_format($total_insurance_balance, 2);?></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="clearfix"></div>
            		</div>
                    <!-- End Transaction Breakdown -->
                    <?php
                    $new_patients = $this->accounting_model->get_patients_visits(1);
                    $returning_patients = $this->accounting_model->get_patients_visits(0);
                    ?>
                   
                    <div class="col-md-3">
                        <h5>VISIT  BREAKDOWN</h5>
                        <table class="table table-striped table-hover table-condensed">
                            <tbody>
                                <tr>
                                    <th>NEW PATIENTS</th>
                                    <td><?php echo number_format($new_patients, 2);?></td>
                                </tr>
                                <tr>
                                    <th>RETURNING PATIENTS</th>
                                    <td><?php echo number_format($returning_patients, 2);?></td>
                                </tr>
                                 <tr>
                                    <th>TOTAL PATIENTS</th>
                                    <td><?php echo number_format($returning_patients + $new_patients, 2);?></td>
                                </tr>
                            </tbody>
                        </table>
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
                   
                   <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4 center-align">
                                <h4>INVOICED</h4>
                                <h3>Ksh <?php echo number_format($total_invoices_revenue-$total_waiver_revenue, 2);?></h3>
                                <!-- <p><?php echo $title;?></p> -->
                            </div>
                            <div class="col-md-4 center-align">
                                 <h4>PAYMENTS</h4>
                                <h3>Ksh <?php echo number_format($total_payments_revenue, 2);?></h3>
                                <!-- <p><?php echo $title;?></p> -->
                            </div>
                            <div class="col-md-4 center-align">
                                 <h4>BALANCE</h4>
                                <h3>Ksh <?php echo number_format($total_invoices_revenue - $total_payments_revenue - $total_waiver_revenue, 2);?></h3>
                                <!-- <p><?php echo $title;?></p> -->
                            </div>
                            
                        </div>
                        <hr>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-4 center-align">
                                <h4>CASH BAL</h4>
                                <h3>Ksh <?php echo number_format($total_cash_balance, 2);?></h3>
                                <!-- <p><?php echo $title;?></p> -->
                            </div>
                            <div class="col-md-4 center-align">
                                
                            </div>
                            <div class="col-md-4 center-align">
                                 <h4>INSURANCE BAL</h4>
                                <h3>Ksh <?php echo number_format($total_insurance_balance, 2);?></h3>
                                <!-- <p><?php echo $title;?></p> -->
                            </div>
                        </div>
                        <hr>
                        <div class="row" style="margin-top: 20px;">
                            <div class="center-align">
                                
                              <h4>OTHER RECEIVABLES</h4>
                                <h3>Ksh <?php echo number_format($receivable, 2);?></h3>
                                <!-- <p><?php echo $title;?></p> -->
                            </div>
                        </div>
                       
                    </div>
                </div>
          	</div>
		</section>
    </div>
</div>