<?php

$result ='<table class="table table-hover table-bordered table-striped table-responsive col-md-12">
			  <thead>
				<tr>
				  <th>#</th>
				  <th>Estimate Date</th>
				  <th>Estimate Amount</th>
				  <th colspan="1"></th>
				</tr>
			  </thead>
			  <tbody>
			';
if($query->num_rows() > 0)
{
	$count =0;
	foreach ($query->result() as $key => $row) {
		# code...
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


			$invoice_total = $amount_payment  = $this->accounts_model->get_visit_quote_amount($visit_id);
			$count++;
			$result .='<tr>
						<td>'.$count.'</td>
						<td>'.$visit_date.'</td>
						<td>'.number_format($invoice_total,0).'</td>
						<td><a href="'.site_url()."print-quotation/".$visit_id.'" target="_blank" class="btn btn-xs btn-warning pull-right" > Print Quote</a></td>
						</tr>';

	}
}

$result .='</tbody>
		</table>';

echo $result;
?>