 <!-- search -->
<?php //echo $this->load->view('search/search_petty_cash', '', TRUE);

    $properties = $this->property_owners_model->get_all_front_end_property_owners();
    $rs8 = $properties->result();
    $property_list = '';
    foreach ($rs8 as $property_rs) :
        $property_owner_id = $property_rs->property_owner_id;
        $property_owner_name = $property_rs->property_owner_name;

        $property_list .="<option value='".$property_owner_id."'>".$property_owner_name."</option>";

    endforeach;

$property_owners = $this->property_model->get_active_property_owners_with_beneficiaries();
$rs8 = $property_owners->result();

// var_dump($property_owners);die();
$property_beneficiaries_list = '';
$owner_id = 0;
foreach ($property_owners->result() as $property_rs =>$value) :


    $property_owner_id = $value->property_owner_id;
    $property_beneficiary_name = $value->property_beneficiary_name;
    $property_beneficiary_id = $value->property_beneficiary_id;
    $property_owner_name = $value->property_owner_name;

    if($property_owner_id != $owner_id)
    {
      $property_beneficiaries_list .= '<optgroup label="'.strtoupper($property_owner_name).'">';
    }

    // else
    // {

      $property_beneficiaries_list .= '<option value="'.$property_beneficiary_id.'#'.$property_owner_id.'">'.strtoupper($property_beneficiary_name).'</option>';
    // }

    


    if($property_owner_id != $owner_id AND $owner_id != 0)
    {
       $property_beneficiaries_list .= '</optgroup>';
    }
    $owner_id = $property_owner_id;


endforeach;


 $query_banks = $this->purchases_model->get_child_accounts("Bank");
$expense_list_accounts = '';
 if($query_banks->num_rows() > 0)
 {
    foreach ($query_banks->result() as $key => $value) {
        # code...
        $account_id = $value->account_id;
        $account_name = $value->account_name;

        $expense_list_accounts .="<option value='".$account_id."'>".$account_name."</option>";
    }
 }

$month = $this->accounts_model->get_months();
$months_list = '<option value="0">Select a Type</option>';
foreach($month->result() as $res)
{
  $month_id = $res->month_id;
  $month_name = $res->month_name;
  if($month_id < 10)
  {
    $month_id = '0'.$month_id;
  }
  $month = date('M');

  if($month == $month_name)
  {
    $months_list .= '<option value="'.$month_id.'" selected>'.$month_name.'</option>';
  }
  else {
    $months_list .= '<option value="'.$month_id.'">'.$month_name.'</option>';
  }



}


$start = 2015;
$end_year = 2030;
$year_list = '<option value="0">Select a Type</option>';
for ($i=$start; $i < $end_year; $i++) {
  // code...
  $year= date('Y');

  if($year == $i)
  {
    $year_list .= '<option value="'.$i.'" selected>'.$i.'</option>';
  }
  else {
    $year_list .= '<option value="'.$i.'">'.$i.'</option>';
  }
}
?>
<!--end reports -->
<div class="row">
    <div class="col-md-12">
       <section class="panel panel-danger">
          <header class="panel-heading">
            <h3 class="panel-title"><?php echo $title;?></h3>
            <div class="panel-tools pull-right" style="margin-top: -25px;">
               
              </div>
          </header>
          <div class="panel-body">

              <?php echo form_open("finance/transfer/search_direct_payments", array("class" => "form-horizontal"));?>
                 <div class="row">
                   <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="form-group"  >
                           <label class="col-lg-4 control-label">Account</label>

                           <div class="col-lg-8">
                              <select  name='account' class='form-control ' >
                                 <option value=''>None - Please Select an account</option>
                                 <?php echo $expense_list_accounts;?>
                               </select>
                           </div>
                       </div>
                        <div class="form-group"  >
                           <label class="col-lg-4 control-label">Property Owner</label>

                           <div class="col-lg-8">
                              <select  name='property_owner_id' class='form-control ' >
                                 <option value=''>None - Please Select a owner</option>
                                 <?php echo $property_list;?>
                               </select>
                           </div>
                       </div>
                        
                    </div>

                   <div class="col-md-4">
                            <div class="form-group">
                                 <label class="col-md-4 control-label">Date From: </label>

                                 <div class="col-md-8">
                                     <div class="input-group">
                                         <span class="input-group-addon">
                                             <i class="fa fa-calendar"></i>
                                         </span>
                                         <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_from" placeholder="Transaction date" value="" id="datepicker" autocomplete="off" >
                                     </div>
                                 </div>
                            </div>

                           <div class="form-group">
                               <label class="col-md-4 control-label">Date To: </label>

                               <div class="col-md-8">
                                   <div class="input-group">
                                       <span class="input-group-addon">
                                           <i class="fa fa-calendar"></i>
                                       </span>
                                       <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_to" placeholder="Transaction date" value="" id="datepicker1" autocomplete="off" >
                                   </div>
                               </div>
                           </div>
                    </div>
                    <div class="col-md-4">
                         <div class="form-group">
                             <label class="col-md-4 control-label">Ref No *</label>

                             <div class="col-md-8">
                                 <input type="text" class="form-control" name="transaction_number" placeholder="Transaction Number" autocomplete="off" />
                             </div>
                         </div>
                          <div class="form-group">
                             <div class="text-center">
                                 <button type="submit" class="btn btn-sm btn-primary">Search record</button>
                             </div>
                           </div>
                   </div>
                  
                   </div>


                 </div>
                 <?php echo form_close();?>
          </div>
        </section>
    </div>
 
