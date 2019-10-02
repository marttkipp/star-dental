<?php
		$result = '';
		
		//if advances exist exist display them
		if ($salary_advance_query->num_rows() > 0)
		{
			$count = $page; 
		
			$result .= 
			'
			<table class="table table-bordered table-striped table-condensed" id="customers">
				<thead>
					<tr>
						<th>#</th>
						<th>Payroll Number</th>
						<th>Branch Code</th>
						<th>Account Number</th>
						<th>Salary Advance Amount</th>
					</tr>
				</thead>
				<tbody>
				  
			';

			foreach($salary_advance_query->result() as $advance_details)
			{
				$payroll_number = $advance_details->personnel_number;
				$account_number = $advance_details->bank_account_number;
				$advance_amount = $advance_details->advance_amount;
				$bank_branch_id = $advance_details->bank_branch_id;
				if(!empty($bank_branch_id))
				{
					$bank_code = $this->salary_advance_model->get_branch_code($bank_branch_id);
				}
				else 
				{
					$bank_code = '';
				}
				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$payroll_number.'</td>
						<td>'.$bank_code.'</td>
						<td>'.$account_number.'</td>
						<td>'.$advance_amount.'</td>
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
			$result .= "There are no advances made";
		}
?>
	<div class="panel-body">
		<?php
		$success = $this->session->userdata('success_message');

		if(!empty($success))
		{
			echo '<div class="alert alert-success"> <strong>Success!</strong> '.$success.' </div>';
			$this->session->unset_userdata('success_message');
		}
		
		$error = $this->session->userdata('error_message');
		
		if(!empty($error))
		{
			echo '<div class="alert alert-danger"> <strong>Oh snap!</strong> '.$error.' </div>';
			$this->session->unset_userdata('error_message');
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
        <title>Salary Advance</title>
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
		<div class ="row">
		</div>
		<div class="table-responsive">
			
			<?php echo $result;?>

		</div>   
		<a href="#" onClick ="$('#customers').tableExport({type:'excel',escape:'false'});">XLS</a>
		<!--<a href="#" onClick a="$('#customers').tableExport({type:'csv',escape:'false'});">CSV</a>
		<a href="#" onClick ="$('#customers').tableExport({type:'pdf',escape:'false'});">PDF</a>-->

    </body>
</html>

	</div>
					