<!-- search -->
<?php //echo $this->load->view('search/search_petty_cash', '', TRUE);

    $properties = $this->property_model->get_active_property();
    $rs8 = $properties->result();
    $property_list = '';
    foreach ($rs8 as $property_rs) :
        $property_id = $property_rs->property_id;
        $property_name = $property_rs->property_name;
        $property_location = $property_rs->property_location;

        $property_list .="<option value='".$property_id."'>".$property_name." Location: ".$property_location."</option>";

    endforeach;

?>
<!-- end search -->
<!--begin the reports section-->
<?php
//unset the sessions set\
?>
<!--end reports -->
<div class="row">
    <div class="col-md-12">

       <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php echo $title;?></h3>

                  <div class="box-tools pull-right">
                  </div>
                </div>
                <div class="box-body">
                <div class="pull-right">
                	<!-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#record_petty_cash"><i class="fa fa-plus"></i> Record</button>
                	<a href="<?php echo base_url().'accounts/petty_cash/print_petty_cash/';?>" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-print"></i> Print</a>
                	<a href="<?php echo base_url().'administration/sync_app_petty_cash';?>" class="btn btn-sm btn-info"><i class="fa fa-sign-out"></i> Sync</a> -->
                </div>

            	<?php echo form_open("finance/purchases/record_petty_cash", array("class" => "form-horizontal"));?>
                <div class="row">
                	<div class="col-md-12">
            			<div class="col-md-6">
                    <div class="form-group" >
                        <label class="col-md-4 control-label">Expenses Acccount *</label>

                        <div class="col-md-8">
                            <select class="form-control select2" name="account_to_id"  required>
                              <option value="0"> ---- Select an invoice ----</option>
                              <?php
                              if($purchase_items->num_rows() > 0)
                              {
                                foreach ($purchase_items->result() as $key => $value) {
                                  // code...
                                  $document_number = $value->document_number;
                                  $transaction_number = $value->transaction_number;
                                  $account_name = $value->account_name;
                                  $finance_purchase_id = $value->finance_purchase_id;
                                  echo '<option value="'.$finance_purchase_id.'">'.$account_name.'  '.$finance_purchase_amount.' </option>';
                                }
                              }
                              ?>

                            </select>
                        </div>
                    </div>
            			</div>
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
                    <div class="form-group" >
                        <label class="col-md-4 control-label">Payment Account*</label>

                        <div class="col-md-8">
                            <select class="form-control select2" name="account_from_id" required>
                              <option value="0"> ---- Select paying account ----</option>
                              <?php
                              if($accounts->num_rows() > 0)
                              {
                                foreach ($accounts->result() as $key => $value) {
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
                    <div class="form-group">
                        <label class="col-md-4 control-label">Payment Reference No *</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="reference_number" placeholder="Ref No." required/>
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
                 <hr>
                <!-- Modal -->
                <div class="modal fade" id="record_petty_cash" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Record Petty Cash</h4>
                            </div>
                            <div class="modal-body">

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
			<?php
			if(!empty($search))
			{
				?>
                <a href="<?php echo base_url().'accounts/petty_cash/close_search/';?>" class="btn btn-sm btn-success"><i class="fa fa-print"></i> Close Search</a>
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



?>
              <table class="table table-hover table-bordered ">
      				 	<thead>
      						<tr>
      						  <th>Transaction Date</th>
      						  <th>Account</th>
      						  <th>Description</th>
      						  <th>Debit</th>
      						  <th>Credit</th>
      						</tr>
      					 </thead>
        			  	<tbody>
        			  	</tbody>
      				</table>

            </div>
		    </div>
    </div>
</div>

 <script type="text/javascript">

       // $(function() {
       //   // alert('sdjkahsjk');
       //    var url = "<?php echo site_url();?>accounting/petty_cash/get_list_type_petty_cash/1";
       //
       //    //get department services
       //    $.get( url, function( data )
       //    {
       //        $( "#charge_to_id" ).html( data );
       //        // $(".custom-select").customselect();
       //    });
       //  });

        function get_transaction_type_list(radio_name)
        {
            var type = getRadioCheckedValue(radio_name);
            // $("#charge_to_id").customselect()="";
            // alert(type);
            var myTarget1 = document.getElementById("creditor_div");

            if(type == 1)
            {

                $('#creditor_id').addClass('select2');
                myTarget1.style.display = 'block';
            }
            else {
              myTarget1.style.display = 'none';
            }


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
