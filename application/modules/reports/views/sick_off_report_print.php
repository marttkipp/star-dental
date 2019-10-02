<?php


$search_title = $this->session->userdata('sick_off_title_search');
if(!empty($search_title))
{
	$title_ext = $search_title;
}
else
{
	$title_ext = 'Sick Off Report for '.date('Y-m-d');
}

$result = '';
//if users exist display them
if ($query->num_rows() > 0)
{
	$count = 0;
	
	$result .= 
		'
			<table class="table table-hover table-bordered table-striped table-responsive col-md-12">
			  <thead>
				<tr>
				  <th>#</th>
				  <th>Patient No. </th>
				  <th>Name </th>
				  <th>Employee No.</th>
				  <th>From Date </th>
				  <th>To Date </th>
				  <th>Days</th>
				  <th>Dept Name</th>
				  <th>Type of Visit</th>
				  <th>Created by</th>
				  </tr>
			  </thead>
			  <tbody>
				  
		';
		

	
	$personnel_query = $this->personnel_model->get_all_personnel();
	
	foreach ($query->result() as $row)
	{
		$total_invoiced = 0;
		$from_date = date('jS M Y',strtotime($row->start_date));
		$to_date = date('jS M Y',strtotime($row->end_date));
		$department_name = $row->department_name;
		$no_of_days = $row->no_of_days;
		$patient_id = $row->patient_id;
		$patient_number = $row->patient_number;
		$strath_no = $row->strath_no;
		$personnel_id = $row->personnel_id;
		$gender_id = $row->gender_id;
		$patient_othernames = $row->patient_othernames;
		$patient_surname = $row->patient_surname;
		$patient_date_of_birth = $row->patient_date_of_birth;
		$last_visit = $row->last_visit;

		if($last_visit != NULL)
		{
			$last_visit = 'Re Visit';
		}
		
		else
		{
			$last_visit = 'First Visit';
		}

		if($gender_id == 1)
		{
			$gender = 'Male';
		}
		else
		{
			$gender = 'Female';
		}

		

		//creators and editors
		if($personnel_query->num_rows() > 0)
		{
			$personnel_result = $personnel_query->result();
			
			foreach($personnel_result as $adm)
			{
				$personnel_id2 = $adm->personnel_id;
				
				if($personnel_id == $personnel_id2)
				{
					$doctor = $adm->personnel_onames.' '.$adm->personnel_fname;
					break;
				}
				
				else
				{
					$doctor = '-';
				}
			}
		}
		
		else
		{
			$doctor = '-';
		}
		
		$count++;
		
		
			$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$patient_number.'</td>
						<td>'.$patient_surname.' '.$patient_othernames.'</td>
						<td>'.$strath_no.'</td>
						<td>'.$from_date.'</td>
						<td>'.$to_date.'</td>
						<td>'.$no_of_days.'</td>
						<td>'.$department_name.'</td>
						<td>'.$last_visit.'</td>
						<td>'.$doctor.'</td>
						

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
	$result .= "There are no sick off's booked today";
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $contacts['company_name'];?> | Visit Report | <?php echo $title_ext;?></title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- IE Support -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" media="all"/>
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/stylesheets/theme-custom.css" media="all"/>
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
			.table thead > tr > th, .table tbody > tr > th, .table tfoot > tr > th, .table thead > tr > td, .table tbody > tr > td, .table tfoot > tr > td
			{
				 padding: 2px;
			}
			
			.row .col-md-12 .title-item{float:left;width: 130px; font-weight:bold; text-align:right; padding-right: 20px;}
			.title-img{float:left; padding-left:30px;}
			img.logo{max-height:70px; margin:0 auto;}
		</style>
    </head>
    <body class="receipt_spacing">
    	<div class="row">
        	<div class="col-xs-12">
            	<img src="<?php echo base_url().'assets/logo/'.$contacts['logo'];?>" alt="<?php echo $contacts['company_name'];?>" class="img-responsive logo"/>
            </div>
        </div>
    	<div class="row">
        	<div class="col-md-12 center-align receipt_bottom_border">
            	<strong>
                	<?php echo $contacts['company_name'];?><br/>
                    P.O. Box <?php echo $contacts['address'];?> <?php echo $contacts['post_code'];?>, <?php echo $contacts['city'];?><br/>
                    E-mail: <?php echo $contacts['email'];?>. Tel : <?php echo $contacts['phone'];?><br/>
                    <?php echo $contacts['location'];?>, <?php echo $contacts['building'];?>, <?php echo $contacts['floor'];?><br/>
                </strong>
            </div>
        </div>
        
      <div class="row receipt_bottom_border" >
        	<div class="col-md-12 center-align">
            	<strong>SICK OFF REPORT (<?php echo $title_ext;?>)</strong>
            </div>
        </div>
        <div class="row receipt_bottom_border" style="margin-bottom: 10px;">
        	<div class="col-md-12">
        		<?php echo $result;?>
        	</div>
        </div>
    </body>
    
</html>