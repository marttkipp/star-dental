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
        <title>P9 Form</title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- IE Support -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/stylesheets/theme-custom.css">
    </head>
    <body class="receipt_spacing">
        <div>
            <div align="center">
            	<strong>
            	<img src="<?php echo base_url();?>assets/logo/kra.jpg" alt="kra.jpg"><br>           	
                KENYA REVENUE AUTHORITY </br>
                DOMESTIC TAXES DEPARTMENT </br>
                TAX DEDUCTION CARD YEAR <?php echo $p9_generation_year;?> </br>
                </strong>
            </div>
            </br>
            <div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label style="font-size:150%;" class="col-md-5"><b>P.9</b></label>                        
                    </div>
                </div>

				<div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="col-md-5">Employer's Name:</label>
							<div class="col-md-7">
							<?php echo $branch_name;?><!-- .............................................. -->
							</div>
						</div><br>
						<!--  
						<div class="form-group">
							<label class="col-md-5">Employer's Name:</label>
							<div class="col-md-7">
							..............................................
							</div>
						 </div> -->
						
						<div class="form-group">
								<label class="col-md-5">Employee's Name:</label>
								<div class="col-md-7">
									<?php echo $personnel_fname;?><!-- .............................................. -->
								</div>
						</div><br>
						<div class="form-group">
								<label class="col-md-5">Employee's Other Name:</label>
								<div class="col-md-5">
									<?php echo $personnel_onames;?><!-- .............................................. -->
								</div>
						</div><br>
					</div>
					<br>
				   
					<div class="col-md-6">
						 <div class="form-group">
							<label class="col-md-5">Employer's Pin:</label>
							<div class="col-md-7">
								<?php echo $branch_kra_pin;?>
							</div>
						</div>
						</br>
						 <div class="form-group">
							<label class="col-md-5">Employee's Pin:</label>
							<div class="col-md-7">
								<?php echo $kra_pin;?>
							</div>
						</div>
						</br>
					</div>
				</div>
        	<table class="table table-bordered table-striped table-condensed">
            	<thead>
                	<tr rowspan="2">
                    	<td>MONTH</td>
                        <td>Basic Salary </br> </br>  Kshs.</td>
                        <td>Benefits Non Cash </br> </br> Kshs.</td>
                        <td>Value of Quarters </br> </br> Kshs.</td>
                        <td align="center">Total Gross Pay </br> </br> Kshs.</td>
                        <td align="center" colspan="3">Defined Contribution Retirement Scheme </br> </br> Kshs.</td>
                        <td align="center">Owner-Occupied Interest </br> </br> Kshs.</td>
                        <td align="center">Retirement Contribution & Owner Occupied Interest </br> </br> Kshs.</td>
                        <td align="center">Chargeable Pay </br> </br> Kshs.</td>
                        <td align="center">Tax Charged </br> </br> Kshs.</td>
                        <td align="center">Personal Relief</br> </br>Kshs. </br>1162</td>
                        <td align="center">Insurance Relief</br> </br> Kshs.</br>-</td>
                        <td align="center">PAYE</br></br> Kshs.</td>
                     </tr>
                     <tr>
                    	<td></td>
                        <td align="center">A</td>
                        <td align="center">B</td>
                        <td align="center">C</td>
                        <td align="center">D</td>
                        <td colspan="3" align="center">E</td>
                        <td align="center">F</td>
                        <td align="center">G</td>
                        <td align="center">H</td>
                        <td align="center">J</td>
                        <td colspan="2" align="center">K</td>
                        <td align="center">L</td>
                     </tr>
                     <tr>
                    	<td colspan="5"></td>
                        <td align="center">E1 30 % of A</td>
                        <td align="center">E2 Actual</td>
                        <td align="center">E3 Fixed</td>
                        <td align="center">Amount of Interest</td>
                        <td align="center">The lowest of E added to F</td>
                        <td></td>
                        <td></td>
                        <td colspan="2" align="center">Total
                        Kshs.1162</td>
                        <td></td>
                     </tr>
                     
                </thead>
                
                <tbody>
                <?php
                	echo $result;
				?>
               	</tbody>
							</table>
			</div>
						<!--footer -->
			<footer>
				<div class="col-md-12">
					<div class="col-md-7">						
						<p><b>To be completed by Employer at end of year</b></p>
						<p><b>TOTAL CHARGEABLE PAY  (COL. H)   Kshs .........................</b></p>   
						<h1 style="font-size:100%;"><b>IMPORTANT</b></h1>
						<p>1.  Use P9A
						     <ol type="a">
								<li> For all liable employees and where director/employee received <br>  Benefits in addition to cash emoluments.</li>
								<li> Where an employee is eligible to deduction on owner occupier interest. </li>								
							</ol> 
						</p>
						<p><b>2.  (a)  Allowable  interest in respect of any month must not exceed Kshs. 12,500/= or Kshs. 150,000 per year.</b></p>
						<p><b>(See back of this card for further information required by the Department).</b></p>
					</div>
					<div class="col-md-5">
						<p style="font-size:100%;font-color:dark;"><b> TOTAL TAX (COL. L) Kshs. _________________________ </b></p>
						<p>Attach 
							<ol type="i">
								<li>Photostat copy of interest certificate and statement of account from the<br> Financial Institution.</li>
								<li>The DECLARATION duly signed by the employee.</li>								
							</ol> 
						</p>
						<h2 style="font-size:100%;"><b>NAMES OF FINANCIAL INSTITUTION ADVANCING MORTGAGE LOAN </b></h2>
						<p>_________________________________________________________</p>
						<p><b>L R NO. OF OWNER OCCUPIED PROPERTY:........................................................................</b></p>
						<p><b>DATE OF OCCUPATION OF HOUSE:................................................................................</b></p>
					</div>
				</div>
			</footer>
			<!--footer end-->
					</body>
				</html>
