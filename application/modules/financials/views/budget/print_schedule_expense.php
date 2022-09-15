<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $contacts['company_name'];?> | SCHEDULE OF EXPENSE</title>
        <!-- For mobile content -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- IE Support -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/bootstrap/css/bootstrap.css" media="all"/>
        <link rel="stylesheet" href="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/stylesheets/theme-custom.css" media="all"/>
        <script src="<?php echo base_url()."assets/themes/porto-admin/1.4.1/";?>assets/vendor/jquery/jquery.js"></script>	
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
    	<input type="hidden" id="base_url" value="<?php echo site_url();?>">
    	<input type="hidden" id="config_url" value="<?php echo site_url();?>">
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
       <input type="hidden" name="budget_year" id="budget_year" value="<?php echo $budget_year;?>">
      <div class="row receipt_bottom_border" >
        	<div class="col-md-12 center-align" style="padding: 5px;">
            	<strong>SCHEDULE OF EXPENDITURE</strong><br>

            	<?php
            	
				 echo $title;
            	?>

            </div>
        </div>

    	<div class="row receipt_bottom_border">
        	<div style="margin: auto;max-width: 500px;">
				<div class="col-md-12">	
					<div id="budget-table"></div>
				</div>
            </div>
        </div>

    	<div class="row" style="font-style:italic; font-size:11px;">
        	<div class="col-sm-12">
                <div class="col-sm-10 pull-left">
                    <strong>Prepared by: </strong>
                </div>
                <div class="col-sm-2 pull-right">
                    <?php echo date('jS M Y H:i a'); ?>
                </div>
            </div>

        </div>
            <script type="text/javascript">
	$(function() {
		// var budget_year = <?php echo $budget_year?>;
		// alert("sdasda");
		var budget_year = document.getElementById("budget_year").value;

		// alert(budget_year);
		get_year_budget(budget_year);
		
	});

	function get_year_budget(budget_year)
	{
		var config_url = $('#config_url').val();
	 	var url = config_url+"financials/budget/get_year_budget_summary/"+budget_year;
	 	// alert(url);
		$.ajax({
			type:'POST',
			url: url,
			data:{query: null},
			dataType: 'text',
			processData: false,
			contentType: false,
			success:function(data){
			var data = jQuery.parseJSON(data);
			  // alert(data.content);
			if(data.message == "success")
			{
				$("#budget-table").html(data.result);
			}
			else
			{
				alert('Please ensure you have added included all the items');
			}
			$("#title-div").html('REPORT FOR '+budget_year);

		},
		error: function(xhr, status, error) {
		alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);

		}
		});
	}
	function add_budget_item(budget_year)
	{

		document.getElementById("sidebar-right").style.display = "block"; 
		document.getElementById("existing-sidebar-div").style.display = "none"; 

		var config_url = $('#config_url').val();
		var data_url = config_url+"financials/budget/add_budget_item/"+budget_year;
		//window.alert(data_url);
		$.ajax({
		type:'POST',
		url: data_url,
		data:{appointment_id: 1},
		dataType: 'text',
		success:function(data){
			//window.alert("You have successfully updated the symptoms");
			//obj.innerHTML = XMLHttpRequestObject.responseText;
			document.getElementById("current-sidebar-div").style.display = "block"; 
			$("#current-sidebar-div").html(data);

			$('.datepicker').datepicker({
					    format: 'yyyy-mm-dd'
					});



			$('.timepicker').timepicker({
			    timeFormat: 'h:mm p',
			    interval: 60,
			    minTime: '10',
			    maxTime: '6:00pm',
			    defaultTime: '11',
			    startTime: '10:00',
			    dynamic: false,
			    dropdown: true,
			    scrollbar: true
			});
			// alert(data);
			},
			error: function(xhr, status, error) {
			//alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
			alert(error);
		}

		});
	}
	function close_side_bar()
	{
		// $('html').removeClass('sidebar-right-opened');
		document.getElementById("sidebar-right").style.display = "none"; 
		document.getElementById("current-sidebar-div").style.display = "none"; 
		document.getElementById("existing-sidebar-div").style.display = "none"; 
		tinymce.remove();
	}

	$(document).on("submit","form#confirm-budget-item",function(e)
	{
		e.preventDefault();
		
		var form_data = new FormData(this);

		// alert(form_data);
		var budget_year = $('#budget_year').val();	
		var budget_month = $('#budget_month').val();	
		var account_id = $('#account_id').val();	
		var config_url = $('#config_url').val();

		var url = config_url+"financials/budget/confirm_budget_item/"+budget_year;
		 
		 // alert(url);
	   $.ajax({
	   type:'POST',
	   url: url,
	   data:form_data,
	   dataType: 'text',
	   processData: false,
	   contentType: false,
	   success:function(data){
	      var data = jQuery.parseJSON(data);
	    
	      	if(data.status == "success")
			{
				
				
				// close_side_bar();
				get_budget_items(budget_year,budget_month,account_id);
				get_year_budget(budget_year);


				
			}
			else
			{
				alert(data.message);
			}
	   
	   },
	   error: function(xhr, status, error) {
	   alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	   
	   }
	   });
		 
		
	   
		
	});
	function delete_budget_item(budget_item_id,budget_year,month,account_id)
	{

		var res = confirm('Are you sure you want to delete this entry ?');


		if(res)
		{
			var config_url = $('#config_url').val();
			var data_url = config_url+"financials/budget/delete_budget_item/"+budget_item_id;
			//window.alert(data_url);
			$.ajax({
			type:'POST',
			url: data_url,
			data:{appointment_id: 1},
			dataType: 'text',
			success:function(data){
				

				 get_budget_items(budget_year,month,account_id);
				 get_year_budget(budget_year);
				},
				error: function(xhr, status, error) {
				//alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
				alert(error);
			}

			});
		}
		

	}

	function edit_budget_item(month,account_id,budget_year)
	{
		close_side_bar();
		document.getElementById("sidebar-right").style.display = "block"; 
		document.getElementById("existing-sidebar-div").style.display = "none"; 

		var config_url = $('#config_url').val();
		var data_url = config_url+"financials/budget/add_budget_item/"+budget_year+"/"+month+"/"+account_id;
		//window.alert(data_url);
		$.ajax({
		type:'POST',
		url: data_url,
		data:{appointment_id: 1},
		dataType: 'text',
		success:function(data){
			//window.alert("You have successfully updated the symptoms");
			//obj.innerHTML = XMLHttpRequestObject.responseText;
			document.getElementById("current-sidebar-div").style.display = "block"; 
			$("#current-sidebar-div").html(data);

			 get_budget_items(budget_year,month,account_id);
			},
			error: function(xhr, status, error) {
			//alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
			alert(error);
		}

		});

	}

	function get_budget_items(budget_year,month,account_id)
	{

		var config_url = $('#config_url').val();
	 	var url = config_url+"financials/budget/get_budget_list/"+budget_year+"/"+month+"/"+account_id;
	 	// alert(url);
		$.ajax({
			type:'POST',
			url: url,
			data:{query: null},
			dataType: 'text',
			processData: false,
			contentType: false,
			success:function(data){
			var data = jQuery.parseJSON(data);
			  // alert(data.content);
			if(data.message == "success")
			{
				$("#visit-payment-div").html(data.result);
			}
			else
			{
				alert('Please ensure you have added included all the items');
			}
			// $("#title-div").html('REPORT FOR '+budget_year);

		},
		error: function(xhr, status, error) {
		alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);

		}
		});
	}

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
    </body>


</html>
