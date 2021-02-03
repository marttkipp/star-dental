<!-- search -->
<?php echo $this->load->view('search/search_creditor_account', '', TRUE);?>
<!-- end search -->

<div class="row">
    <div class="col-md-12">

        <section class="panel">
            <header class="panel-heading">

                <h2 class="panel-title"><?php echo ucfirst(strtolower($title));?></h2>
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

				<table class="table table-hover table-stripped table-condensed table-bordered ">
				 	<thead>
						<tr>
						  <th>Transaction Date</th>
						  <th>Document Type</th>
						  <th>Reference Code</th>
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
				  				$transactionName = $value->transactionName;
				  				$transactionId = $value->transactionId;
				  				$transactionDescription = $value->transactionDescription;
				  				$transactionClassification = $value->transactionClassification;
                  				$transactionId = $value->transactionId;
                  				$transactionId = $value->transactionId;
                  				$recepientId = $value->recepientId;


				  				$transactionDate = $value->transactionDate;
				  				$balance += $dr_amount;
				  				$balance -= $cr_amount;
				  				$total_dr_amount += $dr_amount;
				  				$total_cr_amount += $cr_amount;
				  				$link = '';
                  				$button = '';
                  				$color = 'default';
                  				$custom_color = 'default';
				  				if($transactionClassification === "Supplies Invoices")
				  				{
				                    if($transactionClassification === "Creditors Invoices")
				                    {
				                      $button =  '';
				                    }
				                    else {
				                      $button = '<td><a href="'.site_url().'inventory/orders/goods_received_notes/'.$transactionId.'" class="btn btn-xs btn-success" target="_blank"> View Invoice </a></td>';

				                    }
				                    $amount_paid = $this->company_financial_model->get_creditor_amount_paid($transactionId,$recepientId);


				                    if($dr_amount == $amount_paid)
				                    {
				                    	$custom = 'fully paid';
				                    	$custom_color = 'success';
				                    }
				                    else if($dr_amount > $amount_paid AND $amount_paid > 0)
				                    {
				                    	$custom = 'partially paid';
				                    	$custom_color = 'warning';

				                    }
				                    else if($dr_amount > $amount_paid AND $amount_paid == 0)
				                    {
				                    	$custom = 'Not paid';
				                    	$custom_color = 'info';

				                    }

				                    else
				                    {
				                    	$custom = 'over paid';
				                    	$custom_color = 'danger';
				                    }

				                    $referenceCode .= ' - '.$custom;
				  					$transactionCode = $referenceCode;
				  					$color = 'primary';
				  					$type= 1;
				  				}
                  				if($transactionClassification == "Supplies Credit Note")
				  				{
                    				$button = '<td><a href="'.site_url().'print-suppliers-credit-note/'.$transactionId.'" class="btn btn-xs btn-warning" target="_blank"> View Note </a></td>';
                    				$color = 'warning';
                    				$type= 2;

				  				}
				  				if($transactionClassification == "Creditors Invoices")
				  				{
                    				$button = '';
                    				$amount_paid = $this->company_financial_model->get_creditor_amount_paid($transactionId,$recepientId);


				                    if($dr_amount == $amount_paid)
				                    {
				                    	$custom = 'fully paid';
				                    	$custom_color = 'success';
				                    }
				                    else if($dr_amount > $amount_paid AND $amount_paid > 0)
				                    {
				                    	$custom = 'partially paid';
				                    	$custom_color = 'warning';

				                    }
				                    else if($dr_amount > $amount_paid AND $amount_paid == 0)
				                    {
				                    	$custom = 'Not paid';
				                    	$custom_color = 'info';

				                    }

				                    else
				                    {
				                    	$custom = 'over paid';
				                    	$custom_color = 'danger';
				                    }

				                    $referenceCode .= ' - '.$custom;
                    				$link = 'onclick="get_invoice_details('.$transactionId.',3)"';
                    				$color = 'primary';
                    				$type= 3;
				  				}

				  				if($transactionClassification == 'Creditors Invoices Payments')
				  				{
				  					$link = 'onclick="get_payment_details('.$transactionId.',4)"';
				  					$color = 'success';
				  					$type= 4;
				  				}
				  				if($transactionClassification == 'Supplies Invoices')
				  				{
				  					$link = 'onclick="get_supplier_details('.$transactionId.',6)"';
				  					$color = 'success';
				  					$type= 6;
				  				}
				  				if($transactionClassification == 'Creditors Credit Notes')
				  				{
				  					$link = 'onclick="get_credit_note_details('.$transactionId.',5)"';
				  					$color = 'danger';
				  					$type= 5;
				  				}

				  				if($transactionDescription == "Payment on account")
				  				{
                    				$button = '<td><a href="'.site_url().'allocate-payment/'.$transactionId.'/'.$transactionId.'/'.$creditor_id.'" class="btn btn-xs btn-danger"> Allocate payment </a></td>';

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
								  					<td class="'.$color.'"> '.strtoupper($transactionName).'</td>
								  					<td class="'.$color.'">'.strtoupper($referenceCode).'</td>
								  					<td class="'.$custom_color.'">'.number_format($dr_amount,2).'</td>
								  					<td>'.number_format($cr_amount,2).'</td>
								  					<td>'.number_format($balance,2).'</td>
								  				</tr>';
								 	$result .= '
								 				 
								 				<div id="table-row'.$transactionId.''.$type.'" class="table-rows" style="display:none;">
								 					<tr>
									  					<td colspan= 6>
									  						<div id="link-details'.$transactionId.''.$type.'"></div>
									  					</td>
								  					</tr>
								  				</div>
								  				';
							  		


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

    function get_invoice_details(reference_id,type)
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
			
			// var checked_data = $('#checked_data'+reference_id).val();
			// $('.table-rows').css('display', 'none');
			var checked_id = $('#table-row'+reference_id+type).css('display');
			
			if(checked_id == 'block')
			{
				// alert(checked_id);
				$('#table-row'+reference_id+type).css('display', 'none');
				$('#link-details'+reference_id+type).css('display', 'none');

			}
			else
			{
				$('#table-row'+reference_id+type).css('display', 'block');
				$('#link-details'+reference_id+type).css('display', 'block');
				var obj = document.getElementById("link-details"+reference_id+type);
				XMLHttpRequestObject.open("GET", url);
				    
				XMLHttpRequestObject.onreadystatechange = function(){
				  
				  if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
				    obj.innerHTML = XMLHttpRequestObject.responseText;
				  }
				}

				XMLHttpRequestObject.send(null);
			}
			

			
		}
    }

    function get_payment_details(reference_id,type)
    {

    	var XMLHttpRequestObject = false;
    
		if (window.XMLHttpRequest) {

		XMLHttpRequestObject = new XMLHttpRequest();
		} 

		else if (window.ActiveXObject) {
		XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
		}
		var config_url = $('#config_url').val();
		var url = "<?php echo site_url();?>finance/creditors/get_payment_details/"+reference_id;
		// alert(url);

		  
		if(XMLHttpRequestObject) 
		{
			
			// var checked_data = $('#checked_data'+reference_id).val();
			// $('.table-rows').css('display', 'none');
			var checked_id = $('#table-row'+reference_id+type).css('display');
			
			if(checked_id == 'block')
			{
				// alert(checked_id);
				$('#table-row'+reference_id+type).css('display', 'none');
				$('#link-details'+reference_id+type).css('display', 'none');

			}
			else
			{
				$('#table-row'+reference_id+type).css('display', 'block');
				$('#link-details'+reference_id+type).css('display', 'block');
				var obj = document.getElementById("link-details"+reference_id+type);
				XMLHttpRequestObject.open("GET", url);
				    
				XMLHttpRequestObject.onreadystatechange = function(){
				  
				  if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
				    obj.innerHTML = XMLHttpRequestObject.responseText;
				  }
				}

				XMLHttpRequestObject.send(null);
			}
			

			
		}
    }

    function get_credit_note_details(reference_id,type)
    {

    	var XMLHttpRequestObject = false;
    
		if (window.XMLHttpRequest) {

		XMLHttpRequestObject = new XMLHttpRequest();
		} 

		else if (window.ActiveXObject) {
		XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
		}
		var config_url = $('#config_url').val();
		var url = "<?php echo site_url();?>finance/creditors/get_credit_note_details/"+reference_id;
		// alert(url);

		  
		if(XMLHttpRequestObject) 
		{
			
			// var checked_data = $('#checked_data'+reference_id).val();
			// $('.table-rows').css('display', 'none');
			var checked_id = $('#table-row'+reference_id+type).css('display');
			
			if(checked_id == 'block')
			{
				// alert(checked_id);
				$('#table-row'+reference_id+type).css('display', 'none');
				$('#link-details'+reference_id+type).css('display', 'none');

			}
			else
			{
				$('#table-row'+reference_id+type).css('display', 'block');
				$('#link-details'+reference_id+type).css('display', 'block');
				var obj = document.getElementById("link-details"+reference_id+type);
				XMLHttpRequestObject.open("GET", url);
				    
				XMLHttpRequestObject.onreadystatechange = function(){
				  
				  if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
				    obj.innerHTML = XMLHttpRequestObject.responseText;
				  }
				}

				XMLHttpRequestObject.send(null);
			}
			

			
		}
    }

    function get_supplier_details(reference_id,type)
    {

    	var XMLHttpRequestObject = false;
    
		if (window.XMLHttpRequest) {

		XMLHttpRequestObject = new XMLHttpRequest();
		} 

		else if (window.ActiveXObject) {
		XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
		}
		var config_url = $('#config_url').val();
		var url = "<?php echo site_url();?>finance/creditors/get_suppliers_invoice_details/"+reference_id;
		// alert(url);

		  
		if(XMLHttpRequestObject) 
		{
			
			// var checked_data = $('#checked_data'+reference_id).val();
			// $('.table-rows').css('display', 'none');
			var checked_id = $('#table-row'+reference_id+type).css('display');
			
			if(checked_id == 'block')
			{
				// alert(checked_id);
				$('#table-row'+reference_id+type).css('display', 'none');
				$('#link-details'+reference_id+type).css('display', 'none');

			}
			else
			{
				$('#table-row'+reference_id+type).css('display', 'block');
				$('#link-details'+reference_id+type).css('display', 'block');
				var obj = document.getElementById("link-details"+reference_id+type);
				XMLHttpRequestObject.open("GET", url);
				    
				XMLHttpRequestObject.onreadystatechange = function(){
				  
				  if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
				    obj.innerHTML = XMLHttpRequestObject.responseText;
				  }
				}

				XMLHttpRequestObject.send(null);
			}
			

			
		}
    }
</script>
