<?php
$objective_result = '';
if($query->num_rows() > 0)
{
	$count = 0;
	$objective_result .='
			<table class="table table-hover table-bordered table-striped table-responsive col-md-12" id="excel-export">
				<thead>
					<tr>
						<th>#</th>
						<th>Visit Date</th>
						<th>Visit ID</th>
						<th>HC Seen</th>
						<th>Department</th>
						<th>Class</th>
						<th>Objective Findings</th>
					</tr>
				</thead>
				<tbody>
			';
	foreach($query->result() as $visit_symptoms)
	{
		$visit_id = $visit_symptoms->visit_id;
		$objective_findings_name = $visit_symptoms->objective_findings_name;
		$patient_id = $visit_symptoms->patient_id;
		$branch_code = $visit_symptoms->branch_code;
		$department_name = $visit_symptoms->department_name;
		$objective_findings_class_name = $visit_symptoms->objective_findings_class_name;
		$visit_date = date('jS M Y',strtotime($visit_symptoms->visit_date));
		$count++;
		
		//branch Code
		if($branch_code =='OSE')
		{
			$branch_code = 'Main HC';
		}
		else
		{
			$branch_code = 'Oserengoni';
		}
				
		$objective_result .='
					<tr>
						<td>'.$count.'</td>
						<td>'.$visit_date.'</td>
						<td>'.$visit_id.'</td>
						<td>'.$branch_code.'</td>
						<td>'.$department_name.'</td>
						<td>'.$objective_findings_class_name.'</td>
						<td>'.$objective_findings_name.'</td>
					</tr>';
	}
	$objective_result .='
				</tbody>
			</table>';
}
else
{
	$objective_result.='No objevtive findings were added for any of the patients seen';
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Objective Findings</title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- IE Support -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Bootstrap -->
		<link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" />
		<script type="text/javascript" src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jquery/jquery.js"></script>
		<script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>tableExport.js"></script>
        <script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>jquery.base64.js"></script>
        <script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>html2canvas.js"></script>
        <script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>libs/sprintf.js"></script>
		<script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>jspdf.js"></script>
        <script type="text/javascript" src="<?php echo base_url()."assets/jspdf/";?>libs/base64.js"></script>
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
            .table {margin-bottom: 0;}
			@media print
			{
				#page-break
				{
					page-break-after: always;
					page-break-inside : avoid;
				}
				.print-no-display
				{
					display: none !important;
				}
			}
        </style>
    </head>
    <body class="receipt_spacing">
    	
       <div class="row receipt_bottom_border">
            <div class="col-md-12">
                <section class="panel panel-featured panel-featured-info">
                    <header class="panel-heading">
                         <h2 class="panel-title"><?php echo $title;?></h2>
                    </header>             
                    
                    <!-- Widget content -->
                    <div class="panel-body"  onLoad="window.print();return false;">
                        <?php echo $objective_result;?>
                    </div>
					<a href="#" class="print-no-display" onClick ="$('#excel-export').tableExport({type:'excel',escape:'false'});">EXCEL DOWNLOAD</a>
                    
                </section>
            </div>
        </div>

    </body>
</html>