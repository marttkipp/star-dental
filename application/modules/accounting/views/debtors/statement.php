<!-- search -->
<?php //echo $this->load->view('search_debtor_account', '', TRUE);?>
<!-- end search -->

<div class="row">
    <!-- <div class="col-md-12"> -->

        <section class="panel">
            <header class="panel-heading">
                
                <h2 class="panel-title"><?php echo $title;?></h2>
                <a href="<?php echo site_url();?>accounting/debtors-statements" class="btn btn-sm btn-warning pull-right" style="margin-top: -25px; "><i class="fa fa-arrow-left"></i> Back to debtors</a>
                <!-- <a href="<?php echo base_url().'accounting/creditors/print_creditor_account/'.$creditor_id?>" class="btn btn-sm btn-success pull-right"  style="margin-top: -25px;margin-right: 5px;" target="_blank"><i class="fa fa-print"></i> Print</a>
                <button type="button" class="btn btn-sm btn-primary pull-right"  data-toggle="modal" data-target="#record_creditor_account" style="margin-top: -25px; margin-right: 5px;"><i class="fa fa-plus"></i> Record</button> -->

                	
            </header>
            
            <div class="panel-body">
                <div class="pull-right">
                	
                	<!--<a href="<?php echo base_url().'administration/sync_app_creditor_account';?>" class="btn btn-sm btn-info"><i class="fa fa-sign-out"></i> Sync</a>-->
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
                                <?php echo form_open("accounting/creditors/record_creditor_account/".$creditor_id, array("class" => "form-horizontal"));?>
                                <input type="hidden" name="account_from_id" value="<?php echo $creditor_id;?>">
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
                                
                              <!--   <div class="form-group">
                                    <label class="col-md-4 control-label">Type *</label>
                                    
                                    <div class="col-md-8">
                                        <select class="form-control" name="transaction_type_id">
                                            <option value="">-- Select type --</option>
                                            <option value="1">Payment</option>
                                            <option value="2">Invoice</option>
                                        </select>
                                    </div>
                                </div> -->
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Account*</label>
                                    
                                    <div class="col-md-8">
                                         <select  class="form-control custom-select" name="billed_account_id" id='billed_account_id'>
                                            <option value="">-- Select account --</option>
                                            <?php
                                            if($accounts->num_rows() > 0)
											{
												foreach($accounts->result() as $res)
												{
													$account_id = $res->account_id;
													$account_name = $res->account_name;
													?>
                                                    <option value="<?php echo $account_id;?>"><?php echo $account_name;?></option>
                                                    <?php
												}
											}
											?>
                                        </select>
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
					
			$search = $this->session->userdata('creditor_payment_search');
			$search_title = $this->session->userdata('creditor_search_title');
		
			if(!empty($search))
			{
				echo '
				<a href="'.site_url().'accounting/creditors/close_creditor_search/'.$creditor_id.'" class="btn btn-warning btn-sm ">Close Search</a>
				';
				echo $search_title;
			}	
			// var_dump($creditor_id); die();
				$creditor_result = $this->debtors_model->get_debtor_statement($debtor_id);
			?>

				<table class="table table-hover table-bordered ">
				 	<thead>
						<tr>
						  <th>Transaction Date</th>						  
						  <th>Invoice Total</th>
						  <th>Bill Transfers</th>
						  <th>Waiver</th>
						  <th>Billed Amount</th>
						  <th>Paid Amount</th>	
                          <th>Balance</th>   					
						</tr>
					 </thead>
				  	<tbody>
				  		<?php  echo $creditor_result['result'];?>
					</tbody>
				</table>
          	</div>
		</section>
    <!-- </div> -->
</div>
<script type="text/javascript">
    $(function() {
       $("#billed_account_id").customselect();
    });
</script>