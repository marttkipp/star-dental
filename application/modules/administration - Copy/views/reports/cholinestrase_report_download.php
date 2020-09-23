<?php
$result = $format_header = '';
if($lab_test_formats->num_rows() > 0)
{
	foreach($lab_test_formats->result() as $row)
	{
		$lab_test_format_name = $row->lab_test_formatname;
		
		if($lab_test_format_name != '% Change')
		{
			$format_header .= '<th>'.$lab_test_format_name.'</th>';
		}
	}
}
if($query->num_rows() > 0)
{
	$count = 0;
	$result .= 
		'
			<table class="table table-hover table-bordered table-striped table-responsive col-md-12" id="customers">
				<thead>
					<tr>
						<th>#</th>
						<th>Visit Code</th>
						<th>Payroll No</th>
						<th>Visit Date</th>
						<th>Patient Names</th>
						<th>ID No</th>
						<th>Department</th>
						<th>Company</th>
						'.$format_header.'
						<th>% Change</th>
					</tr>
				</thead>
				<tbody>
		';
	foreach($query->result() as $cholinestrase_results)
	{
		$patient_surname = $cholinestrase_results->patient_surname;
		$patient_othernames = $cholinestrase_results->patient_othernames;
		$patient_name = $patient_surname.' '.$patient_othernames;
		$patient_national_id = $cholinestrase_results->patient_national_id;
		$payroll_no = $cholinestrase_results->strath_no;
		$visit_id = $cholinestrase_results->visit_id;
		$department_name  =$cholinestrase_results->department_name;
		$visit_type_name = $cholinestrase_results->visit_type_name;
		$visit_date = date('jS M Y',strtotime($cholinestrase_results->visit_date));
		$test_results = $this->reports_model->get_cholinestrase_results($visit_id);
		$count ++;
		$format_result = '';
		$result .= 
		'
			<tr>
				<td>'.$count.'</td>
				<td>'.$visit_id.'</td>
				<td>'.$payroll_no.'</td>
				<td>'.$visit_date.'</td>
				<td>'.$patient_name.'</td>
				<td>'.$patient_national_id.'</td>
				<td>'.$department_name.'</td>
				<td>'.$visit_type_name.'</td>
		';
		$per_change = $base_line_calc = $current_calc = 0;
		
		if($lab_test_formats->num_rows() > 0)
		{
			foreach($lab_test_formats->result() as $row)
			{
				$lab_test_format_id = $row->lab_test_format_id;
				$lab_test_format_name = $row->lab_test_formatname;
				
				if($lab_test_format_name != '% Change')
				{
					$format_check = 0;
					
					if($test_results->num_rows() > 0)
					{
						foreach($test_results->result() as $res)
						{
							//var_dump($test_results->result()); die();
							$lab_visit_result_format = $res->lab_visit_result_format;
							$lab_visit_results_result = $res->lab_visit_results_result;
							$visit_id_result = $res->visit_id;
							
							if(($visit_id == $visit_id_result) && ($lab_test_format_id == $lab_visit_result_format))
							{
								if($lab_test_format_name == 'Base Line')
								{
									$base_line_calc = $lab_visit_results_result;
								}
								if($lab_test_format_name == 'Current')
								{
									$current_calc = $lab_visit_results_result;
								}
								$format_result .= '<td>'.$lab_visit_results_result.'</td>';
								$format_check = 1;
							}
						}
					}
					if($format_check == 0)
					{
						$format_result .= '<td></td>';
					}
				}
			}
		}
			
		//calculate % change
		$baseleine = $base_line_calc;
		$current = $current_calc;
		
		if($baseleine > 0)
		{
			$per_change = (($current - $baseleine)/$baseleine)* 100;
		}
		if($per_change <= -50)
		{
			$class = 'danger';
		}
		else
		{
			$class = '';
		}
		$format_result .= '<td class="'.$class.'">'.number_format($per_change, 2).'</td>';
		
		$result .= $format_result.'</tr>';
	}
	
	$result .= 
		'
				</tbody>
			</table>
		';
}
else
{
	$result .= 'No tests have been done';
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Download Cholinestrase Tests</title>
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
                        <?php echo $result;?>
                    </div>
					<a href="#" class="print-no-display" onClick ="$('#excel-export').tableExport({type:'excel',escape:'false'});">EXCEL DOWNLOAD</a>
                    
                </section>
            </div>
        </div>

    </body>
</html>
            