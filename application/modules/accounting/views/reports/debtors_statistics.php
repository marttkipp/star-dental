<div class="row statistics">
    
    <div class="col-md-12 col-sm-12">
    	 <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	<h2 class="panel-title">Transaction breakdown for <?php echo $total_visits;?> patients</h2>
            </header>             
        
              <!-- Widget content -->
              <div class="panel-body">
                <div class="row">
                   
                    <div class="col-md-4">
						<?php
                        	$total_invoices_revenue = $this->accounting_model->get_visit_invoice_totals();
                            $total_transfers = $this->accounting_model->get_visit_invoice_children_totals();
                            $total_payments_revenue = $this->accounting_model->get_visit_payment_totals();
                            $total_waiver_revenue = $this->accounting_model->get_visit_waiver_totals();
                            $all_payments_period = $this->accounting_model->all_payments_period();
                            $receivable = $all_payments_period - $total_payments_revenue;
                            $total_rejected_amounts = $this->accounting_model->get_rejected_amounts();
                            // $total_invoices_revenue -= $total_rejected_amounts;

                            if($receivable < 0)
                            {
                                $receivable = ($receivable);
                            }

                        ?>
                        <?php
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
                                <tr>
                                    <th>TOTAL COLLECTION</th>
                                    <td><?php echo number_format($total_payments_revenue, 2);?></td>
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
                        <div class="clearfix"></div>
            		</div>
                    <!-- End Transaction Breakdown -->
                   
                   <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-4 center-align">
                                <h4>INVOICED</h4>
                                <h3>Ksh <?php echo number_format($total_invoices_revenue-$total_waiver_revenue, 2);?></h3>
                                <!-- <p><?php echo $title;?></p> -->
                            </div>
                            <div class="col-md-4 center-align">
                                 <h4>TODAYS PAYMENTS</h4>
                                <h3>Ksh <?php echo number_format($total_payments_revenue, 2);?></h3>
                                <!-- <p><?php echo $title;?></p> -->
                            </div>
                            <div class="col-md-4 center-align">
                                 <h4>BALANCE</h4>
                                <h3>Ksh <?php echo number_format($total_invoices_revenue - $total_payments_revenue - $total_waiver_revenue, 2);?></h3>
                                <!-- <p><?php echo $title;?></p> -->
                            </div>
                            
                        </div>
                       
                    </div>
                </div>
          	</div>
		</section>
    </div>
</div>