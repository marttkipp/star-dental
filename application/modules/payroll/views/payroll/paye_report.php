
<?php
$personnel_id = $this->session->userdata('personnel_id');
$prepared_by = $this->session->userdata('first_name');
$roll = $payroll->row();
$year = $roll->payroll_year;
$month = $roll->month_id;
$totals = array();

if ($query->num_rows() > 0)
{
	$count = 0;
	
	$result = 
	'
	<table class="table table-bordered table-striped table-condensed" id ="testTable">
		<thead>
			<tr>
				<th>#</th>
				<th>Ref</th>
				<th>Pin</th>
				<th>Personnel</th>
				<th>Residential Status</th>
				<th>Type Of Employee</th>
				<th>Basic Pay</th>
				<th>House Allowance</th>
				<th>Transport Allowance</th>
				<th>Leave Pay</th>
				<th>Overtime</th>
				<th>Directors</th>
				<th>Lumpsum</th>
				<th>Other Allowance</th>
				<th>Total Cash Pay</th>
				<th>Value Of Car Benefit</th>
				<th>Other Non Cash</th>
				<th>Total Non Cash</th>
				<th>Full Time Service Director</th>
				<th>Type Of Housing</th>
				<th>Rent Of House</th>
				<th>Computed Rent Of House</th>
				<th>Rent Recovered</th>
				<th>Net Value Of Housing</th>
				<th>Total Gross Pay</th>
				<th>30% Of Cash Pay</th>
				<th>Actual Contributiion</th>
				<th>Permissible Limit</th>
				<th>Mortgage Interest</th>
				<th>Hosp</th>
				<th>Amount of Benefit</th>
				<th>Taxable Pay</th>
				<th>Tax Charged</th>
				<th>Monthly Relief</th>
				<th>Insurance Relief</th>
				<th>PAYE</th>
				<th>Self Assed Tax</th>
				
	';
	$total_gross = 0;
	$total_paye = 0;
	$total_payments = 0;
	$total_savings = 0;
	$total_loans = 0;
	$total_net = 0;
	$benefits_amount = $payroll_data->benefits;
	$total_benefits = $payroll_data->total_benefits;
	$payments_amount = $payroll_data->payments;
	$total_payments2 = $payroll_data->total_payments;
	$allowances_amount = $payroll_data->allowances;
	$total_allowances2 = $payroll_data->total_allowances;
	$deductions_amount = $payroll_data->deductions;
	$total_deductions2 = $payroll_data->total_deductions;
	$other_deductions_amount2 = $payroll_data->other_deductions;
	$total_other_deductions2 = $payroll_data->total_other_deductions;
	$nssf_amount = $payroll_data->nssf;
	$nhif_amount = $payroll_data->nhif;
	$life_ins_amount = $payroll_data->life_ins;
	$paye_amount = $payroll_data->paye;
	$monthly_relief_amount = $payroll_data->monthly_relief;
	$insurance_relief_amount = $payroll_data->insurance_relief;
	$insurance_amount_amount = $payroll_data->insurance;
	$scheme = $payroll_data->scheme;
	$total_scheme = $payroll_data->total_scheme;
	$savings = $payroll_data->savings;
	$overtime_amount = $payroll_data->overtime;
	
	$result .= '
			</tr>
		</thead>
		<tbody>
	';
	
	foreach ($query->result() as $row)
	{
		$personnel_id = $row->personnel_id;
		$personnel_number = $row->personnel_number;
		$personnel_fname = $row->personnel_fname;
		$personnel_onames = $row->personnel_onames;
		$personnel_kra_pin = $row->personnel_kra_pin;
		$gross = 0;
		
		
		//basic
		$table_id = 0;
		//$basic_pay = $this->payroll_model->get_payroll_amount($personnel_id, $payroll_items, $basic_pay_table, $table_id);
		//$total_basic_pay += $basic_pay;
		//$gross += $basic_pay;
		
		$count++;
		$result .= 
		'
			<tr>
				<td>'.$count.'</td>
				<td>'.$personnel_number.'</td>
				<td>'.$personnel_kra_pin.'</td>
				<td>'.$personnel_onames.' '.$personnel_fname.'</td>
				<td>Resident</td>
				<td>Primary Employee</td>
				
		';
		
		//basic pay with payment id = 1
		$payment_amt = 0; 
		if($payments->num_rows() > 0)
		{
			$payment_id = 1;
			$table_id = $payment_id;
			$total_payment_amount[$payment_id] = 0;

			if(isset($total_payments2->$payment_id))
			{
				$total_payment_amount[$payment_id] = $total_payments2->$payment_id;
			}
			if($total_payment_amount[$payment_id] != 0)
			{
				if(isset($payments_amount->$personnel_id->$table_id))
				{
					$payment_amt = $payments_amount->$personnel_id->$table_id;
					$gross += $payment_amt;
				}
				if(!isset($total_personnel_payments[$payment_id]))
				{
					$total_personnel_payments[$payment_id] = 0;
				}
				$result .= 
				'
						<td>'.number_format($payment_amt, 2).'</td>
						
				';
			}
		}
		
		//house allowance
		if($allowances->num_rows() > 0)
		{
				$allowance_id = 7;
				$table_id = $allowance_id;
				$total_allowance_amount[$allowance_id] = 0;

				if(isset($total_allowances2->$allowance_id))
				{
					$total_allowance_amount[$allowance_id] = $total_allowances2->$allowance_id;
				}
				if (($total_allowance_amount[$allowance_id] < 0) OR ($total_allowance_amount[$allowance_id] > 0))
				{
					$allowance_amt = 0;
					if(isset($allowances_amount->$personnel_id->$table_id))
					{
						$allowance_amt = $allowances_amount->$personnel_id->$table_id;
						$gross += $allowance_amt;
					}
					if(!isset($total_personnel_allowances[$allowance_id]))
					{
						$total_personnel_allowances[$allowance_id] = 0;
					}
					if($allowance_amt != 0)
					{
						$result .= 
						'
								<td>'.number_format($allowance_amt, 2).'</td>
								
						';
					}
					else
					{
						$result .= 
						'
								<td>'.number_format(0, 2).'</td>
								
						';
					}
					
				}
				else
				{
					$result .= 
					'
							<td>'.number_format(0, 2).'</td>
							
					';
				}
		}
		//zero value for transport allowance
		$result .= 
		'
				<td>0</td>
				
		';
		//leave pay
		if($allowances->num_rows() > 0)
		{
				$allowance_id = 9;
				$table_id = $allowance_id;
				$total_allowance_amount[$allowance_id] = 0;

				if(isset($total_allowances2->$allowance_id))
				{
					$total_allowance_amount[$allowance_id] = $total_allowances2->$allowance_id;
				}
				if(($total_allowance_amount[$allowance_id] < 0) OR ($total_allowance_amount[$allowance_id] > 0))
				{
					if(isset($allowances_amount->$personnel_id->$table_id))
					{
						$allowance_amt = $allowances_amount->$personnel_id->$table_id;
						$gross += $allowance_amt;
					}
					if(!isset($total_personnel_allowances[$allowance_id]))
					{
						$total_personnel_allowances[$allowance_id] = 0;
					}
					if($allowance_amt > 0)
					{
						$result .= 
						'
								<td>'.number_format($allowance_amt, 2).'</td>
								
						';
					}
					else
					{
						$result .= 
						'
								<td>'.number_format(0, 2).'</td>
								
						';
					}
					
				}
				else
				{
					$result .= 
					'
							<td>'.number_format(0, 2).'</td>
							
					';
				}
		}
		
		//overtime
		$total_overtime_display = 0;
		if($overtime->num_rows() > 0)
		{
			foreach($overtime->result() as $res)
			{
				$overtime_name = $res->overtime_name;
				$overtime_id = $res->overtime_type;
				$table_id = $overtime_id;
				$overtime_amt = 0;
				if(isset($overtime_amount->$personnel_id->$table_id))
				{
					$overtime_amt =  $overtime_amount->$personnel_id->$table_id;
					$gross += $overtime_amt;
				}
				$total_overtime_display += $overtime_amt;
			}
		}
		$result .= '<td>'.number_format($total_overtime_display, 2).'</td>';
		
		//directors  and lump sum
		$result .= '<td>'.number_format(0, 2).'</td>';
		$result .= '<td>'.number_format(0, 2).'</td>';
		

		//other allowances excluding house,overtime and leave
		if($other_allowances->num_rows() > 0)
		{
			$total_cash_allowances = 0;
			foreach($other_allowances->result() as $res)
			{
				$allowance_id = $res->allowance_id;
				$table_id = $allowance_id;
				$total_allowance_amount[$allowance_id] = 0;

				if(isset($total_allowances2->$allowance_id))
				{
					$total_allowance_amount[$allowance_id] = $total_allowances2->$allowance_id;
				}
				if(($total_allowance_amount[$allowance_id] < 0) OR ($total_allowance_amount[$allowance_id] > 0))
				{
					$allowance_amt = 0;
					if(isset($allowances_amount->$personnel_id->$table_id))
					{
						$allowance_amt = $allowances_amount->$personnel_id->$table_id;
						//var_dump($allowance_amt);die();
						$total_cash_allowances += $allowance_amt;
					}
					if(!isset($total_personnel_allowances[$allowance_id]))
					{
						$total_personnel_allowances[$allowance_id] = 0;
					}
					
				}
					
			}
			//all other payment except basic pay e.g absent should fall under other allowances
			foreach($other_payments->result() as $res)
			{
				$payment_abbr = $res->payment_name;
				$payment_id = $res->payment_id;
				$table_id = $payment_id;
				$total_payment_amount[$payment_id] = 0;
				if(isset($total_payments2->$payment_id))
				{
					$total_payment_amount[$payment_id] = $total_payments2->$payment_id;
				}
				if(($total_payment_amount[$payment_id] < 0) OR ($total_payment_amount[$payment_id] > 0))
				{
					
					if(isset($payments_amount->$personnel_id->$table_id))
					{
						$payment_amt = $payments_amount->$personnel_id->$table_id;
						$total_cash_allowances += $payment_amt;
					}
					if(!isset($total_personnel_payments[$payment_id]))
					{
						$total_personnel_payments[$payment_id] = 0;
					}
				}
			}
			if(($total_cash_allowances < 0) OR ($total_cash_allowances > 0))
			{
				$result .= 
				'
						<td>'.number_format($total_cash_allowances, 2).'</td>
						
				';
			}
			else
			{
				$result .= 
				'
						<td>'.number_format(0, 2).'</td>
						
				';
			}
		}
		else
		{
			$result .= 
			'
					<td>'.number_format(0, 2).'</td>
					
			';
		}
		
		//total cash pay - system generated
		$result .= '<td></td>';
		
		//car benefit
		$total_non_cash_benefits = 0;
		if($benefits->num_rows() > 0)
		{
			$benefit_id = 1;
			$table_id = $benefit_id;
			$total_benefit_amount[$benefit_id] = 0;

			if(isset($total_payments2->$benefit_id))
			{
				$total_benefit_amount[$benefit_id] = $total_payments2->$benefit_id;
			}
			if($total_benefit_amount[$benefit_id] != 0)
			{
													
				$benefit_amt = 0;
				if(isset($benefits_amount->$personnel_id->$table_id))
				{
					$benefit_amt = $benefits_amount->$personnel_id->$table_id;
					$total_non_cash_benefits =+ $benefit_amt;
				}
				if(!isset($total_personnel_benefits[$benefit_id]))
				{
					$total_personnel_benefits[$benefit_id] = 0;
				}
				if($benefit_amt > 0)
				{
					$result .= 
					'
							<td>'.number_format($benefit_amt, 2).'</td>
							
					';
				}
				else
				{
					$result .= 
					'
							<td>'.number_format(0, 2).'</td>
							
					';
				}
					
			}
			else
			{
				$result .= 
				'
						<td>'.number_format(0, 2).'</td>
						
				';
			}
		}
		
		//other non cash benefits
		//all benefit ids except car benefits
		if($other_non_cash_benefits->num_rows() > 0)
		{
			foreach($other_non_cash_benefits->result() as $res)
			{
				$benefit_id = $res->benefit_id;
				$table_id = $benefit_id;
				$total_benefit_amount[$benefit_id] = 0;

				if(isset($total_payments2->$benefit_id))
				{
					$total_benefit_amount[$benefit_id] = $total_payments2->$benefit_id;
				}
				if($total_benefit_amount[$benefit_id] != 0)
				{
														
					$benefit_amt = 0;
					if(isset($allowances_amount->$personnel_id->$table_id))
					{
						$benefit_amt = $allowances_amount->$personnel_id->$table_id;
						$total_non_cash_benefits =+ $benefit_amt;
					}
					if(!isset($total_personnel_benefits[$benefit_id]))
					{
						$total_personnel_benefits[$benefit_id] = 0;
					}
					
				}
					
			}
			if($total_non_cash_benefits > 0)
			{
				$result .= 
				'
						<td>'.number_format($total_non_cash_benefits, 2).'</td>
						
				';
			}
			else
			{
				$result .= 
				'
						<td>'.number_format(0, 2).'</td>
						
				';
			}
		}
		else
		{
			$result .= 
			'
					<td>'.number_format(0, 2).'</td>
					
			';
		}
		
		//total non_cash _benefits -kra calculated
		$result .= '<td></td>';
		
		//full time director
		$director =0;
		$result .= '<td>'.number_format($director, 2).'</td>';
		
		//type of housing
		$result .= '<td></td>';
		
		//rent of house-kra calculated
		$house_rent = 0;
		$result .= '<td></td>';
		
		//computed house rent -kra calculated
		$computed_house_rent = 0;
		$result .= '<td></td>';
		
		//rent recovered -kra calculated
		$recovered_rent = 0;
		$result .= '<td></td>';
		
		//house nett value-kra calculated
		$result .= '<td></td>';
		
		//total gross pay-kra calculated
		$result .= '<td></td>';
		
		#$result .= '<td>'.number_format($gross, 2).'</td>';
		$result .= '<td></td>';
		$result .= '<td></td>';
		
		//actual contribution
		$total_nssf = 0;
		$nssf = $nssf_amount->$personnel_id;
		$total_nssf += $nssf;
		$result .= '<td>'.number_format($nssf, 2).'</td>';
		
		//permissible limit -kra calculated
		$result .= '<td></td>';
		
		//mortagge
		$result .= '<td>'.number_format(0, 2).'</td>';
		
		//hospital
		$result .= '<td>'.number_format(0, 2).'</td>';
		
		//amount of benefit- kra calculated
		$result .= '<td></td>';
		
		//taxable pay - kra calculated
		$result .= '<td></td>';
		
		//tax charged - kra calculated
		$result .= '<td></td>';
		
		//insurance relief
		$paye_less_relief  = 0;
		$relief = $monthly_relief_amount->$personnel_id;
		//$insurance_relief = $insurance_amount_amount->$personnel_id;
		$insurance_relief = $insurance_relief_amount->$personnel_id;
		
		$paye_less_relief -= ($relief + $insurance_relief);
		
		$result .= '<td>'.number_format($relief, 2).'</td>';
		$result .= '<td>'.number_format($insurance_relief, 2).'</td>';
		
		//paye - kra calculated
		$result .= '<td></td>';
		
		//self assessed tax -paye
		$paye =$paye_amount->$personnel_id;
		$paye_less_relief = ($relief + $insurance_relief);
						
		if($paye < 0)
		{
			$paye = 0;
		}
		$total_paye += $paye;
		$final_paye = $paye - $paye_less_relief;
		
						
		if($final_paye < 0)
		{
			$final_paye = 0;
		}
		$result .= 
		'
				<td>'.number_format($final_paye, 2).'</td>
		';
		
	
	}
	
	/*$result .= '
			<tr> 
				<td colspan="6"></td>';
	//gross
	$result .= '
			<th>'.number_format($total_gross, 2, '.', ',').'</th>
			<th>'.number_format($total_paye, 2, '.', ',').'</th>
		</tr> 
	';*/
	
	$result .= 
	'
				  </tbody>
				</table>
	';
}

