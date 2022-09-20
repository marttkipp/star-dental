<div class="col-md-12">
	<div class="row">
		<section class="panel">
			<div class="panel-body">
				
			<form  method="post" id="add-unallocated-payment">

					<div class="row">
						<div class="col-md-12">
							<div class="col-md-6">		
								<div class="form-group">
									<label class="col-md-4 control-label">Invoice Referenced</label>
									<div class="col-md-8">
										<input id="invoice_referenced" class="form-control" name="invoice_referenced" placeholder="Ref No." required="required">
									</div>
								</div>

								
											
								

								
							</div>
							<div class="col-md-6">
								
								<div class="form-group">
									<label class="col-md-4 control-label">Amount Paid</label>
									<div class="col-md-8">
										<input id="amount_paid_unreconcilled" class="form-control" name="amount_paid_unreconcilled" placeholder="Amount Paid" required="required">
									</div>
								</div>
								
								
							</div>

						</div>
					</div>
					<div class="row" style="margin-top: 10px">
						<div class="col-md-12">
							<div class="form-group">
								<label class="col-md-4 control-label">Reason</label>
								<div class="col-md-6">
									<textarea id="payment_description" class="form-control cleditor" name="payment_description" placeholder="Description..." required="required"></textarea>
								</div>
							</div>
						</div>
					</div>
					<input type="text" name="batch_receipt_id" id="batch_receipt_idd" value="<?php echo $batch_receipt_id;?>">
								
					
					<div class="row" style="margin-top: 10px">
				        <div class="col-md-12 center-align">
				        	<button type="submit" class="btn btn-sm btn-success " onclick="add_payment_item()">ADD PAYMENT ITEM</button>	
				        
				        </div>
				    </div>
				</form>
			</div>
		</section>
	</div>
	<div class="row" style="margin-top: 5px;">
		<ul>
			<li style="margin-bottom: 5px;">
				<div class="row">
			        <div class="col-md-12 center-align">
				        	<!-- <div id="old-patient-button" style="display:none">
				        				        		
				        		
				        	</div> -->
				        	<!-- <div> -->
				        		<a  class="btn btn-sm btn-info" onclick="close_side_bar()"><i class="fa fa-folder-closed"></i> CLOSE SIDEBAR</a>
				        	<!-- </div> -->
				        		
			               
			        </div>
			    </div>
				
			</li>
		</ul>
	</div>
</div>

<script type="text/javascript">
	
	
</script>