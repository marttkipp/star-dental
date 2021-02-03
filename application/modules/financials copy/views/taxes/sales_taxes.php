<?php echo $this->load->view('search/sales_taxes_search','', true);?>

<?php
$wht_payable = $this->company_financial_model->get_tax_total_wht_tax();
$gross_wht = (100*$wht_payable/5);
$vat_payable = $this->company_financial_model->get_tax_total_vat_tax();
$gross_vat = ((100*$vat_payable)/16);



$wht_receivable = 0;// $this->company_financial_model->get_total_wht_tax();
$gross_receivable_wht = 0;//(100*$wht_receivable/5);
$vat_receivable = 0;//$this->company_financial_model->get_total_receivable_wht_tax();
$gross_receivable_vat = (100*$vat_receivable/16);



$search = $this->session->userdata('tax_title_search');

if(!empty($search))
{
	$tax_search = ucfirst($search);
}
else {
	$tax_search = 'Reporting as of: '.date('M j, Y', strtotime(date('Y-01-01'))).' to '.date('M j, Y', strtotime(date('Y-m-d')));
}
?>
<div class="text-center">
	<h3 class="box-title">Sales Tax Summary</h3>
	<h5 class="box-title"><?php echo $tax_search?></h5>
	<h6 class="box-title">Created <?php echo date('M j, Y', strtotime(date('Y-m-d')));?></h6>
</div>

<section class="panel">

		<div class="panel-body">
    	<!-- <h3 class="box-title">Sale</h3> -->
    	<table class="table  table-striped table-condensed">
			<thead>
				<tr>
        			<th class="text-left">Taxes</th>
					<th class="text-right">Taxable Amount <br> (Before Tax)</th>
					<th class="text-right">Tax</th>
					<th class="text-right">Taxable Amount <br> (Before Tax)</th>
					<th class="text-right">Tax</th>
					<th class="text-right">Net Tax Owning<br> (Receivable)</th>

				</tr>
				<tr>
        			<th class="text-left"></th>
					<th class="text-right" colspan="2">Payables</th>
					<th class="text-right" colspan="2">Receivables</th>
					<th class="text-right"></th>

				</tr>
			</thead>
			<tbody>
				<tr>
        			<td >VAT (16.00 %)</td>
							<td class="text-right"><?php echo number_format($gross_vat,2);?></td>
							<td class="text-right"><?php echo number_format($vat_payable,2);?></td>
							<td class="text-right"><?php echo number_format($gross_receivable_vat,2)?></td>
							<td class="text-right"><?php echo number_format($vat_receivable,2)?></td>
							<td class="text-right"><?php echo number_format($vat_payable+$vat_receivable,2);?></td>
				</tr>
				<tr>
        	<td >Witholding Tax (5.00%)</td>
					<td class="text-right"><?php echo number_format($gross_wht,2);?></td>
					<td class="text-right"><?php echo number_format($wht_payable,2);?></td>
					<td class="text-right"><?php echo number_format(0,2)?></td>
					<td class="text-right">0.00</td>
					<td class="text-right"><?php echo number_format($wht_payable + $wht_receivable,2);?></td>
				</tr>

			</tbody>
		</table>
    </div>
</section>
