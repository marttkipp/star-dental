<!-- search -->
<?php echo $this->load->view('creditors/search_creditor_account', '', TRUE);?>
<!-- end search -->
<div class="row">
    <div class="col-md-12">

        <section class="panel">
            <header class="panel-heading">
                
                <h2 class="panel-title"><?php echo $title;?></h2>
                <a href="<?php echo site_url();?>accounts/providers" class="btn btn-sm btn-warning pull-right" style="margin-top: -25px; "><i class="fa fa-arrow-left"></i> Back to provider</a>
                <button type="button" class="btn btn-sm btn-primary pull-right"  data-toggle="modal" data-target="#record_creditor_account" style="margin-top: -25px; margin-right: 5px;"><i class="fa fa-plus"></i> Record</button>

                	
            </header>
            
            <div class="panel-body">
                <div class="pull-right">
                	
                </div>
                <!-- Modal -->
                <div class="modal fade" id="record_creditor_account" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Record Transaction</h4>
                            </div>
                            <div class="modal-body">
                                <?php echo form_open("accounts/creditors/record_provider_invoice/".$provider_id, array("class" => "form-horizontal"));?>
                                <input type="hidden" name="provider_id" value="<?php echo $provider_id;?>">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Transaction date: </label>
                                    
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="creditor_account_date" placeholder="Transaction date">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Transaction Code *</label>
                                    
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="transaction_code" placeholder="Code e.g cheque number/MPESA code"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Description *</label>
                                    
                                    <div class="col-md-8">
                                        <textarea class="form-control" name="creditor_account_description"></textarea>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Amount *</label>
                                    
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="creditor_account_amount" placeholder="Amount"/>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-8 col-md-offset-4">
                                        <div class="center-align">
                                            <button type="submit" class="btn btn-primary">Save record</button>
                                        </div>
                                    </div>
                                </div>
                                <?php echo form_close();?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                
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
			$response = $this->petty_cash_model->get_provider_statement($provider_id);	

		
			
			?>

			<table class="table table-hover table-bordered col-md-12">
				<thead>
					<tr>
						<th>Date</th>
						<th>Description</th>
						<th>Document No</th>
						<th>Invoices</th>
						<th>Payments</th>
						<th>Arrears</th>
						<th></th>
					</tr>
				</thead>
			  	<tbody>
			  		<?php echo $response['result'];?>
			  	</tbody>
			</table>
          	</div>
		</section>
    </div>
</div>