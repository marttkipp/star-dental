 <section class="panel">
    <header class="panel-heading">
          <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?></h4>
          <div class="widget-icons pull-right">
               
          </div>
          <div class="clearfix"></div>
    </header>
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
            
            <?php echo form_open_multipart($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
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
                                        
                                        echo "<option value=".$account_id."> ".$account_name."</option>";
                                        
                                    endforeach; 
                                } 
                                ?>
                            </select>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Amount *</label>
                        <div class="col-lg-8">
                        	<input type="text" class="form-control" name="amount" placeholder="Amount" value="<?php echo set_value('amount');?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Cheque *</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" name="cheque_number" placeholder="cheque_number" value="<?php echo set_value('cheque_number');?>" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-4 control-label">Description *</label>
                        <div class="col-lg-8">
                        	<textarea class="form-control" name="description" placeholder="Payment For"><?php echo set_value('description');?></textarea>
                        </div>
                    </div>
                   
                </div>
                <div class="col-md-6">        
                	<div class="form-group">
    					<label class="col-lg-4 control-label">Payment date: </label>
    					
    					<div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payment_date" placeholder="Payment Date" value="<?php echo date('Y-m-d');?>">
                            </div>
    					</div>
    				</div>       
                    <!-- Activate checkbox -->
                  	<div class="form-group">
                        <label class="col-lg-4 control-label">Accout to ?</label>
                        <div class="col-lg-8">
                            <div class="radio">
                            	<label>
                                    <input  type="radio" checked value="0" name="account_to_type" id="account_to_type" onclick="get_accounty_type_list('account_to_type')">
                                    None
                                </label>
                                <label>
                                    <input  type="radio"  value="4" name="account_to_type" id="account_to_type" onclick="get_accounty_type_list('account_to_type')">
                                    Direct Purchase
                                </label>
                                <label>
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
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
    					<label class="col-lg-4 control-label">Charging to: </label>
    					
    					<div class="col-lg-8">
    						<select name="account_to_id" class="form-control custom-select" id="charge_to_id">
    							
    						</select>
    					</div>
    				</div>
                        <div class="form-group" style="display: none;" id="payment_to_div">
                        <label class="col-lg-4 control-label">Accout for ?</label>
                        <div class="col-lg-8">
                            <div class="radio">
                                <label>
                                    <input  type="radio" checked value="1" name="payment_to" id="payment_to" >
                                    Week Payment
                                </label>
                                <label>
                                    <input  type="radio"  value="0" name="payment_to" id="payment_to" >
                                    Month Payment
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions center-align">
                        <button class="submit btn btn-primary btn-sm" type="submit">
                            Add payment detail
                        </button>
                    </div>
                </div>

            </div>
            <?php echo form_close();?>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-hover table-bordered ">
                        <thead>
                            <tr>
                              <th>#</th>   
                              <th>Date</th>      
                              <th>Document No.</th>              
                              <th>Account From</th>
                              <th>Payment Type</th>
                              <th>Payment To</th>
                              <th>Amount</th>                      
                            </tr>
                         </thead>
                        <tbody>
                            <?php
                                $result = '';
                                // var_dump($query); die();
                               if($query->num_rows() > 0)
                               {
                                 $x=$page;
                                    foreach ($query->result() as $key => $value) {
                                        # code...
                                        $account_from_id = $value->account_from_id;
                                        $account_to_type = $value->account_to_type;
                                        $account_to_id = $value->account_to_id;
                                        $receipt_number = $value->receipt_number;
                                        $account_payment_id = $value->account_payment_id;
                                         $payment_date = $value->payment_date;
                                         $created = $value->created;
                                        $amount_paid = $value->amount_paid;

                                        $account_from_name = $this->petty_cash_model->get_account_name($account_from_id);
                                        if($account_to_type == 1)
                                        {
                                            $payment_type = 'Transfer';
                                            $account_to_name = $this->petty_cash_model->get_account_name($account_to_id);
                                        }
                                        else if($account_to_type == 3)
                                        {
                                            // doctor payments
                                            $payment_type = "Doctor Payment";
                                            $account_to_name = $this->petty_cash_model->get_doctor_name($account_to_id);
                                        }
                                        else if($account_to_type == 2)
                                        {
                                            // creditor
                                            $payment_type = "Creditor Payment";
                                            $account_to_name = $this->petty_cash_model->get_creditor_name($account_to_id);
                                        }
                                        else if($account_to_type == 4)
                                        {
                                            // expense account
                                            $payment_type = "Direct Expense Payment";
                                            $account_to_name = $this->petty_cash_model->get_account_name($account_to_id);
                                        }


                                        if($created == date('Y-m-d'))
                                        {
                                            $add_invoice = '<td><a href="'.site_url().'delete-payment-ledger-entry/'.$account_payment_id.'" class="btn btn-sm btn-danger fa fa-trash" onclick="return confirm(\'Do you really want delete this entry?\');"></a></td>';
                                        }
                                        else
                                        {
                                            $add_invoice = '';
                                        }

                                        $x++;

                                        $result .= '<tr>
                                                        <td>'.$x.'</td>
                                                        <td>'.$payment_date.'</td>
                                                        <td>'.strtoupper($receipt_number).'</td>
                                                        <td>'.$account_from_name.'</td>
                                                        <td>'.$payment_type.'</td>
                                                        <td>'.$account_to_name.'</td>
                                                        <td>'.number_format($amount_paid,2).'</td>
                                                        '.$add_invoice.'
                                                    </tr>';

                                    }
                               }
                               echo $result;
                            ?>
                        </tbody>
                    </table>
                </div>
                
            </div>
            <div class="widget-foot">
                                
                <?php if(isset($links)){echo $links;}?>
            
                <div class="clearfix"></div> 
            
            </div>
        </div>
    </div>
</section>

    <script type="text/javascript">

    	
    	
    	function get_accounty_type_list(radio_name)
    	{
    		var type = getRadioCheckedValue(radio_name);
    		// $("#charge_to_id").customselect()="";
            if(type == 3)
            {
                $('#payment_to_div').css('display', 'block');
            }
            else
            {
                $('#payment_to_div').css('display', 'none');
            }

			var url = "<?php echo site_url();?>accounting/petty_cash/get_list_type/"+type;	
            // alert(url);
			//get department services
			$.get( url, function( data ) 
			{
				$( "#charge_to_id" ).html( data );
				// $(".custom-select").customselect();
			});

    	}

    	function getRadioCheckedValue(radio_name)
		{
		   var oRadio = document.forms[0].elements[radio_name];
		 
		   for(var i = 0; i < oRadio.length; i++)
		   {
		      if(oRadio[i].checked)
		      {
		         return oRadio[i].value;
		      }
		   }
		 
		   return '';
		}

    </script>