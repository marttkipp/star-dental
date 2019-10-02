 <table align='center' class='table table-striped table-hover table-condensed'>
	<thead>
		<tr>
			<th>#</th>
			<th>Time</th>
			<th>Method</th>
			<th>Amount Charged</th>
			<th>Total Paid</th>
			<th>Change</th>
			<th colspan="2"></th>
		</tr>
	</thead>
	<tbody>
		<?php
		// $payments_rs = $this->accounts_model->payments(23);

		$credit_note_amount = $this->accounts_model->get_sum_credit_notes($visit_id);

		$num_pages = $total_rows/$per_page;

		if($num_pages < 1)
		{
			$num_pages = 0;
		}
		$num_pages = round($num_pages);

		if($page==0)
		{
			$counted = 0;
		}
		else if($page > 0)
		{
			$counted = $per_page*$page;
		}

		$total_payments = 0;
		$total_amount = ($total + $debit_note_amount) - $credit_note_amount;
		// var_dump($receipts_items->num_rows());
		if($receipts_items->num_rows() > 0)
		{
			foreach ($receipts_items->result() as $value => $key_items):
				$counted++;
				$payment_method = $key_items->payment_method;

				$time = $key_items->time;
				$payment_type = $key_items->payment_type;
				$payment_id = $key_items->payment_id;
				$payment_status = $key_items->payment_status;
				$payment_service_id = $key_items->payment_service_id;
				$change = $key_items->change;
				$service_name = '';
				
				if($payment_type == 1 && $payment_status == 1)
				{
					$amount_paid = $key_items->amount_paid;

					// if($change > 0)
					// {
					// 	$total_paid_invoice = $amount_paid + $change;
					// }
					// else
					// {
						$total_paid_invoice = $amount_paid;
					// }

					$amount_paidd = number_format($amount_paid,2);
					$total_paid_invoice = number_format($total_paid_invoice,2);
					if(count($item_invoiced_rs) > 0)
					{
						foreach ($item_invoiced_rs as $key_items):
						
							$service_id = $key_items->service_id;
							
							if($service_id == $payment_service_id)
							{
								$service_name = $key_items->service_name;
								break;
							}
						endforeach;
					}
				
					?>
					<tr>
						<td><?php echo $counted;?></td>
						<td><?php echo $time;?></td>
						<td><?php echo $payment_method;?></td>
						<td><?php echo $amount_paidd;?></td>
						<td><?php echo $total_paid_invoice;?></td>
						<td><?php echo $change;?></td>
						<td><a href="<?php echo site_url().'accounts/print_single_receipt/'.$payment_id;?>" class="btn btn-sm btn-warning" target="_blank"><i class="fa fa-print"></i></a>
						</td>
						<?php
						$personnel_id = $this->session->userdata('personnel_id');
						$is_admin = $this->reception_model->check_if_admin($personnel_id,1);

						if($is_admin OR $personnel_id == 0 )
						{
						?>

							<td>
                            	<button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#refund_payment<?php echo $payment_id;?>"><i class="fa fa-times"></i></button>
								<!-- Modal -->
								<div class="modal fade" id="refund_payment<?php echo $payment_id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
								    <div class="modal-dialog" role="document">
								        <div class="modal-content">
								            <div class="modal-header">
								            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								            	<h4 class="modal-title" id="myModalLabel">Cancel payment</h4>
								            </div>
								            <div class="modal-body">
								            	<?php echo form_open("accounts/cancel_payment/".$payment_id.'/'.$visit_id, array("class" => "form-horizontal","id"=>"payments-paid-form"));?>
								                <div class="form-group">
								                    <label class="col-md-4 control-label">Action: </label>
								                    <input type="hidden" name="payment_id" id="payment_id" value="<?php echo $payment_id;?>">
								                    <input type="hidden" name="visit_id" id="visit_id" value="<?php echo $visit_id;?>">
								                    <div class="col-md-8">
								                        <select class="form-control" name="cancel_action_id" id="cancel_action_id">
								                        	<option value="">-- Select action --</option>
								                            <?php
								                                if($cancel_actions->num_rows() > 0)
								                                {
								                                    foreach($cancel_actions->result() as $res)
								                                    {
								                                        $cancel_action_id = $res->cancel_action_id;
								                                        $cancel_action_name = $res->cancel_action_name;
								                                        
								                                        echo '<option value="'.$cancel_action_id.'">'.$cancel_action_name.'</option>';
								                                    }
								                                }
								                            ?>
								                        </select>
								                    </div>
								                </div>
								                
								                <div class="form-group">
								                    <label class="col-md-4 control-label">Description: </label>
								                    
								                    <div class="col-md-8">
								                        <textarea class="form-control" name="cancel_description" id="cancel_description"></textarea>
								                    </div>
								                </div>
								                
								                <div class="row">
								                	<div class="col-md-8 col-md-offset-4">
								                    	<div class="center-align">
								                        	<button type="submit" class="btn btn-primary">Save action</button>
								                        </div>
								                    </div>
								                </div>
								                <?php echo form_close();?>
								            </div>
								            <div class="modal-footer">
								                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								            </div>
								        </div>
								    </div>
								</div>

                            </td>
                        <?php
                    	}
                        ?>
					</tr>
					<?php
					$total_payments =  $total_payments + $amount_paid;
				}
			endforeach;

		}
		
		
		?>

		
	</tbody>
</table>
<div>
	<h2>Credit Note / Waiver  : <?php echo $credit_note_amount?></h2>
</div>
<div class="row">
			<div class="col-md-12" style="padding-right: 25px;">
				<div class="pull-right">
					<?php
						$link ='<ul style="list-style:none;">';
						// echo $page;
						if($num_pages > $page)
						{
							// echo "now ".$num_pages." ".$page;
							$last_page = $num_pages -1;

							if($page > 0 AND $page < $last_page)
							{
								// echo $page;
								$page++;
								// echo "now".$page;
								$previous = $page -2;
								$link .='<li onclick="get_next_payments_page('.$previous.','.$visit_id.')" class="pull-left" style="margin-right:20px;" > <i class="fa fa-angle-left"></i> Back</li>  <li onclick="get_next_payments_page('.$page.','.$visit_id.')" class="pull-right"> Next <i class="fa fa-angle-right"></i> </li>';
							}else if($page == $last_page)
							{
								$page++;

								$previous = $page -2;
								// echo "equal".$num_pages." ".$page;
								$link .='<li onclick="get_next_payments_page('.$previous.','.$visit_id.')" class="pull-left"> <i class="fa fa-angle-left"></i> Back</li>';
							}
							else
							{
								$page++;
								$link .='<li onclick="get_next_payments_page('.$page.','.$visit_id.')" class="pull-right"> Next <i class="fa fa-angle-right"></i> </li>';
							}
							// var_dump($link); die();
						}
						$link .='</ul>';
						echo $link;
						
					?>
				</div>
			</div>
		</div>