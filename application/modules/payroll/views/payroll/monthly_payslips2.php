<?php
$personnel_id = $this->session->userdata('personnel_id');
$prepared_by = $this->session->userdata('first_name');
//var_dump($payroll_data); die();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Payslips</title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" media="all" />
		
		<style type="text/css">
            .receipt_spacing{letter-spacing:0px; font-size: 12px;}
            .center-align{margin:0 auto; text-align:center;}
            
            .receipt_bottom_border{border-bottom: #888888 medium solid;}
            .row .col-md-12 table {
                
                border-width:1px 0 0 1px !important;
                font-size:10px;
            }
            .table-condensed > thead > tr > th, .table-condensed > tbody > tr > th, .table-condensed > tfoot > tr > th, .table-condensed > thead > tr > td, .table-condensed > tbody > tr > td, .table-condensed > tfoot > tr > td {
        padding: 0px !important;
    }
			@media all{
				display:none;
			}
            @media print{
                
                #page-break{
                    page-break-before:always;
					display:block;
                }
                /*#page-break{
					page-break-after: always !important;
					page-break-inside: avoid !important;
                }*/
				#numbers-notification
				{
					display: none !important;
				}
            }
            .table {
              margin-bottom: 10px;
            }
            .table-condensed > tbody > tr > th {
                font-size: 15px !important;
            }
            .table > tr > th {
                font-size: 15px !important;
            }
            .col-md-12 td {
                border-width: 0 1px 1px 0 !important;
                font-size:13px !important;
            }
            .tr .th {
                font-size:12px !important;
                padding-left: 0px !important;
            }
            
            .row .col-md-12 th, .row .col-md-12 td {	
                border-width:0 1px 1px 0 !important;
            }
            .col-xs-6{
                /*min-height:600px;*/
				margin-top:30px;
            }
            .row .col-md-12 .title-item{float:left;width: 130px; font-weight:bold; text-align:right; padding-right: 20px;}
            .title-img{float:left; padding-left:30px;}
            img.logo{max-height:70px; margin:0 auto;}
            .left-align{text-align:left !important;}
            .right-align{text-align:right !important;}
             
    
            .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td{border-top:none;}
        </style>
    </head>
    <body class="receipt_spacing">
    	<input type="hidden" id="total_rows" value="<?php echo $total_rows;?>">
    	<input type="hidden" id="current_row" value="<?php echo $current_row;?>">
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
		<div id="payroll_items">
        </div>
    </body>
    <script type="text/javascript" src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jquery/jquery.js"></script>
    <script type="text/javascript">
		$( document ).ready(function()
		{
			var total_rows = parseInt($('#total_rows').val());
			var current_row = parseInt($('#current_row').val());
            var string_json = <?php echo json_encode($personnel);?>;
			var branch_name = '<?php echo $branch_name;?>';
			var payroll_data = '<?php echo $payroll_data;?>';
			var payroll_id = '<?php echo $payroll_id;?>';
			var total_displayed = 0;
			var json_data = jQuery.parseJSON(string_json);
			$.each(json_data, function (index, res) 
			{
				var personnel_id = res.personnel_id;
				
				$.ajax({
					type:'POST',
					url: '<?php echo site_url().'payroll/print-monthly-payslips-data/';?>'+personnel_id,
					data:{'branch_name':branch_name, 'payroll_id':payroll_id, 'payroll_data':payroll_data, 'current_row':current_row, 'total_rows':total_rows},
					dataType: 'json',
					success:function(data)
					{
						//alert(data.message);
						$('#payroll_items').append( data.message );
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
</html>