<!-- search -->
<?php echo $this->load->view('search/search_creditor_account', '', TRUE);?>
<!-- end search -->

<div class="row">
    <div class="col-md-12">

        <section class="panel">
            <header class="panel-heading">

                <h2 class="panel-title"><?php echo $title;?></h2>
                <a href="<?php echo site_url();?>accounting/creditors" class="btn btn-sm btn-warning pull-right" style="margin-top: -25px; "><i class="fa fa-arrow-left"></i> Back to creditors</a>
                <a href="<?php echo base_url().'financials/company_financial/print_creditor_statement/'.$creditor_id?>" class="btn btn-sm btn-success pull-right"  style="margin-top: -25px;margin-right: 5px;" target="_blank"><i class="fa fa-print"></i> Print</a>
                <a href="<?php echo site_url().'search-creditor-bill/'.$creditor_id?>" class="btn btn-sm btn-primary pull-right"  style="margin-top: -25px; margin-right: 5px;"><i class="fa fa-plus"></i> Add Bill</a>
                <a href="<?php echo site_url().'search-creditor-credit-notes/'.$creditor_id?>" class="btn btn-sm btn-danger pull-right"   style="margin-top: -25px; margin-right: 5px;"><i class="fa fa-plus"></i> Add Credit Note</a>
                <a  href="<?php echo site_url().'search-creditor-payments/'.$creditor_id?>" class="btn btn-sm btn-primary pull-right"   style="margin-top: -25px; margin-right: 5px;"><i class="fa fa-plus"></i> Add Payment</a>

                <!-- <button type="button" class="btn btn-sm btn-default pull-right"  data-toggle="modal" data-target="#import_payments_account" style="margin-top: -25px; margin-right: 5px;"><i class="fa fa-plus"></i> Import Payments</button> -->


            </header>

            <div class="panel-body">
                <div class="pull-right">

                	<!--<a href="<?php echo base_url().'administration/sync_app_creditor_account';?>" class="btn btn-sm btn-info"><i class="fa fa-sign-out"></i> Sync</a>-->
                </div>
                <!-- Modal -->





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

			$search = $this->session->userdata('vendor_expense_search');
			$search_title = $this->session->userdata('vendor_expense_title_search');

			if(!empty($search))
			{
				echo '
				<a href="'.site_url().'financials/company_financial/close_creditor_expense_ledger/'.$creditor_id.'" class="btn btn-warning btn-sm ">Close Search</a>
				';
				echo $search_title;
			}
				$opening_balance_rs = $this->company_financial_model->get_creditor_statement_balance($creditor_id);
				$creditor_result = $this->company_financial_model->get_creditor_statement($creditor_id);

			?>

				<table class="table table-hover table-bordered ">
				 	<thead>
						<tr>
						  <th>Transaction Date</th>
						  <th>Document number</th>
						  <th>Description</th>
						  <th>Debit</th>
						  <th>Credit</th>
                          <th>Balance</th>
						</tr>
					 </thead>
				  	<tbody>
				  		<?php
				  		$result = '';
				  		$total_dr_amount =0;
				  		$total_cr_amount =0;
				  		$balance = 0;
				  		$cr_amount = 0;
				  		$dr_amount = 0;

				  		$rows = $opening_balance_rs->row();

				  		// var_dump($rows)die();
				  		if(!empty($search))
				  		{
				  			
				  			$balance += $rows->dr_amount;
				  			$balance -= $rows->cr_amount;
				  			$total_dr_amount += $dr_amount;
				  			$total_cr_amount += $cr_amount;
				  			$result .= '<tr>
						  					<td colspan="3">Opening Balance OR Balance B/F</td>
						  					<td>'.number_format($rows->dr_amount,2).'</td>
							  				<td>'.number_format($rows->cr_amount,2).'</td>
							  				<td>'.number_format($balance,2).'</td>
						  				</tr>';
				  		}
              $button = '';
				  		if($creditor_result->num_rows() > 0)
				  		{


				  			foreach ($creditor_result->result() as $key => $value) {
				  				# code...
				  				$referenceCode = $value->referenceCode;
				  				$transactionCode = $value->transactionCode;
				  				$dr_amount = $value->dr_amount;
				  				$cr_amount = $value->cr_amount;
				  				$transactionDescription = $value->transactionDescription;
				  				$transactionClassification = $value->transactionClassification;
                  				$referenceId = $value->referenceId;
                  				$transactionId = $value->transactionId;

				  				$transactionDate = $value->transactionDate;
				  				$balance += $dr_amount;
				  				$balance -= $cr_amount;
				  				$total_dr_amount += $dr_amount;
				  				$total_cr_amount += $cr_amount;
				  				$link = '';
                  				$button = '';
				  				if($transactionClassification === "Supplies Invoices")
				  				{
				                    if($transactionClassification === "Creditors Invoices")
				                    {
				                      $button =  '';
				                    }
				                    else {
				                      $button = '<td><a href="'.site_url().'inventory/orders/goods_received_notes/'.$referenceId.'" class="btn btn-xs btn-success" target="_blank"> View Invoice </a></td>';

				                    }

				  					$transactionCode = $referenceCode;
				  				}
                  				if($transactionClassification == "Supplies Credit Note")
				  				{
                    				$button = '<td><a href="'.site_url().'print-suppliers-credit-note/'.$referenceId.'" class="btn btn-xs btn-warning" target="_blank"> View Note </a></td>';

				  				}
				  				if($transactionClassification == "Creditors Invoices")
				  				{
                    				$button = '';

                    				$link = 'onclick="get_invoice_details('.$referenceId.')"';
				  				}

				  				if($transactionDescription == "Payment on account")
				  				{
                    				$button = '<td><a href="'.site_url().'allocate-payment/'.$referenceId.'/'.$transactionId.'/'.$creditor_id.'" class="btn btn-xs btn-danger"> Allocate payment </a></td>';

				  				}

				  				if($transactionClassification == 'Creditor Opening Balance')
				  				{
				  					$result .= '<tr>
							  					<td colspan="3">'.$transactionDescription.'</td>
							  					<td>'.number_format($dr_amount,2).'</td>
							  					<td>'.number_format($cr_amount,2).'</td>
							  					<td>'.number_format($balance,2).'</td>

							  				</tr>';

				  				}
				  				else
				  				{
				  					$result .= '<tr '.$link.'>
								  					<td>'.$transactionDate.'</td>
								  					<td>'.$transactionCode.'</td>
								  					<td> '.$transactionDescription.'</td>
								  					<td>'.number_format($dr_amount,2).'</td>
								  					<td>'.number_format($cr_amount,2).'</td>
								  					<td>'.number_format($balance,2).'</td>
	                          						'.$button.'
								  				</tr>';
								 	$result .= '
								 				<div id="table-row'.$referenceId.'" style="display:none;">

								 				<tr >
								  					<td id="link-details'.$referenceId.'" colspan="6" ></td>
								  				</tr>
								  				</div>';
							  		


				  				}

				  			}

				  			$result .= '<tr>
							  					<td colspan="3" >Totals</td>
							  					<td><b>'.number_format($total_dr_amount,2).'</b></td>
							  					<td><b>'.number_format($total_cr_amount,2).'</b></td>
							  					<td><b>'.number_format($balance,2).'</b></td>
							  				</tr>';
				  		}
				  		echo $result;
				  		?>
					</tbody>
				</table>
          	</div>
		</section>
    </div>
</div>
<script type="text/javascript">
    $(function() {
       // $("#billed_account_id").customselect();
       // $("#billed_supplier_id").customselect();
    });

    function get_invoice_details(reference_id)
    {

    	var XMLHttpRequestObject = false;
    
		if (window.XMLHttpRequest) {

		XMLHttpRequestObject = new XMLHttpRequest();
		} 

		else if (window.ActiveXObject) {
		XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
		}
		var config_url = $('#config_url').val();
		var url = "<?php echo site_url();?>finance/creditors/get_invoice_details/"+reference_id;
		// alert(url);

		  
		if(XMLHttpRequestObject) 
		{
			$('#table-row'+reference_id).css('display', 'block');
			var obj = document.getElementById("link-details"+reference_id);
			XMLHttpRequestObject.open("GET", url);
			    
			XMLHttpRequestObject.onreadystatechange = function(){
			  
			  if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
			    obj.innerHTML = XMLHttpRequestObject.responseText;
			  }
			}

			XMLHttpRequestObject.send(null);
		}
    }
</script>
