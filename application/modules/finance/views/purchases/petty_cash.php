
<div class="row">
    <div class="col-md-12">
      <section class="panel panel-info">
          <header class="panel-heading">
              <h3 class="panel-title">Search Petty Cash</h3>
          </header>
          <div class="panel-body">
               <div class="pull-right">
                 <!-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#record_petty_cash"><i class="fa fa-plus"></i> Record</button>
                 <a href="<?php echo base_url().'accounts/petty_cash/print_petty_cash/';?>" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-print"></i> Print</a>
                 <a href="<?php echo base_url().'administration/sync_app_petty_cash';?>" class="btn btn-sm btn-info"><i class="fa fa-sign-out"></i> Sync</a> -->
               </div>
               <div class="row">
                 <div class="col-md-12">
             <?php echo form_open("finance/purchases/search_petty_cash", array("class" => "form-horizontal"));?>



                  <div class="col-md-3">
                       <div class="form-group">
                           <label class="col-md-4 control-label">Date From: </label>

                           <div class="col-md-8">
                               <div class="input-group">
                                   <span class="input-group-addon">
                                       <i class="fa fa-calendar"></i>
                                   </span>
                                   <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_from" placeholder="Transaction date" value="" id="datepicker" autocomplete="off">
                               </div>
                           </div>
                       </div>




                 </div>
                 <div class="col-md-3">

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
                 <div class="col-md-3">
                   <div class="form-group">
                     <div class="text-center">
                         <button type="submit" class="btn btn-sm btn-info">Search record</button>
                     </div>
                   </div>
                 </div>

               <?php echo form_close();?>
               <div class="col-md-3">
                 <a href="<?php echo site_url().'print-petty-cash'?>" target="_blank" class="btn btn-sm btn-warning"><i class="fa fa-print"></i> Print Statement</a>
                 <!-- <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-print"></i> Export to excel Statement</button> -->

               </div>
             </div>


           </div>

            </div>
        </section>
    </div>
    <div class="col-md-12">
      <section class="panel panel-info">
          <header class="panel-heading">
              <h3 class="panel-title">Petty Cash Account </h3>
          </header>
          <div class="panel-body">
               <div class="pull-right">
                 <!-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#record_petty_cash"><i class="fa fa-plus"></i> Record</button>
                 <a href="<?php echo base_url().'accounts/petty_cash/print_petty_cash/';?>" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-print"></i> Print</a>
                 <a href="<?php echo base_url().'administration/sync_app_petty_cash';?>" class="btn btn-sm btn-info"><i class="fa fa-sign-out"></i> Sync</a> -->
               </div>

             <?php echo form_open("finance/purchases/record_petty_cash", array("class" => "form-horizontal"));?>
               <div class="row">
                 <div class="col-md-12">
                 <div class="col-md-6">
                   <input type="hidden" class="form-control" name="account_from_id" placeholder="Account" value="<?php echo $account_from_id?>" required/>
                       <div class="form-group">
                           <label class="col-md-4 control-label">Transaction date: </label>

                           <div class="col-md-8">
                               <div class="input-group">
                                   <span class="input-group-addon">
                                       <i class="fa fa-calendar"></i>
                                   </span>
                                   <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="transaction_date" placeholder="Transaction date" value="<?php echo date('Y-m-d');?>" id="datepicker2" required>
                               </div>
                           </div>
                       </div>
                       <div class="form-group">
                           <label class="col-md-4 control-label">Transaction No *</label>

                           <div class="col-md-8">
                               <input type="text" class="form-control" name="transaction_number" placeholder="Transaction Number" required/>
                           </div>
                       </div>
                       <div class="form-group">
                           <label class="col-md-4 control-label">Department *</label>

                           <div class="col-md-8">
                               <select class="form-control" name="department_id" id="department_id" >
                                   <option value="0">-- Select a department --</option>
                                   <?php
                                   if($departments->num_rows() > 0)
                                   {
                                       foreach($departments->result() as $res)
                                       {
                                           $department_id = $res->department_id;
                                           $department_name = $res->department_name;
                                           ?>
                                           <option value="<?php echo $department_id;?>"><?php echo $department_name;?></option>
                                           <?php
                                       }
                                   }
                                   ?>
                               </select>
                           </div>
                       </div>

                       <div class="form-group" >
                           <label class="col-md-4 control-label">Expense Account *</label>

                           <div class="col-md-8">
                               <select class="form-control select2" name="account_to_id" id="account_to_id" required>
                                 <option value="0">--- select an expense account - ---</option>
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


                 </div>
                 <div class="col-md-6">
                   <div class="form-group">
                       <label class="col-md-4 control-label">Type *</label>

                       <div class="col-md-8">
                           <div class="radio">
                               <label>
                                   <input  type="radio" checked value="0" name="transaction_type_id" id="account_to_type" onclick="get_transaction_type_list(this.value)">
                                   Pure Expense
                               </label>
                               <label>
                                   <input  type="radio"  value="1" name="transaction_type_id" id="account_to_type" onclick="get_transaction_type_list(this.value)">
                                   Expense Linked to a creditor
                               </label>
                           </div>
                       </div>
                   </div>
                   <div class="form-group" style="display:none;" id="creditor_div">
                       <label class="col-md-4 control-label">Creditor*</label>

                       <div class="col-md-8">
                           <select class="form-control" name="creditor_id" id="creditor_id">
                             <option value="0">--- select a creditor - ---</option>
                             <?php
                             if($creditors->num_rows() > 0)
                             {
                               foreach ($creditors->result() as $key => $value) {
                                 // code...
                                 $creditor_id = $value->creditor_id;
                                 $creditor_name = $value->creditor_name;
                                 echo '<option value="'.$creditor_id.'"> '.$creditor_name.'</option>';
                               }
                             }
                             ?>
                           </select>
                       </div>
                   </div>


                           <div class="form-group">
                               <label class="col-md-4 control-label">Description *</label>

                               <div class="col-md-8">
                                   <textarea class="form-control" name="description" required></textarea>
                               </div>
                           </div>

                           <div class="form-group">
                               <label class="col-md-4 control-label">Amount *</label>

                               <div class="col-md-8">
                                   <input type="text" class="form-control" name="transacted_amount" placeholder="Amount" required/>
                               </div>
                           </div>


                 </div>
                 </div>



               </div>
               <div class="row" style="margin-top:5px;">
                     <div class="col-md-12">
                         <div class="text-center">
                             <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Are you sure you want to add this record ? ')">Save record</button>
                         </div>
                     </div>
                 </div>
               <?php echo form_close();?>


            </div>

      </section>
    </div>
