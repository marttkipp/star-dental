<?php
 $personnel_id = $this->session->userdata('personnel_id');
 if($branch_details->num_rows() > 0)
 {
	//var_dump($branch_details->result()); die();
 	foreach ($branch_details->result() as $branch) 
	{
 		$branch_name = $branch->branch_name;
 		$branch_kra_pin = $branch->branch_kra_pin;
 	}
 }
		
?>
<!DOCTYPE html>
<html lang="en">
	
    <head>
        <title>P9 Form</title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- IE Support -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/stylesheets/theme-custom.css">
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
			
            .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td{border-top:none; padding:0;}
            .table > tbody.main-data > tr > th, .table > tbody.main-data > tr > td{padding:3px;}
			
			@media print
			{
				#page-break
				{
					page-break-after: always;
					page-break-inside : avoid;
				}
				#numbers-notification
				{
					display: none !important;
				}
			}
		</style>
    </head>
    <body class="receipt_spacing">
    	<input type="hidden" id="total_rows" value="<?php echo $total_rows;?>">
    	<input type="hidden" id="current_row" value="<?php echo $current_row;?>">
    	<div id="string_json" style="display:none;"><?php echo $personnel;?></div>
        <div class="row" style="height:50px;" id="numbers-notification">
        	<div class="col-xs-6">
            	<h5>Total Personnel</h5>
                <p><?php echo $total_rows;?></p>
            </div>
        	<div class="col-xs-6">
            	<h5>Total Displayed</h5>
                <p id="total_displayed"><?php echo $current_row;?></p>
            </div>
        </div>
        <div id="p9_items"></div>
    	
		<script type="text/javascript" src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jquery/jquery.js"></script>
        <script type="text/javascript">
            $( document ).ready(function()
            {
				console.log('error_check');
                var total_rows = parseInt($('#total_rows').val());
                var current_row = parseInt($('#current_row').val());
                var string_json = <?php echo json_encode($personnel);?>;
                var p9_generation_year = '<?php echo $p9_generation_year;?>';
                var branch_id = '<?php echo $branch_id;?>';
                var from_month_id = '<?php echo $from_month_id;?>';
                var to_month_id = '<?php echo $to_month_id;?>';
                var total_displayed = 0;
                var json_data = jQuery.parseJSON(string_json);
                $.each(json_data, function (index, res) 
                {
                    var personnel_id = res.personnel_id;
					var personnel_number = res.personnel_number;
					var personnel_fname = res.personnel_fname;
					var personnel_onames = res.personnel_onames;
					var kra_pin = res. personnel_kra_pin;
                    
                    $.ajax({
                        type:'POST',
                        url: '<?php echo site_url().'accounts/get-p9-data/';?>'+personnel_id,
                        data:{'p9_generation_year':p9_generation_year, 'branch_id':branch_id, 'from_month_id':from_month_id, 'to_month_id':to_month_id, 'current_row':current_row, 'total_rows':total_rows, 'personnel_number':personnel_number, 'personnel_fname':personnel_fname, 'personnel_onames':personnel_onames, 'kra_pin':kra_pin},
                        dataType: 'json',
                        success:function(data)
                        {
                            //alert(data.message);
							var display_data = 
							'<div id="page-break">'+
								'<table class="table">'+
									'<tr>'+
										'<td align="center">'+
											'<img src="<?php echo base_url();?>/assets/logo/kra.jpg" alt="kra.jpg">'+
										'</td>'+
									'</tr>'+
								'</table>'+
								'<table class="table">'+
									'<tr>'+
										'<th align="center">'+
											'<div class="center-align">KENYA REVENUE AUTHORITY</br>'+
											
											'DOMESTIC TAXES DEPARTMENT</br>'+
											
											'TAX DEDUCTION CARD YEAR <?php echo $p9_generation_year;?></br></div>'+
										'</th>'+
									'</tr>'+
								'</table>'+
	
								data.header + 
								
								'<table class="table table-bordered table-striped table-condensed">'+
									'<thead>'+
										'<tr rowspan="2">'+
											'<td>MONTH</td>'+
											'<td>Basic Salary </br> </br>  Kshs.</td>'+
											'<td>Benefits Non Cash </br> </br> Kshs.</td>'+
											'<td>Value of Quarters </br> </br> Kshs.</td>'+
											'<td align="center">Total Gross Pay </br> </br> Kshs.</td>'+
											'<td align="center" colspan="3">Defined Contribution Retirement Scheme </br> </br> Kshs.</td>'+
											'<td align="center">Owner-Occupied Interest </br> </br> Kshs.</td>'+
											'<td align="center">Retirement Contribution & Owner Occupied Interest </br> </br> Kshs.</td>'+
											'<td align="center">Chargeable Pay </br> </br> Kshs.</td>'+
											'<td align="center">Tax Charged </br> </br> Kshs.</td>'+
											'<td align="center">Personal Relief</br> </br>Kshs. </br>1162</td>'+
											'<td align="center">Insurance Relief</br> </br> Kshs.</br>-</td>'+
											'<td align="center">PAYE</br></br> Kshs.</td>'+
										 '</tr>'+
										 '<tr>'+
											'<td></td>'+
											'<td align="center">A</td>'+
											'<td align="center">B</td>'+
											'<td align="center">C</td>'+
											'<td align="center">D</td>'+
											'<td colspan="3" align="center">E</td>'+
											'<td align="center">F</td>'+
											'<td align="center">G</td>'+
											'<td align="center">H</td>'+
											'<td align="center">J</td>'+
											'<td colspan="2" align="center">K</td>'+
											'<td align="center">L</td>'+
										 '</tr>'+
										 '<tr>'+
											'<td colspan="5"></td>'+
											'<td align="center">E1 30 % of A</td>'+
											'<td align="center">E2 Actual</td>'+
											'<td align="center">E3 Fixed</td>'+
											'<td align="center">Amount of Interest</td>'+
											'<td align="center">The lowest of E added to F</td>'+
											'<td></td>'+
											'<td></td>'+
											'<td colspan="2" align="center">Total Kshs.1162</td>'+
											'<td></td>'+
										 '</tr>'+
										 
									'</thead>'+
									
									'<tbody class="main-data">'+
										data.message + 
									'</tbody>'+
								'</table>'+
								'<footer>'+
									'<table class="table">'+
										'<tr>	'+					
											'<td><p><b>To be completed by Employer at end of year</b></p>'+
											'<p><b>TOTAL CHARGEABLE PAY  (COL. H)   Kshs .........................</b></p>   '+
											'<h1 style="font-size:100%;"><b>IMPORTANT</b></h1>'+
											'<p>1.  Use P9A'+
												 '<ol type="a">'+
													'<li> For all liable employees and where director/employee received <br>  Benefits in addition to cash emoluments.</li>'+
													'<li> Where an employee is eligible to deduction on owner occupier interest. </li>'+								
												'</ol> '+
											'</p>'+
											'<p><b>2.  (a)  Allowable  interest in respect of any month must not exceed Kshs. 12,500/= or Kshs. 150,000 per year.</b></p>'+
											'<p><b>(See back of this card for further information required by the Department).</b></p></td>'+
											
											
											
											'<td><p style="font-size:100%;font-color:dark;"><b> TOTAL TAX (COL. L) Kshs. _________________________ </b></p>'+
											'<p>Attach '+
												'<ol type="i">'+
													'<li>Photostat copy of interest certificate and statement of account from the<br> Financial Institution.</li>'+
													'<li>The DECLARATION duly signed by the employee.</li>	'+							
												'</ol> '+
											'</p>'+
											'<h2 style="font-size:100%;"><b>NAMES OF FINANCIAL INSTITUTION ADVANCING MORTGAGE LOAN </b></h2>'+
											'<p>_________________________________________________________</p>'+
											'<p><b>L R NO. OF OWNER OCCUPIED PROPERTY:........................................................................</b></p>'+
											'<p><b>DATE OF OCCUPATION OF HOUSE:................................................................................</b></p></td>'+
										'</tr>'+
									'</table>'+
								'</footer>'+
							'</div>';
                            $('#p9_items').append( display_data );
                            total_displayed = total_displayed + 1;
                            $('#total_displayed').html( total_displayed );
                            var check = current_row + 1;
                            if(current_row == total_rows)
                            {
                                //$('#numbers-notification').css('display', 'none');
                            }
                        },
                        error: function(xhr, status, error) 
                        {
                            alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
                        }
                    });
                    current_row = current_row + 1;
                    //$('#current_row').val(current_row);
                });
                
            });
        </script>
    </body>
</html>
