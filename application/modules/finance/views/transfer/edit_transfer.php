 <?php

 $accounts = $this->purchases_model->get_transacting_accounts("Bank");


 $finance_transfer = $this->transfer_model->get_transfer_details($finance_transfer_id);


 if($finance_transfer->num_rows() > 0)
 {
 	foreach ($finance_transfer->result() as $key => $value) {
 		# code...
 		$finance_transfer_amount = $value->finance_transfer_amount;
 		$reference_number = $value->reference_number;
 		$account_from_id = $value->account_from_id;
 		// $account_to_id = $value->account_to_id;
 		$transaction_date = $value->transaction_date;
 		$remarks = $value->remarks;
 		

 	}
 }

 ?>
 <div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <h2 class="panel-title">Make Transfer
            </h2>
        </header>
        <div class="panel-body">
            <?php echo form_open_multipart($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Account From</label>
                            <div class="col-lg-8">
                                <select id="account_from_id" name="account_from_id" class="form-control" onchange="get_accounty_type_list(this.value)" required>
                                    <!-- <option value="">--- Account ---</option> -->
                                    <?php
                                     if($accounts->num_rows() > 0)
                                     {
                                         foreach($accounts->result() as $row):
                                             // $company_name = $row->company_name;
                                             $account_name = $row->account_name;
                                             $account_id = $row->account_id;

                                             if($account_from_id == $account_id)
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
                            <label class="col-lg-4 control-label">Reference No *</label>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="reference_number" placeholder="Reference Number" value="<?php echo $reference_number;?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Transfer date: </label>

                            <div class="col-lg-8">
                                <div class="input-group">
                                    <span class="input-group-addon">
                               <i class="fa fa-calendar"></i>
                           </span>
                                    <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="transfer_date" placeholder="Transfer Date" value="<?php echo $transaction_date;?>" id="datepicker2" required>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">

                        <!-- Activate checkbox -->


                        <div class="form-group">
                            <label class="col-lg-4 control-label">Transfer to: </label>

                            <div class="col-lg-8">
                                <select name="account_to_id" class="form-control select2" id="charge_to_id" required>
                                  <option value="">---- select an account to transfer to ------</option>


                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Amount *</label>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="amount" placeholder="Amount" value="<?php echo $finance_transfer_amount;?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Description *</label>
                            <div class="col-lg-8">
                                <textarea class="form-control" name="description" placeholder="Transfer Description" required="required"><?php echo $remarks?></textarea>
                            </div>
                        </div>
                        <div class="form-actions center-align">
                            <button class="submit btn btn-primary btn-sm" type="submit" onclick="return confirm('Are you sure you want to update the transfer details? ')">
                                Update Transfer
                            </button>
                        </div>
                    </div>

                </div>
            <?php echo form_close();?>
        </div>
      </section>
  </div>

  <script type="text/javascript">

  	$(function() {
   		// $("#doctor_idd").customselect();
   		var account_from_id = <?php echo $account_from_id;?>

   		// alert(account_from_id);
   		get_accounty_type_list(account_from_id);
   
	});
    function get_accounty_type_list(radio_name) {
        var type = radio_name;
        // $("#charge_to_id").customselect()="";
        // alert(radio_name);
        var url = "<?php echo site_url();?>finance/transfer/get_list_type/" + type;
        // alert(url);
        //get department services
        $.get(url, function(data) {
            $("#charge_to_id").html(data);
            // $(".custom-select").customselect();
        });

    }

    function getRadioCheckedValue(radio_name) {
        var oRadio = document.forms[0].elements[radio_name];

        for (var i = 0; i < oRadio.length; i++) {
            if (oRadio[i].checked) {
                return oRadio[i].value;
            }
        }

        return '';
    }
</script>