</div>
<div class="row">
    <div class="col-md-12">


			<?php
      $search = $this->session->userdata('search_petty_cash');
			if(!empty($search))
			{
				?>
                <a href="<?php echo base_url().'finance/purchases/close_petty_cash_search';?>" class="btn btn-sm btn-success"><i class="fa fa-print"></i> Close Search</a>
                <?php
			}
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

			$result =  '';

			// echo $result;
      $result = '';
      $count = 0;
      $balance = 0;
// get account opening balance
  $opening_balance_query = $this->purchases_model->get_account_opening_balance('Petty Cash');
  // var_dump($opening_balance_query);die();
  $cr_amount = 0;
  $dr_amount = 0;
  if($opening_balance_query->num_rows() > 0)
  {
    $row = $opening_balance_query->row();
    $cr_amount = $row->cr_amount;
    $dr_amount = $row->dr_amount;
    $balance += $dr_amount;
    $balance -=  $cr_amount;

    $opening_balance = $dr_amount  - $cr_amount;
    // var_dump(expression)
    if($opening_balance < 0 AND $dr_amount > $cr_amount)
    {
      $dr_amount -=$opening_balance;
      $cr_amount = 0;
    }  
    else if($opening_balance < 0 AND $dr_amount < $cr_amount)
    {
      $cr_amount -=$opening_balance;
      $dr_amount = 0;
    }
    else if($opening_balance > 0 AND $dr_amount > $cr_amount)
    {
      $dr_amount =$opening_balance;
      $cr_amount =0;
    }
    else if($opening_balance == 0 AND $dr_amount == $cr_amount)
    {
      $dr_amount =0;
      $cr_amount =0;
    }
   



    $result .='
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>Balance B/F</td>
                <td >'.number_format($dr_amount,2).' </td>
                <td >'.number_format($cr_amount,2).' </td>
                <td >'.number_format($balance,2).' </td>

              </tr>
              ';
  }
  else {
    $result .='
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>Balance B/F</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00 </td>

              </tr>
              ';
  }



  if($query_purchases->num_rows() > 0)
  {
    foreach ($query_purchases->result() as $key => $value) {
      // code...
      $transactionClassification = $value->transactionClassification;

      $document_number = '';
      $transaction_number = '';
      $finance_purchase_description = '';
      $finance_purchase_amount = 0 ;
       $referenceId = $value->payingFor;
      if($transactionClassification == 'Purchase Payment' AND $referenceId > 0 )
      {
       

        // get purchase details
        $detail = $this->purchases_model->get_purchases_details($referenceId);
        $row = $detail->row();
        $document_number = $row->document_number;
        $transaction_number = $row->transaction_number;
        $finance_purchase_description = $row->finance_purchase_description;

      }

       $referenceId = $value->payingFor;
      $document_number =$transaction_number = $value->referenceCode;
      $finance_purchase_description = $value->transactionName;
      // if($transactionClassification == 'Transfer')
      // {
      //   // echo $referenceId;

      //   // get purchase details
      //   $details = $this->purchases_model->get_transfer_details($referenceId);
      //   $row2 = $details->row();
      //   // var_dump($row2->reference_number);die();
      //   // $document_number = $row2->reference_number;
      //   // $transaction_number = $row2->reference_number;
      //   $finance_purchase_description = $row2->remarks;

      // }
      $cr_amount = $value->cr_amount;
      $dr_amount = $value->dr_amount;


      $transaction_date = $value->transactionDate;
      $transaction_date = date('jS M Y',strtotime($transaction_date));
      $creditor_name = $value->creditor_name;
      $creditor_id = 0;//$value->creditor_id;
      $account_name = '';//$value->account_name;
      $finance_purchase_id = '';//$value->finance_purchase_id;


      $balance += $dr_amount;
      $balance -=  $cr_amount;
      $count++;
      $result .='
                <tr>
                  <td>'.$count.'</td>
                  <td>'.$transaction_date.'</td>
                  <td>'.$transaction_number.'</td>
                  <td>'.$finance_purchase_description.' '.$creditor_name.'</td>
                  <td>'.number_format($dr_amount,2).' </td>
                  <td>'.number_format($cr_amount,2).'</td>
                  <td>'.number_format($balance,2).'</td>

                </tr>
                ';
    }


  }

  $result .='
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td>Balance</td>
              <td colspan="3" class="center-align"><strong>KES '.number_format($balance,2).' </strong></td>

            </tr>
            ';


