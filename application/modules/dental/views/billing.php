
<div class="row">
	<div class="col-md-7">
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
        	<div class="col-md-6 ">
                <div class="col-md-12" style="margin-bottom: 10px">
                  <div class="form-group">
                  <label class="col-md-2 control-label">Service: </label>
                  	<div class="col-md-10">
                  		
                  			<select id='service_id_item' name='service_charge_id' class='form-control custom-select ' >
	                      <option value=''>None - Please Select a service</option>
	                       <?php echo $services_list;?>
	                    </select>

                  			
	                    
	                    <input type="hidden" name="visit_id_checked" id="visit_id_checked">
                    </div>
                  </div>
                </div>
                <br>
                <input type="hidden" name="provider_id" value="0">
               
                <input data-format="yyyy-MM-dd" type="hidden" data-plugin-datepicker class="form-control" name="visit_date_date" id="visit_date_date" placeholder="Admission Date" value="<?php echo date('Y-m-d');?>">
            </div>
            <div class="col-md-6" >
            	<div class="center-align">
					<button class='btn btn-info btn-sm'  onclick="parse_procedures(<?php echo $visit_id;?>,1);" >Add to Bill</button>
				</div>
            </div>
            <?php
                  		// }

                  		?>
           </div>

			<div id="billing"></div>
		</div>
		<div class="col-md-5">
			<div class="row">
					<div class="col-lg-12 center-align" id="account_balance">
						<span id="patient_balance"></span>
	                	
	               	</div>
	             </div>
			<section class="panel panel-featured panel-featured-info">
				<header class="panel-heading">
					
					<h2 class="panel-title">Waiver</h2>
				</header>
				
                
				<div class="panel-body">
                        <input type="hidden" name="service_id" value="0">
                        <div class="form-group">
							<label class="col-lg-4 control-label">Waiver Amount: </label>
						  
							<div class="col-lg-8">
								<input type="text" class="form-control" name="waiver_amount" id="waiver_amount" placeholder="" autocomplete="off" >
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-4 control-label">Reason: </label>
						  
							<div class="col-lg-8">
								<textarea class="form-control" name="reason" id="reason" placeholder="" autocomplete="off"></textarea>
							</div>
						</div>
						<br/>
						 <div class="row">
				        	 <div class="col-md-12" >
				            	<div class="center-align">
									<button class='btn btn-info btn-sm'  onclick="add_patient_waiver(<?php echo $patient_id;?>,<?php echo $visit_id;?>);" >Add Waiver</button>
								</div>
				            </div>
				        </div>
				        <br/>
				       	<div class="row">
							<div class="col-lg-12 center-align" >
								<span id="patient_waivers"></span>
			                	
			               	</div>
			             </div>

				</div>
			</section>
			<br/>
	    	<div class="row">
				<div class="form-group">
	                <label class="col-md-4 control-label">Lab Work Description: </label>
	                
	                <div class="col-md-8">
	                	<textarea class="form-control" name="lab_work" id="lab_work_done" ></textarea>
	                </div>
	            </div>
	        </div>
	        <br/>
	        <div class="row">
	        	 <div class="col-md-12" >
	            	<div class="center-align">
						<button class='btn btn-info btn-sm'  onclick="pass_lab_work(<?php echo $visit_id;?>,1);" >Add Lab Charge</button>
					</div>
	            </div>
	        </div>

	        <div id="lab-work-done"></div>
                
		</div>
	</div>	
  <script type="text/javascript">
	  $(document).ready(function(){
	  	$("#service_id_item").customselect();
	       display_billing(<?php echo $visit_id?>);
	       display_lab_work(<?php echo $visit_id?>);
	       display_patient_balance(<?php echo $patient_id?>);
	       display_patient_waivers(<?php echo $patient_id?>);
	  });

  	function open_window_billing(visit_id){
	  var config_url = $('#config_url').val();
	  
	  window.open(config_url+"dental/dental_services/"+visit_id,"Popup","height=1200, width=800, , scrollbars=yes, "+ "directories=yes,location=yes,menubar=yes," + "resizable=no status=no,history=no top = 50 left = 100");
	}
	function parse_procedures(visit_id,suck)
    {

      var procedure_id = document.getElementById("service_id_item").value;
       procedures(procedure_id, visit_id, suck);
     
    }

	function procedures(id, v_id, suck){
       
        var XMLHttpRequestObject = false;
            
        if (window.XMLHttpRequest) {
        
            XMLHttpRequestObject = new XMLHttpRequest();
        } 
            
        else if (window.ActiveXObject) {
            XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
        }
        
        var url = "<?php echo site_url();?>nurse/procedure/"+id+"/"+v_id+"/"+suck;
       
         if(XMLHttpRequestObject) {
                    
            XMLHttpRequestObject.open("GET", url);
                    
            XMLHttpRequestObject.onreadystatechange = function(){
                
                if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

                    // document.getElementById("billing").innerHTML=XMLHttpRequestObject.responseText;
                    display_billing(v_id);
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


	 function display_patient_balance(patient_id){

	    var XMLHttpRequestObject = false;
	        
	    if (window.XMLHttpRequest) {
	    
	        XMLHttpRequestObject = new XMLHttpRequest();
	    } 
	        
	    else if (window.ActiveXObject) {
	        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	    
	    var config_url = $('#config_url').val();
	    var url = config_url+"dental/get_patient_balance/"+patient_id;
		// alert(url);
	    if(XMLHttpRequestObject) {
	                
	        XMLHttpRequestObject.open("GET", url);
	                
	        XMLHttpRequestObject.onreadystatechange = function(){
	            
	            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

	                document.getElementById("patient_balance").innerHTML=XMLHttpRequestObject.responseText;
	            }
	        }
	                
	        XMLHttpRequestObject.send(null);
	    }
	}


	function display_patient_waivers(patient_id){

	    var XMLHttpRequestObject = false;
	        
	    if (window.XMLHttpRequest) {
	    
	        XMLHttpRequestObject = new XMLHttpRequest();
	    } 
	        
	    else if (window.ActiveXObject) {
	        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	    
	    var config_url = $('#config_url').val();
	    var url = config_url+"dental/get_patient_waivers/"+patient_id;
		// alert(url);
	    if(XMLHttpRequestObject) {
	                
	        XMLHttpRequestObject.open("GET", url);
	                
	        XMLHttpRequestObject.onreadystatechange = function(){
	            
	            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

	                document.getElementById("patient_waivers").innerHTML=XMLHttpRequestObject.responseText;
	            }
	        }
	                
	        XMLHttpRequestObject.send(null);
	    }
	}
	function add_patient_waiver(patient_id,visit_id)
	{
		 var config_url = document.getElementById("config_url").value;
	     var data_url = config_url+"dental/add_patient_waiver/"+patient_id+"/"+visit_id;
	   
	      var waiver_amount = document.getElementById('waiver_amount').value;
	      var reason = document.getElementById('reason').value;

	     // alert(tooth);
	    $.ajax({
	    type:'POST',
	    url: data_url,
	    data:{waiver_amount: waiver_amount,reason: reason},
	    dataType: 'text',
	    success:function(data){
	     // get_medication(visit_id);
	     document.getElementById('waiver_amount').value = "";
	     document.getElementById('reason').value = "";
	     alert('You have successfully added a waiver to the account');
	    	display_patient_balance(patient_id);
	   		display_patient_waivers(patient_id);

	    //obj.innerHTML = XMLHttpRequestObject.responseText;
	    },
	    error: function(xhr, status, error) {
	    //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	    alert(error);
	    	display_patient_balance(patient_id);
	   		display_patient_waivers(patient_id);
	    }

	    });


	    
	}
	function delete_waiver_work(payment_id,patient_id)
	{
		 var config_url = document.getElementById("config_url").value;
	     var data_url = config_url+"dental/remove_patient_waiver/"+payment_id;
	   
	     
	    $.ajax({
	    type:'POST',
	    url: data_url,
	    data:{payment_id: payment_id},
	    dataType: 'text',
	    success:function(data){
	     // get_medication(visit_id);
	     alert('You have successfully removed a waiver to the account');
	     display_patient_balance(patient_id);
	    display_patient_waivers(patient_id);
	    //obj.innerHTML = XMLHttpRequestObject.responseText;
	    },
	    error: function(xhr, status, error) {
	    //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	    alert(error);
	    display_patient_balance(patient_id);
	    display_patient_waivers(patient_id);
	    }

	    });

		
	}
	
	function display_billing(visit_id){

	    var XMLHttpRequestObject = false;
	        
	    if (window.XMLHttpRequest) {
	    
	        XMLHttpRequestObject = new XMLHttpRequest();
	    } 
	        
	    else if (window.ActiveXObject) {
	        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	    
	    var config_url = $('#config_url').val();
	    var url = config_url+"dental/view_billing/"+visit_id;
		// alert(url);
	    if(XMLHttpRequestObject) {
	                
	        XMLHttpRequestObject.open("GET", url);
	                
	        XMLHttpRequestObject.onreadystatechange = function(){
	            
	            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

	                document.getElementById("billing").innerHTML=XMLHttpRequestObject.responseText;
	            }
	        }
	                
	        XMLHttpRequestObject.send(null);
	    }
	}

	function change_payer(visit_charge_id, service_charge_id, v_id)
	{

		var res = confirm('Do you want to change who is being billed ? ');

		if(res)
		{

			var config_url = document.getElementById("config_url").value;
		    var data_url = config_url+"accounts/change_payer/"+visit_charge_id+"/"+service_charge_id+"/"+v_id;
		   
		      // var tooth = document.getElementById('tooth'+procedure_id).value;
		     // alert(data_url);
		    $.ajax({
		    type:'POST',
		    url: data_url,
		    data:{visit_charge_id: visit_charge_id},
		    dataType: 'text',
		    success:function(data){
		     // get_medication(visit_id);
		         display_billing(v_id);
		     alert('You have successfully updated your billing');
		    //obj.innerHTML = XMLHttpRequestObject.responseText;
		    },
		    error: function(xhr, status, error) {
		    //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
		        display_billing(v_id);
		    	alert(error);
		    }

		    });

		}

	}
	//Calculate procedure total
	function calculatetotal(amount, id, procedure_id, v_id){
	       
	    var units = document.getElementById('units'+id).value;  
	    var billed_amount = document.getElementById('billed_amount'+id).value;  
	   // alert(billed_amount);
	    grand_total(id, units, billed_amount, v_id);

	}
	function grand_total(procedure_id, units, amount, v_id){



		 var config_url = document.getElementById("config_url").value;
	     var data_url = config_url+"accounts/update_service_total/"+procedure_id+"/"+units+"/"+amount+"/"+v_id;
	   
	      // var tooth = document.getElementById('tooth'+procedure_id).value;
	     // alert(data_url);
	    $.ajax({
	    type:'POST',
	    url: data_url,
	    data:{procedure_id: procedure_id},
	    dataType: 'text',
	    success:function(data){
	     // get_medication(visit_id);
	         display_billing(v_id);
	     alert('You have successfully updated your billing');
	    //obj.innerHTML = XMLHttpRequestObject.responseText;
	    },
	    error: function(xhr, status, error) {
	    //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	        display_billing(v_id);
	    alert(error);
	    }

	    });


	

	   //  var XMLHttpRequestObject = false;
	        
	   //  if (window.XMLHttpRequest) {
	    
	   //      XMLHttpRequestObject = new XMLHttpRequest();
	   //  } 
	        
	   //  else if (window.ActiveXObject) {
	   //      XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	   //  }
	   //  var config_url = document.getElementById("config_url").value;

	   //  var url = config_url+"accounts/update_service_total/"+procedure_id+"/"+units+"/"+amount+"/"+v_id;
	   //  // alert(url);
	   //  if(XMLHttpRequestObject) {
	                
	   //      XMLHttpRequestObject.open("GET", url);
	                
	   //      XMLHttpRequestObject.onreadystatechange = function(){
	            
	   //          if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) 
				// {
	   //  			// display_patient_bill(v_id);
	   //  			display_billing(v_id);
	   //          }
	   //      }
	                
	   //      XMLHttpRequestObject.send(null);
	   //  }
	}

	function delete_procedure(id, visit_id){
	    var XMLHttpRequestObject = false;
	        
	    if (window.XMLHttpRequest) {
	    
	        XMLHttpRequestObject = new XMLHttpRequest();
	    } 
	        
	    else if (window.ActiveXObject) {
	        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	     var config_url = document.getElementById("config_url").value;
	    var url = config_url+"nurse/delete_procedure/"+id;
	    
	    if(XMLHttpRequestObject) {
	                
	        XMLHttpRequestObject.open("GET", url);
	                
	        XMLHttpRequestObject.onreadystatechange = function(){
	            
	            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

	                display_billing(visit_id);
	            }
	        }
	                
	        XMLHttpRequestObject.send(null);
	    }
	}


	function delete_lab_work(id, visit_id){
	    var XMLHttpRequestObject = false;
	        
	    if (window.XMLHttpRequest) {
	    
	        XMLHttpRequestObject = new XMLHttpRequest();
	    } 
	        
	    else if (window.ActiveXObject) {
	        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	     var config_url = document.getElementById("config_url").value;
	    var url = config_url+"dental/delete_lab_work/"+id;
	    
	    if(XMLHttpRequestObject) {
	                
	        XMLHttpRequestObject.open("GET", url);
	                
	        XMLHttpRequestObject.onreadystatechange = function(){
	            
	            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

	                display_lab_work(visit_id);
	            }
	        }
	                
	        XMLHttpRequestObject.send(null);
	    }
	}

	function save_other_deductions(visit_id)
	{
		 // start of saving rx
        var config_url = $('#config_url').val();
        var data_url = config_url+"dental/save_other_deductions/"+visit_id;
        // window.alert(data_url);
         var doctor_notes_rx = $('#deductions_and_other_info').val();//document.getElementById("vital"+vital_id).value;
        $.ajax({
        type:'POST',
        url: data_url,
        data:{notes: doctor_notes_rx},
        dataType: 'text',
        success:function(data){
        //obj.innerHTML = XMLHttpRequestObject.responseText;
           window.alert("You have successfully updated the payment information");
        },
        error: function(xhr, status, error) {
        //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
        alert(error);
        }

        });
      // end of saving rx
	}



  </script>