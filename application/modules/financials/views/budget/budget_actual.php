<input type="hidden" name="budget_year" id="budget_year" value="<?php echo $budget_year;?>">
<div class="row" style="margin-top: 10px;">
	<div class="col-md-6">
		<?php echo form_open("financials/budget/search_actual_budget", array("class" => "form-horizontal"));?>
		<div class="col-md-6">
			<div class="form-group">
	            <label class="col-md-4 control-label">Year: </label>
	            
	            <div class="col-md-8">
	               <select id="budget_year" name="budget_year" class="form-control">	
		                <?php

		                $start_year = 2019;
				        $end_year  = date('Y') + 1;
				        // $selected = $this->session->session->userdata('budget_year');
				       
				   		for ($i=$start_year; $i <= $end_year; $i++) { 
				   			# code...

							if($i == date("Y"))
							{
	                                echo "<option value=".$i." selected>".$i."</option>";
	                        }
	                        else{
	                            echo "<option value=".$i.">".$i."</option>";
	                        }
				   		}          
		                
	                    ?>
	                 </select> 
	            </div>
	        </div>
		</div>
		<div class="col-md-2">
			<button type="submit" class="btn btn-sm btn-success"> SEARCH</button>
		</div>
		<div class="col-md-2">
			<?php
			$budget_year_searched = $this->session->userdata('acutal_budget_year');

			if(!empty($budget_year_searched))
			{
				?>
				<a href="<?php echo site_url().'financials/budget/close_budget_actual_search'?>" class="btn btn-sm btn-warning"> CLOSE</a>
				<?php
			}

			?>
			
		</div>
		<?php echo form_close();?>
	</div>
	<div class="col-md-3">
		<div id="title-div"></div>
	</div>
	<div class="col-md-3">

		<a href="<?php echo site_url().'company-financials/budget'?>" class="btn btn-sm btn-primary"> Budget</a>		
		<a href="<?php echo site_url().'company-financials/budget-comparison'?>" class="btn btn-sm btn-warning"> Comparison</a>
		<a onclick="javascript:xport.toCSV('testTable');" class="btn btn-sm btn-success"> Export</a>
		<a href="<?php echo site_url().'company-financials'?>" class="btn btn-sm btn-danger"> <i class="fa fa-arrow-left"></i></a>
	</div>
</div>
<div class="row">
	<div class="panel-body">
		<div id="budget-table"></div>
		
		
	</div>
	
</div>
<script type="text/javascript">
	$(function() {
		// var budget_year = <?php echo $budget_year?>;
			var budget_year = document.getElementById("budget_year").value;

		// alert(budget_year);
		get_year_budget(budget_year);
		
	});

	function get_year_budget(budget_year)
	{
		var config_url = $('#config_url').val();
	 	var url = config_url+"financials/budget/get_year_budget_actual/"+budget_year;
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