else
{
	$result = "There are no personnel";
}

?>

<!DOCTYPE html>
<html lang="en">
	<style type="text/css">
		.receipt_spacing{letter-spacing:0px; font-size: 12px;}
		.center-align{margin:0 auto; text-align:center;}
		
		.receipt_bottom_border{border-bottom: #888888 medium solid;}
		.row .col-md-12 table {
			border:solid #000 !important;
			border-width:1px 0 0 1px !important;
			font-size:10px;
		}
		.row .col-md-12 th, .row .col-md-12 td {
			border:solid #000 !important;
			border-width:0 1px 1px 0 !important;
		}
		
		.row .col-md-12 .title-item{float:left;width: 130px; font-weight:bold; text-align:right; padding-right: 20px;}
		.title-img{float:left; padding-left:30px;}
		img.logo{max-height:70px; margin:0 auto;}
	</style>
    <head>
        <title>PAYE Report</title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- IE Support -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Bootstrap -->
         <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/stylesheets/theme-custom.css">
		<script type="text/javascript" src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jquery/jquery.js"></script>
		<script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>tableExport.js"></script>
        <script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>jquery.base64.js"></script>
        <script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>html2canvas.js"></script>
        <script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>libs/sprintf.js"></script>
		<script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>jspdf.js"></script>
        <script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>libs/base64.js"></script>
    </head>
    <body class="receipt_spacing">
    	<div class="row" >
        	<img src="<?php echo base_url().'assets/logo/'.$branch_image_name;?>" alt="<?php echo $branch_name;?>" class="img-responsive logo"/>
        	<div class="col-md-12 center-align receipt_bottom_border">
            	<strong>
                	<?php echo $branch_name;?><br/>
                    <?php echo $branch_address;?> <?php echo $branch_post_code;?> <?php echo $branch_city;?><br/>
                    E-mail: <?php echo $branch_email;?>. Tel : <?php echo $branch_phone;?><br/>
                    <?php echo $branch_location;?><br/>
                </strong>
            </div>
        </div>
        
      	<div class="row receipt_bottom_border" >
        	<div class="col-md-12 center-align">
            	<h4><?php echo '<h3>PAYE for The month of '.date('M Y',strtotime($year.'-'.$month)).'</h3>';?></h4>
            </div>
        </div>
        
        <div class="row receipt_bottom_border" >
        	<div class="col-md-12">
            	<?php echo $result;?>
            </div>
        	<div class="col-md-12 center-align">
            	<?php echo 'Prepared By: '.$prepared_by.' '.date('jS M Y H:i:s',strtotime(date('Y-m-d H:i:s')));?>
            </div>
        </div>
    <a href="#" onclick="javascript:xport.toCSV('testTable');">XLS</a>
<!--<a href="#" onClick ="$('#customers').tableExport({type:'csv',escape:'false'});">CSV</a>
<a href="#" onClick ="$('#customers').tableExport({type:'pdf',escape:'false'});">PDF</a>-->

    </body>
</html>
<script type="text/javascript">
	var xport = {
  _fallbacktoCSV: true,  
  toXLS: function(tableId, filename) {   
    this._filename = (typeof filename == 'undefined') ? tableId : filename;
    
    //var ieVersion = this._getMsieVersion();
    //Fallback to CSV for IE & Edge
    if ((this._getMsieVersion() || this._isFirefox()) && this._fallbacktoCSV) {
      return this.toCSV(tableId);
    } else if (this._getMsieVersion() || this._isFirefox()) {
      alert("Not supported browser");
    }

    //Other Browser can download xls
    var htmltable = document.getElementById(tableId);
    var html = htmltable.outerHTML;

    this._downloadAnchor("data:application/vnd.ms-excel" + encodeURIComponent(html), 'xls'); 
  },
  toCSV: function(tableId, filename) {
    this._filename = (typeof filename === 'undefined') ? tableId : filename;
    // Generate our CSV string from out HTML Table
    var csv = this._tableToCSV(document.getElementById(tableId));
    // Create a CSV Blob
    var blob = new Blob([csv], { type: "text/csv" });

    // Determine which approach to take for the download
    if (navigator.msSaveOrOpenBlob) {
      // Works for Internet Explorer and Microsoft Edge
      navigator.msSaveOrOpenBlob(blob, this._filename + ".csv");
    } else {      
      this._downloadAnchor(URL.createObjectURL(blob), 'csv');      
    }
  },
  _getMsieVersion: function() {
    var ua = window.navigator.userAgent;

    var msie = ua.indexOf("MSIE ");
    if (msie > 0) {
      // IE 10 or older => return version number
      return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)), 10);
    }

    var trident = ua.indexOf("Trident/");
    if (trident > 0) {
      // IE 11 => return version number
      var rv = ua.indexOf("rv:");
      return parseInt(ua.substring(rv + 3, ua.indexOf(".", rv)), 10);
    }

    var edge = ua.indexOf("Edge/");
    if (edge > 0) {
      // Edge (IE 12+) => return version number
      return parseInt(ua.substring(edge + 5, ua.indexOf(".", edge)), 10);
    }

    // other browser
    return false;
  },
  _isFirefox: function(){
    if (navigator.userAgent.indexOf("Firefox") > 0) {
      return 1;
    }
    
    return 0;
  },
  _downloadAnchor: function(content, ext) {
      var anchor = document.createElement("a");
      anchor.style = "display:none !important";
      anchor.id = "downloadanchor";
      document.body.appendChild(anchor);

      // If the [download] attribute is supported, try to use it
      
      if ("download" in anchor) {
        anchor.download = this._filename + "." + ext;
      }
      anchor.href = content;
      anchor.click();
      anchor.remove();
  },
  _tableToCSV: function(table) {
    // We'll be co-opting `slice` to create arrays
    var slice = Array.prototype.slice;

    return slice
      .call(table.rows)
      .map(function(row) {
        return slice
          .call(row.cells)
          .map(function(cell) {
            return '"t"'.replace("t", cell.textContent);
          })
          .join(",");
      })
      .join("\r\n");
  }
};

</script>
