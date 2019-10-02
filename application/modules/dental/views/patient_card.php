<?php
// get visit details


$visit_rs = $this->reception_model->get_visit_details($visit_id);

foreach ($visit_rs as $key) {
	# code...

	$time_end=$key->time_end;
	$time_start=$key->time_start;
	$visit_date=$key->visit_date;
	$personnel_id=$key->personnel_id;
	$room_id=$key->room_id;
	$patient_id=$key->patient_id;
}

?>
 <section class="panel">
	<header class="panel-heading">
		<h2 class="panel-title"><?php echo $patient;?></h2>
		<div class="pull-right">
			 <a href="<?php echo site_url().'queue'?>" style="margin-top:-50px" class="btn btn-large btn-info fa fa-arrow-left" > Back to Queue </a>
		</div>
	</header>
	<div class="panel-body">
    
		<div class="center-align">
			<?php
				$error = $this->session->userdata('error_message');
				$validation_error = validation_errors();
				$success = $this->session->userdata('success_message');
				
				if(!empty($error))
				{
					echo '<div class="alert alert-danger">'.$error.'</div>';
					$this->session->unset_userdata('error_message');
				}
				
				if(!empty($validation_error))
				{
					echo '<div class="alert alert-danger">'.$validation_error.'</div>';
				}
				
				if(!empty($success))
				{
					echo '<div class="alert alert-success">'.$success.'</div>';
					$this->session->unset_userdata('success_message');
				}
			?>
		</div>
		
		<?php echo $this->load->view("nurse/allergies_brief", '', TRUE);?>
			
		<div class="clearfix"></div>
		<input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id;?>">
		 <input type="hidden" name="current_date" id="current_date" value="<?php echo $visit_date;?>">
          <input type="hidden" name="visit_id" id="visit_id" value="<?php echo $visit_id;?>">
		<div class="tabbable" style="margin-bottom: 18px;">
			<ul class="nav nav-tabs nav-justified">
				<li class="active" ><a href="#patient-history" data-toggle="tab">Patient card history</a></li>
				<li><a href="#prescription" data-toggle="tab">Prescription</a></li>
				<li><a href="#history-patients" data-toggle="tab">Sick Leave</a></li>
				<li><a href="#diary" data-toggle="tab">Patient's Appointments</a></li>
				<li><a href="#uploads" data-toggle="tab">Uploads</a></li>

				<li><a href="#billing-form" data-toggle="tab">Visit Billing</a></li>
				<li><a href="#patient_details" data-toggle="tab">Patient Details</a></li>
				<li><a href="#visit_trail" data-toggle="tab">Visit Trail</a></li>
			</ul>
			<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
				
				<div class="tab-pane active" id="patient-history">
					<?php echo $this->load->view("patient_history", '', TRUE);?>
				</div>
				<div class="tab-pane " id="dentine">
					<div id="page_item"></div>
				</div>
				<div class="tab-pane " id="prescription">

					<?php echo $this->load->view("prescription_view", '', TRUE); ?>
					<div id="visit-prescription"></div>
				</div>
				<div class="tab-pane " id="history-patients">
					<?php echo $this->load->view("sick_leave", '', TRUE); //echo $this->load->view("nurse/patients/lifestyle", '', TRUE);?>
				</div>
				<div class="tab-pane " id="uploads">
					<?php echo $this->load->view("uploads", '', TRUE);?>
				</div>
				<div class="tab-pane " id="diary">
					<?php echo $this->load->view("patient_appointments", '', TRUE);?>
				</div>
				<div class="tab-pane" id="patient_details">

					<?php 
					$v_data['patient_details'] = $patient_details;
					echo $this->load->view("patient_details", '', TRUE);?>
				</div>
				<div class="tab-pane" id="billing-form">

					<?php echo $this->load->view("billing", '', TRUE);?>
				</div>
				<div class="tab-pane" id="visit_trail">
					<?php echo $this->load->view("visit_trail", '', TRUE);?>
				</div>

			</div>
		</div>
				  


		<div class="row">
			<div class="center-align"> 
			 
				<div class="col-md-12">
				  <div class="center-align">
					<?php echo form_open("dental/send_to_accounts/".$visit_id, array("class" => "form-horizontal"));?>
					  <input type="submit" class="btn btn-large btn-danger center-align" value="Send To Accounts"/>
					<?php echo form_close();?>
				  </div>
				</div>
			</div>

		</div>
		
	</div>
        
  </section>
  
  <script type="text/javascript">
  	
	var config_url = $("#config_url").val();
		
	$(document).ready(function(){
		

	  	$.get( config_url+"nurse/get_family_history/<?php echo $visit_id;?>", function( data ) {
			$("#new-nav").html(data);
			$("#checkup_history").html(data);
		});

		get_medication(<?php echo $visit_id;?>);
		prescription_view();

		get_surgeries(<?php echo $visit_id;?>);
		get_page_item(1,<?php echo $patient_id;?>);



	});

	function get_medication(visit_id){
    
	    var XMLHttpRequestObject = false;
	        
	    if (window.XMLHttpRequest) {
	    
	        XMLHttpRequestObject = new XMLHttpRequest();
	    } 
	        
	    else if (window.ActiveXObject) {
	        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	    }

	    var config_url = document.getElementById("config_url").value;
	    var url = config_url+"nurse/load_medication/"+visit_id;

	    if(XMLHttpRequestObject) {
	        
	        var obj = document.getElementById("medication");
	                
	        XMLHttpRequestObject.open("GET", url);
	                
	        XMLHttpRequestObject.onreadystatechange = function(){
	            
	            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

	                obj.innerHTML = XMLHttpRequestObject.responseText;
	            }
	        }
	                
	        XMLHttpRequestObject.send(null);
	    }
	}
	function get_surgeries(visit_id){
    
	    var XMLHttpRequestObject = false;
	        
	    if (window.XMLHttpRequest) {
	    
	        XMLHttpRequestObject = new XMLHttpRequest();
	    } 
	        
	    else if (window.ActiveXObject) {
	        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	    var config_url = document.getElementById("config_url").value;
	    var url = config_url+"nurse/load_surgeries/"+visit_id;
	    
	    if(XMLHttpRequestObject) {
	        
	        var obj = document.getElementById("surgeries");
	                
	        XMLHttpRequestObject.open("GET", url);
	                
	        XMLHttpRequestObject.onreadystatechange = function(){
	            
	            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

	                obj.innerHTML = XMLHttpRequestObject.responseText;
	            }
	        }
	                
	        XMLHttpRequestObject.send(null);
	    }
	}
	function save_surgery(visit_id){
	    var XMLHttpRequestObject = false;
	        
	    if (window.XMLHttpRequest) {
	    
	        XMLHttpRequestObject = new XMLHttpRequest();
	    } 
	        
	    else if (window.ActiveXObject) {
	        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	    var date = document.getElementById("datepicker").value;
	    var description = document.getElementById("surgery_description").value;
	    var month = document.getElementById("month").value;
	    var config_url = document.getElementById("config_url").value;
	    var url = config_url+"nurse/surgeries/"+date+"/"+description+"/"+month+"/"+visit_id;
	   
	    if(XMLHttpRequestObject) {
	                
	        XMLHttpRequestObject.open("GET", url);
	                
	        XMLHttpRequestObject.onreadystatechange = function(){
	            
	            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

	                get_surgeries(visit_id);
	            }
	        }
	                
	        XMLHttpRequestObject.send(null);
	    }
	}

	function delete_surgery(id, visit_id){
	    //alert(id);
	    var XMLHttpRequestObject = false;
	        
	    if (window.XMLHttpRequest) {
	    
	        XMLHttpRequestObject = new XMLHttpRequest();
	    } 
	        
	    else if (window.ActiveXObject) {
	        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	      var config_url = document.getElementById("config_url").value;
	    var url = config_url+"nurse/delete_surgeries/"+id;
	    
	    if(XMLHttpRequestObject) {
	                
	        XMLHttpRequestObject.open("GET", url);
	                
	        XMLHttpRequestObject.onreadystatechange = function(){
	            
	            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

	                get_surgeries(visit_id);
	            }
	        }
	                
	        XMLHttpRequestObject.send(null);
	    }
	}
	function save_medication(visit_id){
	    var config_url = document.getElementById("config_url").value;
	    var data_url = config_url+"nurse/medication/"+visit_id;
	   
	     var patient_medication = $('#medication_description').val();
	     var patient_medicine_allergies = $('#medicine_allergies').val();
	     var patient_food_allergies = $('#food_allergies').val();
	     var patient_regular_treatment = $('#regular_treatment').val();
	     
	    $.ajax({
	    type:'POST',
	    url: data_url,
	    data:{medication: patient_medication,medicine_allergies: patient_medicine_allergies, food_allergies: patient_food_allergies, regular_treatment: patient_regular_treatment },
	    dataType: 'text',
	    success:function(data){
	     get_medication(visit_id);
	    //obj.innerHTML = XMLHttpRequestObject.responseText;
	    },
	    error: function(xhr, status, error) {
	    //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
	    alert(error);
	    }

	    });

	       
	}
  </script>

