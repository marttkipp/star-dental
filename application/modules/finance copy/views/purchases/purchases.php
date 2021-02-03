
<div class="row">
    <div class="col-md-12">
      <section class="panel">
          <header class="panel-heading">
              <h3 class="panel-title">Search </h3>
          </header>
          <div class="panel-body">
               <div class="pull-right">
                 <!-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#record_petty_cash"><i class="fa fa-plus"></i> Record</button>
                 <a href="<?php echo base_url().'accounts/petty_cash/print_petty_cash/';?>" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-print"></i> Print</a>
                 <a href="<?php echo base_url().'administration/sync_app_petty_cash';?>" class="btn btn-sm btn-info"><i class="fa fa-sign-out"></i> Sync</a> -->
               </div>

             <?php echo form_open("finance/purchases/search_purchases", array("class" => "form-horizontal"));?>
               <div class="row">
                 <div class="col-md-12">

                 <div class="col-md-3">
                   <div class="form-group">
                       <label class="col-md-4 control-label">Ref No *</label>

                       <div class="col-md-8">
                           <input type="text" class="form-control" name="transaction_number" placeholder="Transaction Number" />
                       </div>
                   </div>
                 </div>
                  <div class="col-md-3">
                       <div class="form-group">
                           <label class="col-md-4 control-label">Date From: </label>

                           <div class="col-md-8">
                               <div class="input-group">
                                   <span class="input-group-addon">
                                       <i class="fa fa-calendar"></i>
                                   </span>
                                   <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_from" placeholder="Transaction date" value="<?php echo date('Y-m-d');?>" id="datepicker" >
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
                                     <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="date_ro" placeholder="Transaction date" value="<?php echo date('Y-m-d');?>" id="datepicker1" >
                                 </div>
                             </div>
                         </div>


                 </div>
                 <div class="col-md-3">
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
    <div class="col-md-12">
      <section class="panel">
          <header class="panel-heading">
              <h3 class="panel-title">Add purchased item </h3>
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
                 <br>
                 <div class="row">
                       <div class="col-md-12">
                           <div class="text-center">
                               <button type="submit" class="btn btn-sm btn-primary">Save record</button>
                           </div>
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
      $search = $this->session->userdata('search_purchases');
			if(!empty($search))
			{
				?>
                <a href="<?php echo base_url().'finance/purchases/close_search';?>" class="btn btn-sm btn-success"><i class="fa fa-print"></i> Close Search</a>
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
  if($query_purchases->num_rows() > 0)
  {
    foreach ($query_purchases->result() as $key => $value) {
      // code...
      $document_number = $value->document_number;
      $transaction_number = $value->transaction_number;
      $transaction_date = $value->transaction_date;
      $creditor_name = $value->creditor_name;
      $creditor_id = $value->creditor_id;
      $property_id = $value->property_id;
      $account_name = $value->account_name;
      $finance_purchase_id = $value->finance_purchase_id;
      $finance_purchase_description = $value->finance_purchase_description;
      $finance_purchase_amount = $value->finance_purchase_amount;
      $amount_paid = $this->purchases_model->get_amount_paid($finance_purchase_id);
      $checkbox_data = array(
                'name'        => 'visit[]',
                'id'          => 'checkbox'.$finance_purchase_id,
                'class'          => 'css-checkbox lrg',
                'value'       => $finance_purchase_id
              );

      if($amount_paid == $finance_purchase_amount)
      {
        $status = '<td class="success">Fully Paid</td>';
      }
      else if($amount_paid >0) {
        $status = '<td class="warning">Partially paid</td>';
      }
      else {
        $status = '<td class="primary">Not paid</td>';
      }

      $balance = $finance_purchase_amount - $amount_paid;
      $count++;
      $result .='
                <tr>
                  <td>'.$count.'</td>
                  <td>'.form_checkbox($checkbox_data).'<label for="checkbox'.$finance_purchase_id.'" name="checkbox79_lbl" class="css-label lrg klaus"></label>'.'</td>
                  <td>'.$transaction_date.'</td>
                  <td>'.$transaction_number.'</td>
                  <td>'.$finance_purchase_description.'</td>
                  <td>'.number_format($finance_purchase_amount,2).'</td>
                  <td>'.number_format($amount_paid,2).'</td>
                  <td>'.$creditor_name.'</td>
                  '.$status.'
                  <td><button type="button" class="btn btn-sm btn-success text-center" onclick="display_payment_model('.$finance_purchase_id.')" ><i class="fa fa-plus"></i> Make Payment </button>
                      <div class="modal fade " id="modal-defaults'.$finance_purchase_id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                          <div class="modal-dialog modal-lg" role="document">
                              <div class="modal-content ">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">Make Payment for '.$transaction_number.' of Balance Kes. '.$balance.'</h4>
                                  </div>
                                  '.form_open("finance/purchases/make_payment/".$finance_purchase_id, array("class" => "form-horizontal")).'

                                  <div class="modal-body">
                                  <div class="row">
                                    <div class="col-md-12">
                                      <div class="form-group">
                                          <label class="col-md-4 control-label">Transaction date: </label>

                                          <div class="col-md-8">
                                              <div class="input-group">
                                                  <span class="input-group-addon">
                                                      <i class="fa fa-calendar"></i>
                                                  </span>
                                                  <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="payment_date" placeholder="Transaction date" value="'.date('Y-m-d').'" id="datepicker1'.$finance_purchase_id.'" required>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="form-group" >
                                          <label class="col-md-4 control-label">Payment Account*</label>

                                          <div class="col-md-8">
                                              <select class="form-control" name="account_from_id" required>
                                                <option value="0"> ---- Select paying account ----</option>';

                                                if($accounts->num_rows() > 0)
                                                {
                                                  foreach ($accounts->result() as $key => $value) {
                                                    // code...
                                                    $account_id = $value->account_id;
                                                    $account_name = $value->account_name;
                                                    $result .= '<option value="'.$account_id.'"> '.$account_name.'</option>';
                                                  }
                                                }
                                                $result .= '
                                              </select>
                                          </div>
                                      </div>
                                        <input type="hidden" class="form-control" name="balance" placeholder="Ref No." value="'.$balance.'" required/>
                                      <div class="form-group">
                                          <label class="col-md-4 control-label">Payment Reference No *</label>

                                          <div class="col-md-8">
                                              <input type="text" class="form-control" name="reference_number" placeholder="Ref No." required/>
                                          </div>
                                      </div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Amount *</label>

                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="amount_paid" placeholder="'.$balance.'" required/>
                                            </div>
                                        </div>


                                    </div>
                                    </div>

                                  </div>
                                  <div class="modal-footer">
                                    <button type="submit" class="btn btn-sm btn-success" >Add Payment</button>
                                      <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                                  </div>

                                     '.form_close().'
                              </div>
                          </div>
                      </div>

                  </td>
                </tr>
                ';
    }
  }
?>
<section class="panel">
    <header class="panel-heading">
        <h3 class="panel-title">All Transactions </h3>
    </header>
    <div class="panel-body">
      <table class="table table-hover table-bordered ">
			 	<thead>
					<tr>
            <th>#</th>
            <th></th>
					  <th>Date</th>
					  <th>Ref Number</th>
					  <th>Description</th>
					  <th>Invoice Amount</th>
            <th>Amount Paid</th>
					  <th>Creditor</th>
            <th>Action</th>
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
