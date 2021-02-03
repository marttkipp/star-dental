<?php

$account_payments_rs = $this->petty_cash_model->get_payment_detail($account_payment_id);

// var_dump($account_payments_rs);die();
if($account_payments_rs->num_rows() > 0)
{
	foreach ($account_payments_rs->result() as $key => $value) {
		# code...
		$account_from_id = $value->account_from_id;
		$account_to_id = $value->account_to_id;
		$account_payment_description = $value->account_payment_description;
		$amount_paid = $value->amount_paid;
		$account_payment_description = $value->account_payment_description;
		$receipt_number = $value->receipt_number;
		$payment_date = $value->payment_date;
	}
}

?>

<div class="col-md-12" style="margin-top:40px !important;">
	<section class="panel">
		<div class="panel-body">
			 <div class="padd">
		         <?php echo form_open_multipart($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form","id" => "edit-direct-payment"));?>
		            <div class="row">
		                <div class="col-md-6">
		                    <div class="form-group">
		                        <label class="col-lg-4 control-label">Parent Account</label>
		                        <div class="col-lg-8">
		                            <select id="account_from_id" name="account_from_id" class="form-control">
		                                <option value="0">--- Account ---</option>
		                                <?php
		                                if($accounts->num_rows() > 0)
		                                {   
		                                    foreach($accounts->result() as $row):
		                                        // $company_name = $row->company_name;
		                                        $account_name = $row->account_name;
		                                        $account_id = $row->account_id;
		                                        
		                                        if($account_id == $account_from_id)
		                                        {
		                                        	echo "<option value=".$account_id." selected> ".$account_name."</option>";
		                                        }
		                                        else
		                                        {
		                                        	echo "<option value=".$account_id."> ".$account_name."</option>";
		                                        }
		                                        
		                                        
		                                    endforeach; 
		                                } 
		                                ?>
		                            </select>
		                        </div>
		                    </div> 
		                    <div class="form-group">
		                        <label class="col-lg-4 control-label">Amount *</label>
		                        <div class="col-lg-8">
		                            <input type="text" class="form-control" name="amount" placeholder="Amount" value="<?php echo $amount_paid;?>" required>
		                        </div>
		                    </div>
		                    <div class="form-group">
		                        <label class="col-lg-4 control-label">Cheque *</label>
		                        <div class="col-lg-8">
		                            <input type="text" class="form-control" name="cheque_number" placeholder="cheque_number" value="<?php echo $receipt_number;?>" >
		                        </div>
		                    </div>

		                    <div class="form-group">
		                        <label class="col-lg-4 control-label">Description *</label>
		                        <div class="col-lg-8">
		                            <textarea class="form-control" name="description" placeholder="Payment For"><?php echo $account_payment_description;?></textarea>
		                        </div>
		                    </div>
		                   
		                </div>
		                <input type="hidden" name="redirect_url" id="redirect_url" value="<?php echo $this->uri->uri_string();?>">
		                <input type="hidden" name="account_payment_id" id="account_payment_id" value="<?php echo $account_payment_id;?>">
		                <div class="col-md-6">        
		                    <div class="form-group">
		                        <label class="col-lg-4 control-label">Payment date: </label>
		                        
		                        <div class="col-lg-8">
		                            <div class="input-group">
		                                <span class="input-group-addon">
		                                    <i class="fa fa-calendar"></i>
		                                </span>
		                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payment_date" placeholder="Payment Date" value="<?php echo $payment_date;?>">
		                            </div>
		                        </div>
		                    </div>       
		                    <!-- Activate checkbox -->
		                    <div class="form-group">
		                        <label class="col-lg-4 control-label">Accout to ?</label>
		                        <div class="col-lg-8">
		                            <div class="radio">
		                                <!-- <label>
		                                    <input  type="radio" checked value="0" name="account_to_type" id="account_to_type" onclick="get_accounty_type_list('account_to_type')">
		                                    None
		                                </label> -->
		                                <label>
		                                    <input  type="radio"  value="4" name="account_to_type" id="account_to_type" onclick="get_accounty_type_list('account_to_type')" checked="checked">
		                                    Direct Purchase
		                                </label>
		                               <!--  <label>
		                                    <input  type="radio" value="2" name="account_to_type" id="account_to_type" onclick="get_accounty_type_list('account_to_type')">
		                                    Creditor
		                                </label>
		                                <label>
		                                    <input  type="radio" value="3" name="account_to_type" id="account_to_type" onclick="get_accounty_type_list('account_to_type')">
		                                    Doctor
		                                </label>
		                                <label>
		                                    <input  type="radio" value="1" name="account_to_type" id="account_to_type" onclick="get_accounty_type_list('account_to_type')">
		                                    Transfer
		                                </label> -->
		                            </div>
		                        </div>
		                    </div>
		                    <div class="form-group">
		                        <label class="col-lg-4 control-label">Charging to: </label>
		                        
		                        <div class="col-lg-8">
		                            <select name="account_to_id" class="form-control custom-select" id="charge_to_id">

		                            	 <?php
		                                if($expense_accounts->num_rows() > 0)
		                                {   
		                                    foreach($expense_accounts->result() as $row):
		                                        // $company_name = $row->company_name;
		                                        $account_name = $row->account_name;
		                                        $account_id = $row->account_id;
		                                        
		                                        if($account_id == $account_to_id)
		                                        {
		                                        	echo "<option value=".$account_id." selected> ".$account_name."</option>";
		                                        }
		                                        else
		                                        {
		                                        	echo "<option value=".$account_id."> ".$account_name."</option>";
		                                        }
		                                        
		                                        
		                                    endforeach; 
		                                } 
		                                ?>
		                                
		                            </select>
		                        </div>
		                    </div>
		                        
		                    <div class="form-actions center-align">
		                        <button class="submit btn btn-primary btn-sm" type="submit" onclick="return confirm('Are you sure you want to edit this payment ? ')">
		                            Edit payment detail
		                        </button>
		                    </div>
		                </div>

		            </div>
		            <?php echo form_close();?>
		        </div>
		</div>
	</section>
</div>
<br/>
<div class="row" style="margin-top: 5px;">
		<ul>
			<li style="margin-bottom: 5px;">
				<div class="row">
			        <div class="col-md-12 center-align">
				        <a  class="btn btn-sm btn-info" onclick="close_side_bar()"><i class="fa fa-folder-closed"></i> CLOSE SIDEBAR</a>
				        		
			               
			        </div>
			    </div>
				
			</li>
		</ul>
	</div>
