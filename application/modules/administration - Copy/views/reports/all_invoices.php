<!-- search -->
<?php echo $this->load->view('search/invoices', '', TRUE);?>
<!-- end search -->
<?php //echo $this->load->view('transaction_statistics', '', TRUE);?>

<div class="row">
    <div class="col-md-12">

        <section class="panel panel-featured panel-featured-info">
            <header class="panel-heading">
            	 <h2 class="panel-title"><?php echo $title;?></h2>
            </header>

          <!-- Widget content -->
                <div class="panel-body">
<?php
		$result = '';
		if(!empty($search))
		{
			echo '<a href="'.site_url().'administration/reports/close_invoice_search" class="btn btn-sm btn-warning">Close Search</a>';
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

		$method_rs = $this->accounts_model->get_payment_methods();

		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;

			$result .=
				'
					<table class="table table-hover table-bordered table-striped table-responsive col-md-12">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Visit Date</th>
						  <th>Invoice</th>
						  <th>Patient</th>
						  <th>Phone</th>
						  <th>Category</th>
						  <th>Doctor</th>
						  <th>Invoice Total</th>
						  <th>Patient Bill</th>
						  <th>Insurance Bill</th>
						  <th>Paid amount</th>
						  <th>Balance</th>
						  <th colspan="2"></th>
						</tr>
					  </thead>
					  <tbody>
			';

			$personnel_query = $this->personnel_model->get_all_personnel();

			foreach ($query->result() as $row)
			{
				$total_invoiced = 0;
				$visit_date = date('jS M Y',strtotime($row->visit_date));
				$visit_time = date('H:i a',strtotime($row->visit_time));
				if($row->visit_time_out != '0000-00-00 00:00:00')
				{
					$visit_time_out = date('H:i a',strtotime($row->visit_time_out));
				}
				else
				{
					$visit_time_out = '-';
				}

				$visit_id = $row->visit_id;
				$patient_id = $row->patient_id;
				$personnel_id = $row->personnel_id;
				$dependant_id = $row->dependant_id;
				$strath_no = $row->strath_no;
				$visit_type_id = $row->visit_type_id;
				$visit_type = $row->visit_type;
				$visit_table_visit_type = $visit_type;
				$invoice_number = $visit_id;//$row->invoice_number;
				$patient_table_visit_type = $visit_type_id;
				$coming_from = $this->reception_model->coming_from($visit_id);
				$sent_to = $this->reception_model->going_to($visit_id);
				$visit_type_name = $row->visit_type_name;
				$patient_othernames = $row->patient_othernames;
				$patient_surname = $row->patient_surname;
				$patient_phone1 = $row->patient_phone1;
				$patient_date_of_birth = $row->patient_date_of_birth;
				$close_card = $row->close_card;
				$hold_card = $row->hold_card;
				$invoice_number = $row->invoice_number;
				$visit_type_id = $row->visit_type;
				$parent_visit = $row->parent_visit;
				$rejected_amount = $row->rejected_amount;

				// this is to check for any credit note or debit notes



				//creators and editors
				if($personnel_query->num_rows() > 0)
				{
					$personnel_result = $personnel_query->result();

					foreach($personnel_result as $adm)
					{
						$personnel_id2 = $adm->personnel_id;

						if($personnel_id == $personnel_id2)
						{
							$doctor = $adm->personnel_onames.' '.$adm->personnel_fname;
							break;
						}

						else
						{
							$doctor = '-';
						}
					}
				}

				else
				{
					$doctor = '-';
				}

				$count++;

				//payment data
				$cash = $this->reports_model->get_all_visit_payments($visit_id);
				$charges = '';

				foreach($services_query->result() as $service)
				{
					$service_id = $service->service_id;
					$visit_charge = $this->reports_model->get_all_visit_charges($visit_id, $service_id);
					$total_invoiced += $visit_charge;

					//$charges .= '<td>'.$visit_charge.'</td>';
				}
				if($hold_card == 1)
				{
					$button ='<td><a href="'.site_url().'reception/unhold_card/'.$visit_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to unhold this card?\');">Unhold Card</a></td>';
				}
				else
				{
					if($close_card == 1)
					{
						$button ='<td><a href="'.site_url().'accounts/print_invoice_new/'.$visit_id.'" class="btn btn-sm btn-success" target="_blank">Invoice</a></td>
								 <td><a href="'.site_url().'administration/reports/open_visit_current/'.$visit_id.'"  onclick="return confirm(\'Do you want to open card ?\');" class="btn btn-sm btn-info" >Open Card</a></td>';
					}
					else
					{
						$button ='<td><a href="'.site_url().'administration/reports/end_visit_current/'.$visit_id.'"  onclick="return confirm(\'Do you want to close visit ?\');" class="btn btn-sm btn-danger" >Close Card</a></td>';
					}
				}
				// payment value ///

				// var_dump($parent_visit); die();



				$payments_value = $this->accounts_model->total_payments($visit_id);
				$invoice_total = $amount_payment  = $this->accounts_model->total_invoice($visit_id);

				// end of the debit and credit notes

				$balance = $this->accounts_model->balance($payments_value,$invoice_total);

				$rs_rejection = $this->dental_model->get_visit_rejected_updates_sum($visit_id,$visit_type);
				$total_rejected = 0;
				if(count($rs_rejection) >0){
				  foreach ($rs_rejection as $r2):
				    # code...
				    $total_rejected = $r2->total_rejected;

				  endforeach;
				}

				$rejected_amount += $total_rejected;

				if($visit_type_id > 1 AND $rejected_amount > 0)
				{

				}



				// if($parent_visit == 0 OR empty($parent_visit))
				// {
				// 	$invoice_total = $amount_payment - $rejected_amount;
				// }
				// else
				// {
				// 	$rejected_amount = $this->accounts_model->get_child_amount_payable($visit_id);
				// 	$invoice_total = $rejected_amount;
				// }


				// if($parent_visit == 0 OR empty($parent_visit))
				// {
				// 	$balance = $invoice_total - $payments_value;
				// }
				// else
				// {
				// 	$rejected_amount = $this->accounts_model->get_child_amount_payable($visit_id);
				// 	// echo $rejected_amount; die();
				// 	$balance = $rejected_amount - $payments_value;



				// }

				// $invoice_total = $invoice_total - $rejected_amount;


					// echo $invoice_total; die();
				if($visit_type > 1 AND $total_rejected > 0)
				{
					$payable_by_patient = $rejected_amount;
					$payable_by_insurance = $invoice_total - $rejected_amount;
				}
				else if($visit_type > 1 AND $total_rejected == 0 OR empty($total_rejected))
				{
					$payable_by_patient = 0;
					$payable_by_insurance = $invoice_total;
				}
				else
				{
					$payable_by_patient = $invoice_total;
					$payable_by_insurance = 0;
				}

					$result .=
						'
							<tr>
								<td>'.$count.'</td>
								<td>'.$visit_date.'</td>
								<td>'.$invoice_number.'</td>
								<td>'.$patient_surname.' '.$patient_othernames.'</td>
								<td>'.$patient_phone1.'</td>
								<td>'.$visit_type_name.'</td>
								<td>'.$doctor.'</td>
								<td>'.number_format($invoice_total,2).'</td>
								<td>'.number_format($payable_by_patient,2).'</td>
								<td>'.number_format($payable_by_insurance,2).'</td>
								<td>'.number_format($payments_value,2).'</td>
								<td>'.number_format($balance,2).'</td>

								<td><button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#book-appointment'.$visit_id.'">UPDATE </button>
								<div class="modal fade " id="book-appointment'.$visit_id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
								    <div class="modal-dialog modal-lg" role="document">
								        <div class="modal-content ">
								            <div class="modal-header">
								            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								            	<h4 class="modal-title" id="myModalLabel">Update Payment for Invoice #'.$visit_id.' BALANCE KES '.number_format($balance,2).'</h4>
								            </div>
								            '.form_open("administration/reports/receipt_payment/".$visit_id, array("class" => "form-horizontal")).'

								            <div class="modal-body">
								            	<div class="row">
								            		<input type="hidden" name="redirect_url" id="redirect_url'.$visit_id.'" value="'.$this->uri->uri_string().'">
								            		<input type="hidden" name="patient_id" id="patient_id'.$visit_id.'" value="'.$patient_id.'">
								            		<div class="col-md-12">
								            			<div class="col-md-12">
								            				<div class="form-group">
																<label class="col-lg-4 control-label"> Payment Type: </label>

																<div class="col-lg-8">
																	<select class="form-control" name="payment_method'.$visit_id.'" id="payment_type_id'.$visit_id.'" onchange="check_payment_type('.$visit_id.')">
																		<option value="0">--- select a method of payment --</option>';
																 $num_rows = count($method_rs);
																	 if($num_rows > 0)
																	  {

																		foreach($method_rs as $res)
																		{
																		  $payment_method_id = $res->payment_method_id;
																		  $payment_method = $res->payment_method;

																			$result .= '<option value="'.$payment_method_id.'">'.$payment_method.'</option>';

																		}
																	  }

												                    $result .=' </select>
							                                    </div>
															</div>

															<div id="mpesa_div'.$visit_id.'" class="form-group" style="display:none;" >
																<label class="col-lg-4 control-label"> Mpesa TX Code: </label>

																<div class="col-lg-8">
																	<input type="text" class="form-control" name="mpesa_code" placeholder="">
																</div>
															</div>

															<div id="insuarance_div'.$visit_id.'" class="form-group" style="display:none;" >
																<label class="col-lg-4 control-label"> Reference Number: </label>
																<div class="col-lg-8">
																	<input type="text" class="form-control" name="debit_card_detail" placeholder="">
																</div>
															</div>

															<div id="cheque_div'.$visit_id.'" class="form-group" style="display:none;" >
																<label class="col-lg-4 control-label"> Cheque Number: </label>

																<div class="col-lg-8">
																	<input type="text" class="form-control" name="cheque_number" placeholder="">
																</div>
															</div>
															<div class="form-group">
																<label class="col-lg-4 control-label"> Amount: </label>

																<div class="col-lg-8">
							                                       <input type="text" name="amount'.$visit_id.'" class="form-control" value=""/>
							                                    </div>
															</div>


								            			</div>
								            		</div>
								            	</div>



								            </div>
								            <div class="modal-footer">
								            	<button  class="btn btn-sm btn-success" type="submit">Update Payment Info</button>
								                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
								            </div>

								               '.form_close().'
								        </div>
								    </div>
								</div>

							</td>
								<td><a href="'.site_url().'accounts/print_invoice_new/'.$visit_id.'" class="btn btn-sm btn-success" target="_blank">Invoice</a></td>
								<td><a href="'.site_url().'receipt-payment/'.$visit_id.'/1" class="btn btn-sm btn-warning" >Payments</a></td>


							</tr>
					';
			}

			$result .=
			'
						  </tbody>
						</table>
			';
		}

		else
		{
			$result .= "There are no visits";
		}

		echo $result;
?>
          </div>

          <div class="widget-foot">

				<?php if(isset($links)){echo $links;}?>

                <div class="clearfix"></div>

            </div>

		</section>
    </div>
  </div>
  <script type="text/javascript">

  	function check_payment_type(visit_id){

   		var payment_type_id = $('#payment_type_id'+visit_id).val();

   		// alert(payment_type_id);
	    var myTarget1 = document.getElementById("cheque_div"+visit_id);

	    var myTarget2 = document.getElementById("mpesa_div"+visit_id);

	    var myTarget3 = document.getElementById("insuarance_div"+visit_id);

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

  	WWW}
  </script>