</div>
 <section class="panel panel-danger">
    <header class="panel-heading">
          <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?></h4>
          <div class="widget-icons pull-right">
               
          </div>
          <div class="clearfix"></div>
    </header>
    <div class="panel-body">
        <div class="padd">
            
            <?php echo form_open_multipart($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Paying From</label>
                        <div class="col-lg-8">
                            <select id="account_from_id" name="account_from_id" class="form-control" required="required">
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
                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payment_date" placeholder="Payment Date" value="<?php echo date('Y-m-d');?>" required>
                            </div>
          					</div>
          				</div>       
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Amount *</label>
                        <div class="col-lg-8">
                          <input type="text" class="form-control" name="amount" placeholder="Amount" value="<?php echo set_value('amount');?>" required>
                        </div>
                    </div>
                    <!-- Activate checkbox -->
                  	<div class="form-group" style="display: none;">
                        <label class="col-lg-4 control-label">Accout to ?</label>
                        <div class="col-lg-8">
                            <div class="radio">
                            	<label>
                                    <input  type="radio" checked value="0" name="account_to_type" id="account_to_type" onclick="get_accounty_type_list(0)">
                                    None
                                </label>
                               <!--  <label>
                                    <input  type="radio"  value="4" name="account_to_type" id="account_to_type" onclick="get_accounty_type_list(4)">
                                    Direct Purchase
                                </label> -->
                                <!-- <label>
                                    <input  type="radio" value="2" name="account_to_type" id="account_to_type" onclick="get_accounty_type_list('account_to_type')">
                                    Creditor
                                </label> -->
                                <label>
                                    <input  type="radio" value="3" name="account_to_type" id="account_to_type" onclick="get_accounty_type_list(3)">
                                    Landlord
                                </label>
                                <!-- <label>
                                    <input  type="radio" value="1" name="account_to_type" id="account_to_type" onclick="get_accounty_type_list('account_to_type')">
                                    Transfer
                                </label> -->
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="display: none;">
            					<label class="col-lg-4 control-label">Charging to: </label>
            					
            					<div class="col-lg-8">
            						<select name="charge_to_id" class="form-control custom-select" id="charge_to_id">
            							
            						</select>
            					</div>
            				</div>
                    <input type="hidden" name="account_to_type" value="3">
                    <div style="display: none;" id="payment_to_div">
                        <!-- <div class="form-group" >
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
                        </div> -->
                        
                    </div>

                    <div class="form-group">
                       <label class="col-lg-4 control-label">Beneficairy</label>

                       <div class="col-lg-8">

                        <select class="form-control selectpicker" name="property_beneficiary_id" required="required">
                          <option value="0">---- SELECT A BENEFICIARY ---- </option>
                          <?php echo $property_beneficiaries_list;?>
                        </select>

                          
                       </div>
                   </div>


                    <div class="form-group" style="display: none;">
                        <label class="col-lg-4 control-label">Expense Account: </label>
                        
                        <div class="col-lg-8">
                            <select name="account_to_id" class="form-control" id="account_to_id" >
                                <option value="">----- SELECT AN EXPENSE  --------</option>
                                <?php
                                 if($expense_accounts->num_rows() > 0)
                                 {
                                   foreach ($expense_accounts->result() as $key => $value) {
                                     // code...
                                     $account_id = $value->account_id;
                                     $account_name = $value->account_name;
                                     echo '<option value="'.$account_id.'"> '.$account_name.'</option>';
                                   }
                                 }
                                 ?>
                            </select>
                        </div>
                    </div>
                    <br>
                    
                    
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-actions center-align">
                        <button class="submit btn btn-primary btn-sm" type="submit" onclick="return confirm('Are you sure you want to add this transaction ? ')">
                            Add payment detail
                        </button>
                    </div>
                </div>
            </div>
            <?php echo form_close();?>
            
        </div>
    </div>
    <section class="panel panel-danger">
    <header class="panel-heading">
          <h4 class="pull-left"><i class="icon-reorder"></i>Transactions</h4>
          <div class="widget-icons pull-right">
                <a href="<?php echo site_url().'export-direct-payments'?>" target="_blank"  class="submit btn btn-success btn-sm" type="submit">Export Transactions</a>
                <a href="<?php echo site_url().'print-direct-payments'?>" target="_blank" class="submit btn btn-warning btn-sm" type="submit">Print Transactions</a>
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
                
            $search = $this->session->userdata('search_direct_payments');
             if(!empty($search))
            {
               echo ' <a href="'.base_url().'finance/transfer/close_direct_payments_search" class="btn btn-sm btn-danger"><i class="fa fa-cancel"></i> Close Search</a>'; 
            }
            ?>
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
                                        $payment_to = $value->payment_to;
                                        $property_beneficiary_id = $value->property_beneficiary_id;

                                        $account_from_name = $this->transfer_model->get_account_name($account_from_id);
                                        if($account_to_type == 1 AND $account_to_id > 0)
                                        {
                                            $payment_type = 'Transfer';
                                            $account_to_name = $this->transfer_model->get_account_name($account_to_id);
                                        }
                                        else if($account_to_type == 3 AND $payment_to > 0)
                                        {
                                            // doctor payments
                                            $payment_type = "Landlord Payment";
                                            $account_to_name = $this->transfer_model->get_owner_name($payment_to);
                                            if($property_beneficiary_id > 0)
                                            {
                                               $account_to_name .= ' ('.$this->transfer_model->get_beneficiary_name($property_beneficiary_id).')';
                                            }

                                        }
                                        else if($account_to_type == 2 AND $account_to_id > 0)
                                        {
                                            // creditor
                                            $payment_type = "Creditor Payment";
                                            $account_to_name = $this->transfer_model->get_creditor_name($account_to_id);
                                        }
                                        else if($account_to_type == 4 AND $account_to_id > 0)
                                        {
                                            // expense account
                                            $payment_type = "Direct Expense Payment";
                                            $account_to_name = $this->transfer_model->get_account_name($account_to_id);
                                        }
                                        else if($account_to_type == 3 AND $property_beneficiary_id > 0)
                                        {
                                            // doctor payments
                                            $payment_type = "Landlord Payment";
                                            $account_to_name = $this->transfer_model->get_beneficiary_name($property_beneficiary_id);
                                        }
                                        else
                                        {
                                          $account_to_name ='';
                                        }


                                        if($created == date('Y-m-d'))
                                        {
                                            $add_invoice = '<td><a href="'.site_url().'delete-direct-payments/'.$account_payment_id.'" class="btn btn-xs btn-danger fa fa-trash" onclick="return confirm(\'Do you really want delete this entry?\');"></a></td>';
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

    	
    	
    	function get_accounty_type_list(type)
    	{
        // alert(type);
    		// var type = getRadioCheckedValue(radio_name);
    		// $("#charge_to_id").customselect()="";
            if(type == 3)
            {
                $('#payment_to_div').css('display', 'block');
            }
            else
            {
                $('#payment_to_div').css('display', 'none');
            }

			var url = "<?php echo site_url();?>finance/transfer/get_list_type/"+type;	
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