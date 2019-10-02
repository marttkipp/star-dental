<div class="row" style="margin-top:20px">
	<div class="col-md-4">
		<section class="panel panel-featured-left panel-featured-primary">
            <div class="panel-body">
               <div class="widget-summary">
                    
                    <div class="widget-summary-col">
                        <div class="widget-summary-col widget-summary-col-icon">
                            <div class="summary-icon bg-primary">
                                <i class="fa fa-life-ring"></i>
                            </div>
                        </div>
                        <div class="widget-summary-col">
                            <div class="summary">
                                <h4 class="title">Total Contacts</h4>
                                <div class="info">
                                    <strong class="amount"><?php echo $total_contacts;?></strong>
                                </div>
                            </div>
                            
                        </div>

                    </div>
                </div>

            </div>
        </section>
	</div>
	<div class="col-md-4">
		<section class="panel panel-featured-left panel-featured-primary">
            <div class="panel-body">
                <div class="widget-summary">
                    
                    <div class="widget-summary-col">
                      <table class="table table-striped table-hover table-condensed">
					        <tbody>
					            <tr>
					                <th>Sent Messages</th>
					                <td><?php echo $sent_messages;?></td>
					            </tr>
					            <tr>
					                <th>Unsent messages</th>
					                <td><?php echo $unsent_messages;?></td>
					            </tr>
					           
					        </tbody>
					    </table>

                    </div>
                </div>

            </div>
        </section>
	</div>
	
	<div class="col-md-4">
		<section class="panel panel-featured-left panel-featured-primary">
            <div class="panel-body">
                <div class="widget-summary">
                    
                    <div class="widget-summary-col">
                        <div class="widget-summary-col widget-summary-col-icon">
                            <div class="summary-icon bg-primary">
                                <i class="fa fa-life-ring"></i>
                            </div>
                        </div>
                        <div class="widget-summary-col">
                            <div class="summary">
                                <h4 class="title">Account Balance</h4>
                                <div class="info">
                                    <strong class="amount"><?php echo number_format($balance,2);?></strong>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>
	</div>
	
</div>