?>
<section class="panel">
    <header class="panel-heading">
        <h3 class="panel-title">Petty Cash Transaction </h3>
    </header>
    <div class="panel-body">
      <?php
      $error = $this->session->userdata('error_message');
      $success = $this->session->userdata('success_message');

      if(!empty($error))
      {
        var_dump($error);die();
        echo '<div class="alert alert-warning">'.$error.'</div>';
        $this->session->unset_userdata('error_message');
      }

      if(!empty($success))
      {
        echo '<div class="alert alert-success">'.$success.'</div>';
        $this->session->unset_userdata('success_message');
      }
      ?>
      <div class="center-align"><?php echo $search_title;?></div>
      <table class="table table-condensed table-bordered ">
			 	<thead>
					<tr>
            <th>#</th>
					  <th>Date</th>
					  <th>Ref Number</th>
					  <th>Description</th>
					  <th>Debit</th>
            <th>Credit</th>
					  <th>Bal</th>
            <!-- <th>Action</th> -->
					</tr>
				 </thead>
			  	<tbody>
            <?php echo $result;?>
			  	</tbody>
			</table>

    </div>

</section>
</div>
</div>

 <script type="text/javascript">

        function get_transaction_type_list(type)
        {
            // var type = getRadioCheckedValue(radio_name);
            // $("#charge_to_id").customselect()="";
            // alert(radio_name);
            var myTarget1 = document.getElementById("creditor_div");

            if(type == 1)
            {


                myTarget1.style.display = 'block';
                $('#creditor_id').addClass('select2');
            }
            else {
              myTarget1.style.display = 'none';
            }
            var url = "<?php echo site_url();?>accounting/petty_cash/get_list_type_petty_cash/1";
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

        function display_payment_model(modal_id)
      	{
      		$('#modal-defaults'+modal_id).modal('show');
      		$('#datepicker1'+modal_id).datepicker({
    	      autoclose: true,
    	      format: 'yyyy-mm-dd',
    	    })
      	}


        function check_payment_type(payment_type_id){

          var myTarget1 = document.getElementById("cheque_div");

          var myTarget2 = document.getElementById("mpesa_div");

          var myTarget3 = document.getElementById("insuarance_div");

          if(payment_type_id == 1)
          {
            // this is a check

            myTarget1.style.display = 'block';
            myTarget2.style.display = 'none';
            myTarget3.style.display = 'none';
          }
          else if(payment_type_id == 2)
          {
            myTarget1.style.display = 'none';
            myTarget2.style.display = 'none';
            myTarget3.style.display = 'none';
          }
          else if(payment_type_id == 3)
          {
            myTarget1.style.display = 'none';
            myTarget2.style.display = 'none';
            myTarget3.style.display = 'block';
          }
          else if(payment_type_id == 4)
          {
            myTarget1.style.display = 'none';
            myTarget2.style.display = 'none';
            myTarget3.style.display = 'none';
          }
          else if(payment_type_id == 5)
          {
            myTarget1.style.display = 'none';
            myTarget2.style.display = 'block';
            myTarget3.style.display = 'none';
          }
          else
          {
            myTarget2.style.display = 'none';
            myTarget3.style.display = 'block';
          }

        }

    </script>