<script type="text/javascript">
		$(document).ready(function() {
	
		check_date();
		load_patient_appointments();
	});

	function check_date(){
	     var datess=document.getElementById("scheduledate").value;
	     load_schedule();
	     load_patient_appointments_two();
	     if(datess){
		  $('#show_doctor').fadeToggle(1000); return false;
		 }
		 else{
		  alert('Select Date First')
		 }
	}

	function load_schedule(){
		var config_url = $('#config_url').val();
		var datess=document.getElementById("scheduledate").value;
		var doctor= <?php echo $this->session->userdata('personnel_id');?>//document.getElementById("doctor").value;

		var url= config_url+"reception/doc_schedule/"+doctor+"/"+datess;
	
		  $('#doctors_schedule').load(url);
		  $('#doctors_schedule').fadeIn(1000); return false;	
	}
	function load_patient_appointments(){
		var patient_id = $('#patient_id').val();
		var current_date = $('#current_date').val();

		var url= config_url+"reception/patient_schedule/"+patient_id+"/"+current_date;
		
		$('#patient_schedule').load(url);
		$('#patient_schedule').fadeIn(1000); return false;	

		$('#patient_schedule2').load(url);
		$('#patient_schedule2').fadeIn(1000); return false;	
	}
	function load_patient_appointments_two(){
		var patient_id = $('#patient_id').val();
		var current_date = $('#current_date').val();

		var url= config_url+"reception/patient_schedule/"+patient_id+"/"+current_date;
		
		$('#patient_schedule2').load(url);
		$('#patient_schedule2').fadeIn(1000); return false;	
	}
	function schedule_appointment(appointment_id)
	{
		if(appointment_id == '1')
		{
			$('#appointment_details').css('display', 'block');
		}
		else
		{
			$('#appointment_details').css('display', 'none');
		}
	}

	var config_url = $("#config_url").val();
		
	// $(document).ready(function(){
		
	//   	$.get( config_url+"nurse/get_family_history/<?php echo $visit_id;?>", function( data ) {
	// 		$("#new-nav").html(data);
	// 		$("#checkup_history").html(data);
	// 	});
	// });

	function pass_tooth()
    {

     var tooth_id = document.getElementById("tooth_id").value;
     var visit_id = document.getElementById("visit_id").value;
     var patient_id = document.getElementById("patient_id").value;


    var radios = document.getElementsByName('cavity_status');
    var cavity_status = null;
    for (var i = 0, length = radios.length; i < length; i++)
    {
     if (radios[i].checked)
     {
      // do whatever you want with the checked radio
       cavity_status = radios[i].value;
      // only one radio can be logically checked, don't check the rest
      break;
     }
    }
    
     

     var url = "<?php echo base_url();?>dental/save_dentine/"+visit_id+"/"+patient_id;
     //
     $.ajax({
     type:'POST',
     url: url,
     data:{tooth_id: tooth_id,patient_id: patient_id,cavity_status: cavity_status},
     dataType: 'text',
     success:function(data){
       // var prescription_view = document.getElementById("prescription_view");
       // prescription_view.style.display = 'none';

         var data = jQuery.parseJSON(data);
            
            var status = data.status;

            if(status == 'success')
            {
              alert(data.message);
              get_page_item(7,patient_id);
              // display_patient_history(visit_id,patient_id);

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
     
     return false;
    }

     function get_page_item(page_id,patient_id)
    {
        // alert(page_id);
        // get_page_links(page_id,patient_id);
        // if(page_id > 1)
        // {
            var visit_id = <?php echo $visit_id;?>;//window.localStorage.getItem('visit_id');
             // alert(visit_id);
            if(visit_id > 0)
            {

              var visit_id = visit_id;
              // get_page_header(visit_id); 
              var url = "<?php echo base_url();?>dental/get_page_item/"+page_id+"/"+patient_id+"/"+visit_id;  
              // alert(url);
              $.ajax({
              type:'POST',
              url: url,
              data:{page_id: page_id,patient_id: patient_id},
              dataType: 'text',
              success:function(data){
                  var data = jQuery.parseJSON(data);
                  var page_item = data.page_item;

                  // alert(page_item);
                  $('#page_item').html(data.page_item);
              },
              error: function(xhr, status, error) {
              alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
                  // display_patient_bill(visit_id);
              }
              });                
            }
            else
            {
              var visit_id = null;

              if(page_id > 1)
              {
                alert("Please select a date of a visit you wish to see");
              }
              else
              {
                var url = "<?php echo base_url();?>dental/get_page_item/"+page_id+"/"+patient_id+"/"+visit_id;  
            
                $.ajax({
                type:'POST',
                url: url,
                data:{page_id: page_id,patient_id: patient_id},
                dataType: 'text',
                success:function(data){
                    var data = jQuery.parseJSON(data);
                    var page_item = data.page_item;
                    $('#page_item').html(data.page_item);
                },
                error: function(xhr, status, error) {
                alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
                    // display_patient_bill(visit_id);
                }
                });   
              }
              
            }

       return false;

    }

     function check_department_type(teeth_number)
  {

    // var myTarget = document.getElementById("add_item");
    // myTarget.style.display = 'block';

    var visit_id = document.getElementById("visit_id").value;
    var patient_id = document.getElementById("patient_id").value;
     var XMLHttpRequestObject = false;
          
      if (window.XMLHttpRequest) {
      
          XMLHttpRequestObject = new XMLHttpRequest();
      } 
          
      else if (window.ActiveXObject) {
          XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
      }
      
      var config_url = document.getElementById("config_url").value;
      var url = config_url+"dental/display_dental_formula/"+teeth_number+"/"+visit_id+"/"+patient_id;
      // alert(url);
      if(XMLHttpRequestObject) {
                  
          XMLHttpRequestObject.open("GET", url);
                  
          XMLHttpRequestObject.onreadystatechange = function(){
              
              if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

                  document.getElementById("dental-formula").innerHTML=XMLHttpRequestObject.responseText;
              }
          }
                  
          XMLHttpRequestObject.send(null);
      }
  }




  function save_other_sick_off(visit_id)
	{
		 // start of saving rx
        var config_url = $('#config_url').val();
        var data_url = config_url+"dental/save_other_patient_sickoff/"+visit_id;
        //window.alert(data_url);
         var doctor_notes_rx = $('#deductions_and_other').val();//document.getElementById("vital"+vital_id).value;
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

	 function prescription_view()
  {

    // var myTarget = document.getElementById("add_item");
    // myTarget.style.display = 'block';

    var visit_id = document.getElementById("visit_id").value;
    var patient_id = document.getElementById("patient_id").value;
    // alert(patient_id);
     var XMLHttpRequestObject = false;
          
      if (window.XMLHttpRequest) {
      
          XMLHttpRequestObject = new XMLHttpRequest();
      } 
          
      else if (window.ActiveXObject) {
          XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
      }
      
      var config_url = document.getElementById("config_url").value;
      var url = config_url+"dental/display_patient_prescription/"+visit_id+"/"+patient_id;
      // alert(url);
      if(XMLHttpRequestObject) {
                  
          XMLHttpRequestObject.open("GET", url);
                  
          XMLHttpRequestObject.onreadystatechange = function(){
              
              if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

                  document.getElementById("visit-prescription").innerHTML=XMLHttpRequestObject.responseText;
              }
          }
                  
          XMLHttpRequestObject.send(null);
      }
  }


	function save_prescription(patient_id,visit_id)
	{
		 // start of saving rx
		 // var prescription = tinymce.get('#visit_prescription').getContent();
        var config_url = $('#config_url').val();
        var data_url = config_url+"dental/save_prescription/"+patient_id+"/"+visit_id;
        // window.alert(data_url);
         // var prescription = $('#visit_prescription').val();//document.getElementById("vital"+vital_id).value;
         		// console.debug(tinymce.activeEditor.getContent());

        var prescription = tinymce.get('visit_prescription'+visit_id).getContent();

        
			 // alert(visit_id);

         // alert(prescription);
        $.ajax({
        type:'POST',
        url: data_url,
        data:{prescription: prescription},
        dataType: 'text',
        success:function(data){
        //obj.innerHTML = XMLHttpRequestObject.responseText;
           window.alert("You have successfully updated the prescription");
           prescription_view();
        },
        error: function(xhr, status, error) {
        //alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
        alert(error);
        }

        });
      // end of saving rx
	}
  

</script>