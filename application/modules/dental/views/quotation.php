
<div class="row">
	<div class="col-md-12">
    	<br/>
    	<div class="row">
    		<?php
      		// $sent_to = $this->reception_model->going_to($visit_id);
      		// if($sent_to == "Accounts")
      		// {

      		// }
      		// else
      		// {
      			?>

      		<div class="col-md-7">
	        	<div class="col-md-12 ">
	                <div class="col-md-8">
	                  <div class="form-group">
	                  <label class="col-md-2 control-label">Service: </label>
	                  	<div class="col-md-10">
	                  		
	                  			<select id='service_charge_id_item' name='service_quote_charge_id' class='form-control custom-select ' >
		                      <option value=''>None - Please Select a service</option>
		                       <?php echo $services_list;?>
		                    </select>

	                  			
		                    
		                    <input type="hidden" name="visit_id_checked" id="visit_id_checked">
	                    </div>
	                  </div>
	                </div>
	                <div class="col-md-4" >
	                	<input type="hidden" name="provider_id" value="0">
	               
		                <input data-format="yyyy-MM-dd" type="hidden" data-plugin-datepicker class="form-control" name="visit_date_date" id="visit_date_date" placeholder="Admission Date" value="<?php echo date('Y-m-d');?>">

		                <div class="center-align">
							<button class='btn btn-info btn-sm'  onclick="parse_procedures_quote(<?php echo $visit_id;?>,1);" >Add to Quote</button>
						</div>

	                </div>
	                <br>
	                <div class="col-md-12" >
	                	<p style="text-decoration: underline; margin-top: 30px;"><strong>Today's Quote 
	                		<a href="<?php echo site_url().'print-quotation/'.$visit_id?>" target="_blank" class="btn btn-xs btn-warning pull-right" > Print Quote</a> </strong></p>

	           			<div id="quotation_div"></div>
	                </div>
					
	            </div>
	        </div>
            <div class="col-md-5" >

            	<p style="text-decoration: underline;"><strong>Patients Quotes</strong> </p>
	           	<div id="patients_quote_div"></div>
            	
            </div>
            
           </div>

			
		</div>
		
	</div>	
  <script type="text/javascript">
	  $(document).ready(function(){
	  	$("#service_charge_id_item").customselect();
	       display_quotation(<?php echo $visit_id?>);
	       display_quotation_list(<?php echo $visit_id?>);
	  });

  	function open_window_billing(visit_id){
	  var config_url = $('#config_url').val();
	  
	  window.open(config_url+"dental/dental_services/"+visit_id,"Popup","height=1200, width=800, , scrollbars=yes, "+ "directories=yes,location=yes,menubar=yes," + "resizable=no status=no,history=no top = 50 left = 100");
	}
	function parse_procedures_quote(visit_id,suck)
    {

      var procedure_id = document.getElementById("service_charge_id_item").value;
       quote_procedures(procedure_id, visit_id, suck);
     
    }

	function quote_procedures(id, v_id, suck){
       
        var XMLHttpRequestObject = false;
            
        if (window.XMLHttpRequest) {
        
            XMLHttpRequestObject = new XMLHttpRequest();
        } 
            
        else if (window.ActiveXObject) {
            XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
        }
        
        var url = "<?php echo site_url();?>nurse/quote/"+id+"/"+v_id+"/"+suck;
       
         if(XMLHttpRequestObject) {
                    
            XMLHttpRequestObject.open("GET", url);
                    
            XMLHttpRequestObject.onreadystatechange = function(){
                
                if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

                    // document.getElementById("billing").innerHTML=XMLHttpRequestObject.responseText;
                    display_quotation(v_id);
                    display_quotation_list(v_id);
                }
            }
                    
            XMLHttpRequestObject.send(null);
        }

    }

    function pass_lab_work(visit_id,suck)
    {

      var lab_work_done = document.getElementById("lab_work_done").value;
       lab_work_request(lab_work_done, visit_id, suck);
     
    }

    function lab_work_request(lab_work_done, v_id, suck){

    	var config_url = $('#config_url').val();
        var data_url = config_url+"dental/save_lab_work/"+v_id;
        //window.alert(data_url);
        var doctor_notes_rx = lab_work_done; 
        //$('#deductions_and_other').val();//document.getElementById("vital"+vital_id).value;
        $.ajax({
        type:'POST',
        url: data_url,
        data:{notes: doctor_notes_rx},
        dataType: 'json',
        success:function(data){
        //obj.innerHTML = XMLHttpRequestObject.responseText;

           window.alert(data.message);
           display_lab_work(v_id);  
        },
        error: function(xhr, status, error) {
        //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
        	alert(error);
        	display_lab_work(v_id);  
        }

        });
  

    }

    function display_lab_work(visit_id){

	    var XMLHttpRequestObject = false;
	        
	    if (window.XMLHttpRequest) {
	    
	        XMLHttpRequestObject = new XMLHttpRequest();
	    } 
	        
	    else if (window.ActiveXObject) {
	        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	    
	    var config_url = $('#config_url').val();
	    var url = config_url+"dental/view_lab_work/"+visit_id;
		// alert(url);
	    if(XMLHttpRequestObject) {
	                
	        XMLHttpRequestObject.open("GET", url);
	                
	        XMLHttpRequestObject.onreadystatechange = function(){
	            
	            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

	                document.getElementById("lab-work-done").innerHTML=XMLHttpRequestObject.responseText;
	            }
	        }
	                
	        XMLHttpRequestObject.send(null);
	    }
	}


	
	
	function display_quotation(visit_id){

	    var XMLHttpRequestObject = false;
	        
	    if (window.XMLHttpRequest) {
	    
	        XMLHttpRequestObject = new XMLHttpRequest();
	    } 
	        
	    else if (window.ActiveXObject) {
	        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	    
	    var config_url = $('#config_url').val();
	    var url = config_url+"dental/view_quotation/"+visit_id;
		// alert(url);
	    if(XMLHttpRequestObject) {
	                
	        XMLHttpRequestObject.open("GET", url);
	                
	        XMLHttpRequestObject.onreadystatechange = function(){
	            
	            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

	                document.getElementById("quotation_div").innerHTML=XMLHttpRequestObject.responseText;
	            }
	        }
	                
	        XMLHttpRequestObject.send(null);
	    }
	}

	
	//Calculate procedure total
	function calculatetotalquotation(amount, id, procedure_id, v_id){
	       
	    var units = document.getElementById('quote_units'+id).value;  
	    var billed_amount = document.getElementById('quote_amount'+id).value;  
	   // alert(billed_amount);
	    grand_total_quotation(id, units, billed_amount, v_id);

	}
	function grand_total_quotation(procedure_id, units, amount, v_id){



		 var config_url = document.getElementById("config_url").value;
	     var data_url = config_url+"accounts/update_quotation_total/"+procedure_id+"/"+units+"/"+amount+"/"+v_id;
	   
	      // var tooth = document.getElementById('tooth'+procedure_id).value;
	     // alert(data_url);
	    $.ajax({
	    type:'POST',
	    url: data_url,
	    data:{procedure_id: procedure_id},
	    dataType: 'text',
	    success:function(data){
	     // get_medication(visit_id);
	         display_quotation(v_id);
	         display_quotation_list(v_id);
	     alert('You have successfully updated your billing');
	    //obj.innerHTML = XMLHttpRequestObject.responseText;
	    },
	    error: function(xhr, status, error) {
	    //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	        display_quotation(v_id);
	        display_quotation_list(v_id);
	    	alert(error);
	    }

	    });


	}

	function delete_quote(id, visit_id){
	    var XMLHttpRequestObject = false;
	        
	    if (window.XMLHttpRequest) {
	    
	        XMLHttpRequestObject = new XMLHttpRequest();
	    } 
	        
	    else if (window.ActiveXObject) {
	        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	     var config_url = document.getElementById("config_url").value;
	    var url = config_url+"nurse/delete_quote/"+id;
	    
	    if(XMLHttpRequestObject) {
	                
	        XMLHttpRequestObject.open("GET", url);
	                
	        XMLHttpRequestObject.onreadystatechange = function(){
	            
	            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

	                display_quotation(visit_id);
	                display_quotation_list(visit_id);
	            }
	        }
	                
	        XMLHttpRequestObject.send(null);
	    }
	}

	function display_quotation_list(visit_id)
	{

	    var XMLHttpRequestObject = false;
	        
	    if (window.XMLHttpRequest) {
	    
	        XMLHttpRequestObject = new XMLHttpRequest();
	    } 
	        
	    else if (window.ActiveXObject) {
	        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	    
	    var config_url = $('#config_url').val();
	    var url = config_url+"dental/view_patients_quotation/"+visit_id;
		// alert(url);
	    if(XMLHttpRequestObject) {
	                
	        XMLHttpRequestObject.open("GET", url);
	                
	        XMLHttpRequestObject.onreadystatechange = function(){
	            
	            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

	                document.getElementById("patients_quote_div").innerHTML=XMLHttpRequestObject.responseText;
	            }
	        }
	                
	        XMLHttpRequestObject.send(null);
	    }
	}

  </script>