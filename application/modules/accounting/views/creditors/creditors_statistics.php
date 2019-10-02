<div class="row statistics">
    <div class="col-md-2 col-sm-12">
    	 <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
                <h2 class="panel-title">Invoices breakdown</h2>
              </header>             
        
              <!-- Widget content -->
              <div class="panel-body">
                <table class="table table-striped table-hover table-condensed">
                	<thead>
                    	<tr>
                        	<th>Type</th>
                            <th>No</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Total Invoices</th>
                            <td><?php echo $outpatients =0;?></td>
                        </tr>
                        
                    </tbody>
                </table>
                
                <div class="clearfix"></div>
          	</div>
		</section>
    </div>
    
    <div class="col-md-10 col-sm-12">
    	 <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	<h2 class="panel-title">Transaction breakdown</h2>
            </header>             
        
              <!-- Widget content -->
              <div class="panel-body">
                <div class="row">
                    <!-- End Transaction Breakdown -->
                    <div class="col-md-3">
                        
                    </div>
                    <div class="col-md-3">
                    	<h4>Total Invoices</h4>	
                        <h3>Ksh <?php echo number_format(0, 2);?></h3>
                        
                    </div>
                    <div class="col-md-3">
                      	<h4>Total Payments</h4>	
                        <h3>Ksh <?php echo number_format(0, 2);?></h3>
                    </div>
                    <div class="col-md-3">
                        <h4>Total Debt</h4>	
                        <h3>Ksh <?php echo number_format(0, 2);?></h3>
                    </div>
                </div>
          	</div>
		</section>
    </div>
</div>