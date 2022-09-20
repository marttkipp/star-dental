<?php

$result = '<table class="table table-bordered table-condensed" >
				<thead>
					<th></th>
					<th>Reference</th>
					<th>Amount</th>
					<th>Reason</th>
					<th>Action</th>
				</thead>
				<tbody>

			';
$unallocated_payments = $this->hospital_administration_model->get_all_unallocated_batch_payments($batch_receipt_id);


if($unallocated_payments->num_rows() > 0)
{	
	$count = 0;
	foreach ($unallocated_payments->result() as $key => $value) {
		# code...
		$amount_paid = $value->amount_paid;
		$invoice_number = $value->invoice_number;
		$reason = $value->reason;
		$unallocated_payment_id = $value->unallocated_payment_id;
		$count++;
		$result .= '<tr>
						<td>'.$count.'</td>
						<td>'.$invoice_number.'</td>
						<td>'.number_format($amount_paid,2).'</td>
						<td>'.$reason.'</td>
						<td><a onclick="delete_unallocated_payment('.$unallocated_payment_id.','.$batch_receipt_id.')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> </a></td>


					</tr>';

	}
}

$result .= '
				</tbody>
			</table>

			';
echo $result;
?>