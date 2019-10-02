<div class="row" >
		<header class="panel-heading">						
			<a class='btn btn-sm btn-warning ' data-toggle='modal' data-target='#add_assessment' ><i class="fa fa-plus"></i> Add Service</a>
			<a class='btn btn-sm btn-info ' data-toggle='modal' data-target='#add_provider' ><i class="fa fa-plus"></i> Add Provider</a>
			<span style="text-transform: uppercase;"><strong>Patient Name: <?php echo $title;?></strong></span>
			<a href="<?php echo site_url();?>queue" class="btn btn-info btn-sm pull-right " ><i class="fa fa-arrow-left"></i> Back to Queue</a>
		</header>	
		

</div>
	
<div class="row" >
	<div class="col-md-3" style="background: #fff;min-height:500px;border-right: grey 2px solid;">
		<div class="row">
			<header class="panel-heading">						
				<h2 class="panel-title">Vists</h2>
				
			</header>

			<div id="visits_div"></div>

		</div>
	</div>
	<div class="col-md-9" style="background: #fff;min-height:500px;">
		<div class="row">
			<header class="panel-heading">						
				<div id="page_header"></div>
				
			</header>
		</div>
		<div class="row">
			<div id="patient_bill"></div>
		</div>
	</div>
</div>

<div class="modal fade bs-example-modal-lg" id="add_assessment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add New Service</h4>
            </div>
            <?php echo form_open("accounts/add_service_item", array("class" => "form-horizontal"));?>
            <div class="modal-body">
            	<div class="row">
                	<div class='col-md-12'>
                      	<div class="form-group">
							<label class="col-lg-4 control-label">Service Name: </label>
						  
							<div class="col-lg-8">
								<select id='parent_service_id' name='parent_service_id' class='form-control custom-select ' >
			                      <option value=''>None - Please Select a service</option>
			                       <?php echo $services_items;?>
			                    </select>
							</div>
						</div>
						 <input type="hidden" class="form-control" name="redirect_url" placeholder="" autocomplete="off" value="<?php echo $this->uri->uri_string()?>">
                      	<div class="form-group">
							<label class="col-lg-4 control-label">Charge Name: </label>
						  
							<div class="col-lg-5">
								<input type="text" class="form-control" name="service_charge_item" placeholder="" autocomplete="off">
							</div>
						</div>				
                      	<div class="form-group">
							<label class="col-lg-4 control-label">Service Amount: </label>
						  
							<div class="col-lg-5">
								<input type="number" class="form-control" name="service_amount" placeholder="" autocomplete="off" >
							</div>
						</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            	<button type="submit" class='btn btn-info btn-sm' type='submit' >Add Service</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="add_provider" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add New Provider</h4>
                </div>
                 <?php echo form_open("accounts/add_accounts_personnel", array("class" => "form-horizontal"));?>
                <div class="modal-body">
                	<div class="row">
                    	<div class='col-md-12'>
                          	<div class="form-group">
								<label class="col-lg-4 control-label">First Name: </label>
							  <input type="hidden" class="form-control" name="redirect_url" placeholder="" autocomplete="off" value="<?php echo $this->uri->uri_string()?>">
								<div class="col-lg-5">
									<input type="text" class="form-control" name="personnel_fname" placeholder="" autocomplete="off">
								</div>
							</div>
                          	<div class="form-group">
								<label class="col-lg-4 control-label">Other Names: </label>
							  
								<div class="col-lg-5">
									<input type="text" class="form-control" name="personnel_onames" placeholder="" autocomplete="off">
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Phone Number: </label>
							  
								<div class="col-lg-5">
									<input type="text" class="form-control" name="personnel_phone" placeholder="" autocomplete="off">
								</div>
							</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                	<button type="submit" class='btn btn-info btn-sm' type='submit' >Add Provider</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    
                </div>
                <?php echo form_close();?>
            </div>
        </div>
</div>
<div class="modal fade bs-example-modal-lg" id="add_to_bill" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add to Bill</h4>
            </div>
             <?php echo form_open("accounts/add_accounts_personnel", array("class" => "form-horizontal","id"=>"add_bill"));?>
            <div class="modal-body">
            	<div class="row">
            		<div class="col-md-2 ">
            		</div>
		            	<div class="col-md-10 ">
		                    <div class="col-md-12" style="margin-bottom: 10px">
			                  <div class="form-group">
			                  <label class="col-md-2 control-label">Service: </label>
			                  	<div class="col-md-10">
				                    <select id='service_id_item' name='service_id' class='form-control custom-select ' >
				                      <option value=''>None - Please Select a service</option>
				                       <?php echo $services_list;?>
				                    </select>

				                    <input type="hidden" name="visit_id_checked" id="visit_id_checked">
			                    </div>
			                  </div>
			                </div>
			                <br>
			                <div class="col-md-12">
				                <div class="form-group">
									<label class="col-lg-2 control-label">Date: </label>
									
									<div class="col-lg-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_date" id="visit_date_date" placeholder="Admission Date" value="<?php echo date('Y-m-d');?>">
                                        </div>
									</div>
								</div>
							</div>
			            </div>
			            <div class="col-md-12" style="margin:20px;">
			            	<div class="center-align">
								
							</div>
			            </div>
			        </div>
            </div>
            <div class="modal-footer">
            	<button type="submit" class='btn btn-info btn-sm' type='submit' >Add to Bill</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="add_payment_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add to Bill</h4>
            </div>
             <?php echo form_open("accounts/add_accounts_personnel", array("class" => "form-horizontal","id"=>"add_payment"));?>
            <div class="modal-body">
            	<div class="row">
            		<div class="col-md-2 ">
            		</div>
		            	<div class="col-md-10 ">
		            		<div class="form-group" >
								<div class="col-lg-4">
                                	<div class="radio">
                                        <label>
                                            <input id="optionsRadios2" type="radio" name="type_payment" id="type_payment" value="1"  onclick="getservices(1)" > 
                                            Normal
                                        </label>
                                    </div>
								</div>
								<div class="col-lg-4" style="display: none">
                                	<div class="radio">
                                        <label>
                                            <input id="optionsRadios2" type="radio" name="type_payment" id="type_payment" value="2"> 
                                            Debit Note
                                        </label>
                                    </div>
								</div>
								<div class="col-lg-4" >
                                	<div class="radio">
                                        <label>
                                            <input id="optionsRadios2" type="radio" name="type_payment" id="type_payment" value="3"  onclick="getservices(1)"> 
                                            Waiver
                                        </label>
                                    </div>
								</div>
							</div>
							 
                           	<!-- <input type="hidden" name="type_payment" value="1"> -->
                           	<!-- <input type="hidden" name="service_id" id="service_id" value="0"> -->
                           	<div id="normal_div" style="display: none;">
                           		<div id="service_div" class="form-group" >
	                                <label class="col-lg-2 control-label"> Services: </label>
	                                
	                                <div class="col-lg-8">
	                                    <select class="form-control" name="service_id" >
	                                    	<option value="">--Select a service--</option>
											<?php
	                                        $service_rs = $this->accounts_model->get_all_service();
	                                        $service_num_rows = count($service_rs);
	                                        if($service_num_rows > 0)
	                                        {
												foreach($service_rs as $service_res)
												{
													$service_id = $service_res->service_id;
													$service_name = $service_res->service_name;
													
													echo '<option value="'.$service_id.'">'.$service_name.'</option>';
												}
	                                        }
	                                        ?>
	                                    </select>
	                                </div>
	                            </div>

	                        <div class="col-md-12" style="margin-bottom: 10px">
	                        	<input type="hidden" name="provider_id" id="provider_id_item" value="0">

			                  
			                </div>

								<div class="form-group">
									<label class="col-lg-2 control-label">Amount: </label>
								  
									<div class="col-lg-8">
										<input type="text" class="form-control" name="amount_paid" id="amount_paid" placeholder="" autocomplete="off"  onkeyup="get_change()">
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-lg-2 control-label">Payment Method: </label>
									  
									<div class="col-lg-8">
										<select class="form-control" name="payment_method" id="payment_method" onchange="check_payment_type(this.value)">
											<option value="0">Select a group</option>
	                                    	<?php
											  $method_rs = $this->accounts_model->get_payment_methods();
											  $num_rows = count($method_rs);
											 if($num_rows > 0)
											  {
												
												foreach($method_rs as $res)
												{
												  $payment_method_id = $res->payment_method_id;
												  $payment_method = $res->payment_method;
												  
													echo '<option value="'.$payment_method_id.'">'.$payment_method.'</option>';
												  
												}
											  }
										  ?>
										</select>
									  </div>
								</div>
                           		
                           	</div>
                           	<div id="waiver_div" style="display: none;">

                           		<div id="service_div" class="form-group" >
	                                <label class="col-lg-2 control-label"> Services: </label>
	                                
	                                <div class="col-lg-8">
	                                    <select class="form-control" name="waiver_service_id" >
	                                    	<option value="">--Select a service--</option>
											<?php
	                                        $service_rs = $this->accounts_model->get_all_service();
	                                        $service_num_rows = count($service_rs);
	                                        if($service_num_rows > 0)
	                                        {
												foreach($service_rs as $service_res)
												{
													$service_id = $service_res->service_id;
													$service_name = $service_res->service_name;
													
													echo '<option value="'.$service_id.'">'.$service_name.'</option>';
												}
	                                        }
	                                        ?>
	                                    </select>
	                                </div>
	                            </div>

	                         <div class="col-md-12" style="margin-bottom: 10px">
			                  <div class="form-group " >
			                  <label class="col-md-2 control-label">Provider: </label>
			                  	<div class="col-md-10">
				                    <select id='provider_id_item' name='provider_id' class='form-control custom-select ' >
				                      <option value=''>None - Please Select a provider</option>
				                      <?php
									
											if(count($doctor) > 0){
												foreach($doctor as $row):
													$fname = $row->personnel_fname;
													$onames = $row->personnel_onames;
													$personnel_id = $row->personnel_id;
													
													if($personnel_id == set_value('personnel_id'))
													{
														echo "<option value='".$personnel_id."' selected='selected'>".$onames." ".$fname."</option>";
													}
													
													else
													{
														echo "<option value='".$personnel_id."'>".$onames." ".$fname."</option>";
													}
												endforeach;
											}
										?>
				                    </select>
				                </div>
			                  </div>
			                </div>

								<div class="form-group">
									<label class="col-lg-2 control-label">Amount: </label>
								  
									<div class="col-lg-8">
										<input type="text" class="form-control" name="waiver_amount" id="waiver_amount" placeholder="" autocomplete="off"  onkeyup="get_change()">
									</div>
								</div>
								
                           		
                           	</div>                          							
	                        

							<input type="hidden" class="form-control" name="change_payment" id="change_payment" placeholder="" autocomplete="off" >
							<div id="mpesa_div" class="form-group" style="display:none;" >
								<label class="col-lg-2 control-label"> Mpesa TX Code: </label>

								<div class="col-lg-8">
									<input type="text" class="form-control" name="mpesa_code" id="mpesa_code" placeholder="">
								</div>
							</div>
						  
							<div id="insuarance_div" class="form-group" style="display:none;" >
								<label class="col-lg-2 control-label"> Credit Card Detail: </label>
								<div class="col-lg-8">
									<input type="text" class="form-control" name="insuarance_number" id="insuarance_number" placeholder="">
								</div>
							</div>
						  
							<div id="cheque_div" class="form-group" style="display:none;" >
								<label class="col-lg-2 control-label"> Cheque Number: </label>
							  
								<div class="col-lg-8">
									<input type="text" class="form-control" name="cheque_number" id="cheque_number" placeholder="">
								</div>
							</div>
							<div id="bank_deposit_div" class="form-group" style="display:none;" >
								<label class="col-lg-2 control-label"> Deposit Detail: </label>
							  
								<div class="col-lg-8">
									<input type="text" class="form-control" name="deposit_detail" id="deposit_detail" placeholder="">
								</div>
							</div>
							<div id="debit_card_div" class="form-group" style="display:none;" >
								<label class="col-lg-2 control-label"> Debit Card Detail: </label>
							  
								<div class="col-lg-8">
									<input type="text" class="form-control" name="debit_card_detail" id="debit_card_detail" placeholder="">
								</div>
							</div>
						  
							<div id="username_div" class="form-group" style="display:none;" >
								<label class="col-lg-2 control-label"> Username: </label>
							  
								<div class="col-lg-8">
									<input type="text" class="form-control" name="username" id="username" placeholder="">
								</div>
							</div>
						  
							<div id="password_div" class="form-group" style="display:none;" >
								<label class="col-lg-2 control-label"> Password: </label>
							  
								<div class="col-lg-8">
									<input type="password" class="form-control" name="password" id="password" placeholder="">
								</div>
							</div>
			            </div>
			           <input type="hidden" name="visit_id_payments" id="visit_id_payments">
			        </div>
            </div>
            <div class="modal-footer">
            	<h4 class="pull-left" > Change : <span id="change_item"></span></h4>
            	<button type="submit" class='btn btn-info btn-sm' type='submit' >Add Payment</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>
 <div class="modal fade bs-example-modal-lg" id="end_visit_date" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Discharge Patient</h4>
            </div>
            <?php echo form_open("", array("class" => "form-horizontal","id"=>"discharge-patient"));?>
            <div class="modal-body">
            	<div class="row">
                	<div class="col-md-12">
		                <div class="form-group">
							<label class="col-lg-2 control-label">Disharged Date: </label>
							
							<div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="visit_date_charged" id="visit_date_charged" placeholder="Discharged Date" value="<?php echo date('Y-m-d');?>">
                                </div>
							</div>
						</div>
					</div>
                </div>
            </div>
			 <input type="hidden" name="visit_discharge_visit" id="visit_discharge_visit">
            <div class="modal-footer">
            	<button type="submit" class='btn btn-info btn-sm' type='submit' >Discharge Patient</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>

 <div class="modal fade bs-example-modal-lg" id="change_patient_type" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Change Patient Visit Type</h4>
            </div>
            <?php echo form_open("accounts/change_patient_visit", array("class" => "form-horizontal","id"=>"visit_type_change"));?>
            <div class="modal-body">
            	<div class="row">
                	<div class='col-md-12'>
                      	<div class="form-group">
							<label class="col-lg-2 control-label">Type: </label>
						  
							<div class="col-lg-8">
								<select id='visit_type_id' name='visit_type_id' class='form-control' >
			                      <option value=''>None - Please Select a service</option>
			                      <?php
																
									if($visit_types_rs->num_rows() > 0){

										foreach($visit_types_rs->result() as $row):
											$visit_type_name = $row->visit_type_name;
											$visit_type_id = $row->visit_type_id;

											if($visit_type_id == $patient_type_id)
											{
												echo "<option value='".$visit_type_id."' selected='selected'>".$visit_type_name."</option>";
											}
											
											else
											{
												echo "<option value='".$visit_type_id."'>".$visit_type_name."</option>";
											}
										endforeach;
									}
								?>
			                      
			                    </select>
							</div>
						</div>

						 <input type="hidden" class="form-control" name="redirect_url" placeholder="" autocomplete="off" value="<?php echo $this->uri->uri_string()?>">
                      	 <input type="hidden" name="visit_id_visit" id="visit_id_visit">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            	<button type="submit" class='btn btn-info btn-sm' type='submit' >Change Type</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>
<script type="text/javascript">
	function get_visit_detail(visit_id)
	{
		// alert(visit_id);
		
		document.getElementById("visit_id_checked").value = visit_id;
		document.getElementById("visit_id_payments").value = visit_id;
		document.getElementById("visit_id_visit").value = visit_id;
		document.getElementById("visit_discharge_visit").value = visit_id;

		display_patient_bill(visit_id);
	}
	function get_next_page(page,visit_id)
	{
		// alert()
		 var XMLHttpRequestObject = false;
          
      if (window.XMLHttpRequest) {
      
          XMLHttpRequestObject = new XMLHttpRequest();
      } 
          
      else if (window.ActiveXObject) {
          XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
      }
      
      var config_url = document.getElementById("config_url").value;
      var url = config_url+"accounts/get_visits_div/"+visit_id+"/"+page;
      // alert(url);
      if(XMLHttpRequestObject) {
                  
          XMLHttpRequestObject.open("GET", url);
                  
          XMLHttpRequestObject.onreadystatechange = function(){
              
              if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

                  document.getElementById("visits_div").innerHTML=XMLHttpRequestObject.responseText;
              }
          }
                  
          XMLHttpRequestObject.send(null);
      }

	}

	function get_next_invoice_page(page,visit_id)
	{
		// alert(page);
		 var XMLHttpRequestObject = false;
          
      if (window.XMLHttpRequest) {
      
          XMLHttpRequestObject = new XMLHttpRequest();
      } 
          
      else if (window.ActiveXObject) {
          XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
      }
      
      var config_url = document.getElementById("config_url").value;
      var url = config_url+"accounts/view_patient_bill/"+visit_id+"/"+page;
      // alert(url);
      if(XMLHttpRequestObject) {
                  
          XMLHttpRequestObject.open("GET", url);
                  
          XMLHttpRequestObject.onreadystatechange = function(){
              
              if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

                  document.getElementById("patient_bill").innerHTML=XMLHttpRequestObject.responseText;
              }
          }
                  
          XMLHttpRequestObject.send(null);
      }

	}

	function get_next_payments_page(page,visit_id)
	{
		// alert(page);
		 var XMLHttpRequestObject = false;
          
      if (window.XMLHttpRequest) {
      
          XMLHttpRequestObject = new XMLHttpRequest();
      } 
          
      else if (window.ActiveXObject) {
          XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
      }
      
      var config_url = document.getElementById("config_url").value;
      var url = config_url+"accounts/get_patient_receipt/"+visit_id+"/"+page;
      // alert(url);
      if(XMLHttpRequestObject) {
                  
          XMLHttpRequestObject.open("GET", url);
                  
          XMLHttpRequestObject.onreadystatechange = function(){
              
              if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

                  document.getElementById("payments-made").innerHTML=XMLHttpRequestObject.responseText;
              }
          }
                  
          XMLHttpRequestObject.send(null);
      }

	}


	function get_page_header(visit_id)
	{
		// alert()
		 var XMLHttpRequestObject = false;
          
      if (window.XMLHttpRequest) {
      
          XMLHttpRequestObject = new XMLHttpRequest();
      } 
          
      else if (window.ActiveXObject) {
          XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
      }
      
      var config_url = document.getElementById("config_url").value;
      var url = config_url+"accounts/get_patient_details_header/"+visit_id;
      // alert(url);
      if(XMLHttpRequestObject) {
                  
          XMLHttpRequestObject.open("GET", url);
                  
          XMLHttpRequestObject.onreadystatechange = function(){
              
              if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

                  document.getElementById("page_header").innerHTML=XMLHttpRequestObject.responseText;
              }
          }
                  
          XMLHttpRequestObject.send(null);
      }

	}
</script>

<script type="text/javascript">

  $(function() {
       $("#service_id_item").customselect();
       $("#provider_id_item").customselect();
       $("#parent_service_id").customselect();

   });
   $(document).ready(function(){
   		// display_patient_bill(<?php echo $visit_id;?>);
   		// alert(<?php echo $num_pages;?>)

   		get_all_visits_div(<?php echo $patient_id;?>);
   		// display_patient_bill(<?php echo $visit_id;?>);
   });
     
  function getservices(id){

		var type_payment =  $("input[name='type_payment']:checked").val();

        // var myTarget1 = document.getElementById("service_div");
        var myTarget5 = document.getElementById("normal_div");
        var myTarget6 = document.getElementById("waiver_div");
		// alert(id);
        if(type_payment == 1)
        {
          myTarget6.style.display = 'none';
          myTarget5.style.display = 'block';
        }
        else
        {
          myTarget6.style.display = 'block';
          myTarget5.style.display = 'none';
        }
        
  }



  function check_payment_type(payment_type_id){
   
    var myTarget1 = document.getElementById("cheque_div");

    var myTarget2 = document.getElementById("mpesa_div");

    var myTarget4 = document.getElementById("debit_card_div");

    var myTarget5 = document.getElementById("bank_deposit_div");

    var myTarget3 = document.getElementById("insuarance_div");

    if(payment_type_id == 1)
    {
      // this is a check     
      myTarget1.style.display = 'block';
      myTarget2.style.display = 'none';
      myTarget3.style.display = 'none';
      myTarget4.style.display = 'none';
      myTarget5.style.display = 'none';
    }
    else if(payment_type_id == 2)
    {
      myTarget1.style.display = 'none';
      myTarget2.style.display = 'none';
      myTarget3.style.display = 'none';
      myTarget4.style.display = 'none';
      myTarget5.style.display = 'none';
    }
    else if(payment_type_id == 7)
    {
      myTarget1.style.display = 'none';
      myTarget2.style.display = 'none';
      myTarget3.style.display = 'none';
      myTarget4.style.display = 'none';
      myTarget5.style.display = 'block';
    }
    else if(payment_type_id == 8)
    {
      myTarget1.style.display = 'none';
      myTarget2.style.display = 'none';
      myTarget3.style.display = 'none';
      myTarget4.style.display = 'block';
      myTarget5.style.display = 'none';
    }
    else if(payment_type_id == 5)
    {
      myTarget1.style.display = 'none';
      myTarget2.style.display = 'block';
      myTarget3.style.display = 'none';
      myTarget4.style.display = 'none';
      myTarget5.style.display = 'none';
    }
    else if(payment_type_id == 6)
    {
       myTarget1.style.display = 'none';
      myTarget2.style.display = 'none';
      myTarget3.style.display = 'block';
      myTarget4.style.display = 'none';
      myTarget5.style.display = 'none';  
    }

  }

   function display_patient_bill(visit_id){

   	// alert(visit_id);
      var XMLHttpRequestObject = false;
          
      if (window.XMLHttpRequest) {
      
          XMLHttpRequestObject = new XMLHttpRequest();
      } 
          
      else if (window.ActiveXObject) {
          XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
      }
      
      var config_url = document.getElementById("config_url").value;
      var url = config_url+"accounts/view_patient_bill/"+visit_id;
      // alert(url);
      if(XMLHttpRequestObject) {
                  
          XMLHttpRequestObject.open("GET", url);
                  
          XMLHttpRequestObject.onreadystatechange = function(){
              
              if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

                  document.getElementById("patient_bill").innerHTML=XMLHttpRequestObject.responseText;

                 get_services_offered(visit_id);
                 get_patient_receipt(visit_id);
      			 get_page_header(visit_id);
              }
          }
                  
          XMLHttpRequestObject.send(null);
      }

      
  }
  function get_services_offered(visit_id){

      var XMLHttpRequestObject = false;
          
      if (window.XMLHttpRequest) {
      
          XMLHttpRequestObject = new XMLHttpRequest();
      } 
          
      else if (window.ActiveXObject) {
          XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
      }
      
      var config_url = document.getElementById("config_url").value;
      var url = config_url+"accounts/get_services_billed/"+visit_id;
      // alert(url);
      if(XMLHttpRequestObject) {
                  
          XMLHttpRequestObject.open("GET", url);
                  
          XMLHttpRequestObject.onreadystatechange = function(){
              
              if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

                  document.getElementById("billed_services").innerHTML=XMLHttpRequestObject.responseText;
              }
          }
                  
          XMLHttpRequestObject.send(null);
      }
      get_page_header(visit_id);
  }

  function get_patient_receipt(visit_id){

      var XMLHttpRequestObject = false;
          
      if (window.XMLHttpRequest) {
      
          XMLHttpRequestObject = new XMLHttpRequest();
      } 
          
      else if (window.ActiveXObject) {
          XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
      }
      
      var config_url = document.getElementById("config_url").value;
      var url = config_url+"accounts/get_patient_receipt/"+visit_id;
      // alert(url);
      if(XMLHttpRequestObject) {
                  
          XMLHttpRequestObject.open("GET", url);
                  
          XMLHttpRequestObject.onreadystatechange = function(){
              
              if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

                  document.getElementById("payments-made").innerHTML=XMLHttpRequestObject.responseText;
              }
          }
                  
          XMLHttpRequestObject.send(null);
      }
      get_page_header(visit_id);
  }
  function get_all_visits_div(visit_id){

      var XMLHttpRequestObject = false;
          
      if (window.XMLHttpRequest) {
      
          XMLHttpRequestObject = new XMLHttpRequest();
      } 
          
      else if (window.ActiveXObject) {
          XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
      }
      
      var config_url = document.getElementById("config_url").value;
      var url = config_url+"accounts/get_visits_div/"+visit_id;
      // alert(url);
      if(XMLHttpRequestObject) {
                  
          XMLHttpRequestObject.open("GET", url);
                  
          XMLHttpRequestObject.onreadystatechange = function(){
              
              if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

                  document.getElementById("visits_div").innerHTML=XMLHttpRequestObject.responseText;
              }
          }
                  
          XMLHttpRequestObject.send(null);
      }
  }

	//Calculate procedure total
	function calculatetotal(amount, id, procedure_id, v_id){
	     
	    var units = document.getElementById('units'+id).value;  
	    var billed_amount = document.getElementById('billed_amount'+id).value;  

	    grand_total(id, units, billed_amount, v_id);
	}
	function grand_total(procedure_id, units, amount, v_id){
    	var config_url = document.getElementById("config_url").value;
    	var url = config_url+"accounts/update_service_total/"+procedure_id+"/"+units+"/"+amount+"/"+v_id;
	
		$.ajax({
		type:'POST',
		url: url,
		data:{visit_id: v_id},
		dataType: 'json',
		success:function(data){
			alert(data.message);
			display_patient_bill(v_id);
		},
		error: function(xhr, status, error) {
		alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
			display_patient_bill(v_id);
		}
		});
		return false;

	    
	   
	}
	function delete_service(id, visit_id){

		var res = confirm('Are you sure you want to delete this charge?');
     
	    if(res)
	    {

	    	var config_url = document.getElementById("config_url").value;
	    	var url = config_url+"accounts/delete_service_billed/"+id+"/"+visit_id;
		
			$.ajax({
			type:'POST',
			url: url,
			data:{visit_id: visit_id,id: id},
			dataType: 'json',
			success:function(data){
				alert(data.message);
				display_patient_bill(visit_id);
			},
			error: function(xhr, status, error) {
			alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
				display_patient_bill(visit_id);
			}
			});
			return false;
		    var XMLHttpRequestObject = false;
		        
		    if (window.XMLHttpRequest) {
		    
		        XMLHttpRequestObject = new XMLHttpRequest();
		    } 
		        
		    else if (window.ActiveXObject) {
		        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
		    }
		     var config_url = document.getElementById("config_url").value;
		    var url = config_url+"accounts/delete_service_billed/"+id;
		    
		    if(XMLHttpRequestObject) {
		                
		        XMLHttpRequestObject.open("GET", url);
		                
		        XMLHttpRequestObject.onreadystatechange = function(){
		            
		            if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {

		                display_patient_bill(visit_id);
		            }
		        }
		                
		        XMLHttpRequestObject.send(null);
		    }
		}
	}
	function save_service_items(visit_id)
	{
		var provider_id = $('#provider_id'+visit_id).val();
		var service_id = $('#service_id'+visit_id).val();
		var visit_date = $('#visit_date_date'+visit_id).val();
		var url = "<?php echo base_url();?>accounts/add_patient_bill/"+visit_id;
		
		$.ajax({
		type:'POST',
		url: url,
		data:{provider_id: provider_id, service_charge_id: service_id, visit_date: visit_date},
		dataType: 'text',
		success:function(data){
			alert("You have successfully billed");
			display_patient_bill(visit_id);
		},
		error: function(xhr, status, error) {
		alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
			display_patient_bill(visit_id);
		}
		});
		return false;
	}

	$(document).on("submit","form#add_bill",function(e)
	{
		e.preventDefault();	

		var service_id = $('#service_id_item').val();
		var provider_id = $('#provider_id_item').val();
		var visit_date = $('#visit_date_date').val();
		var visit_id = $('#visit_id_checked').val();
		var url = "<?php echo base_url();?>accounts/add_patient_bill/"+visit_id;
		
		$.ajax({
		type:'POST',
		url: url,
		data:{provider_id: provider_id, service_charge_id: service_id, visit_date: visit_date},
		dataType: 'json',
		success:function(data){

			alert(data.message);
		 	$('#add_to_bill').modal('toggle');
			display_patient_bill(visit_id);
		},
		error: function(xhr, status, error) {
		alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
			display_patient_bill(visit_id);
		}
		});
		return false;
	});

	$(document).on("submit","form#visit_type_change",function(e)
	{
		e.preventDefault();	

		var visit_type_id = $('#visit_type_id').val();
		var visit_id = $('#visit_id_visit').val();
		var url = "<?php echo base_url();?>accounts/change_patient_visit/"+visit_id;
		
		$.ajax({
		type:'POST',
		url: url,
		data:{visit_type_id: visit_type_id},
		dataType: 'text',
		success:function(data){
			alert("You have successfully changed patient type");
		 	$('#change_patient_type').modal('toggle');
			display_patient_bill(visit_id);
		},
		error: function(xhr, status, error) {
		alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
			display_patient_bill(visit_id);
		}
		});
		return false;
	});

	$(document).on("submit","form#discharge-patient",function(e)
	{
		e.preventDefault();	

		var visit_date_charged = $('#visit_date_charged').val();
		var visit_id = $('#visit_discharge_visit').val();
		var url = "<?php echo base_url();?>accounts/discharge_patient/"+visit_id;
		
		$.ajax({
		type:'POST',
		url: url,
		data:{visit_date_charged: visit_date_charged},
		dataType: 'json',
		success:function(data){
			alert(data.message);
		 	$('#end_visit_date').modal('toggle');
			display_patient_bill(visit_id);
		},
		error: function(xhr, status, error) {
		alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
			display_patient_bill(visit_id);
		}
		});
		return false;
	});

	$(document).on("submit","form#payments-paid-form",function(e)
	{
		// alert("changed");
		e.preventDefault();	

		var cancel_action_id = $('#cancel_action_id').val();
		var cancel_description = $('#cancel_description').val();
		var visit_id = $('#visit_id').val();
		var payment_id = $('#payment_id').val();
		var url = "<?php echo base_url();?>accounts/cancel_payment/"+payment_id+"/"+visit_id;		
		$.ajax({
		type:'POST',
		url: url,
		data:{cancel_description: cancel_description, cancel_action_id: cancel_action_id},
		dataType: 'text',
		success:function(data){
			alert("You have successfully cancelled a payment");
		 	$('#refund_payment'+visit_id).modal('toggle');
		 	get_page_header(visit_id);
			get_patient_receipt(visit_id);
		},
		error: function(xhr, status, error) {
		alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
			get_page_header(visit_id);
			get_patient_receipt(visit_id);
		}
		});
		return false;
	});


	$(document).on("submit","form#add_payment",function(e)
	{
		e.preventDefault();	

		var payment_method = $('#payment_method').val();
		var amount_paid = $('#amount_paid').val();
		var type_payment =  $("input[name='type_payment']:checked").val(); //$('#type_payment').val();
		// alert(amount_paid); die();
		var service_id = $('#service_id').val();
		var waiver_amount = $('#waiver_amount').val();
		var waiver_service_id = $('#waiver_service_id').val();		
		var cheque_number = $('#cheque_number').val();
		var insuarance_number = $('#insuarance_number').val();
		var mpesa_code = $('#mpesa_code').val();
		var username = $('#username').val();
		var password = $('#password').val();
		var change_payment = $('#change_payment').val();

		var debit_card_detail = $('#debit_card_detail').val();
		var deposit_detail = $('#deposit_detail').val();
		var password = $('#password').val();


		var visit_id = $('#visit_id_payments').val();

		var payment_service_id = $('#payment_service_id').val();

	
		var url = "<?php echo base_url();?>accounts/make_payments/"+visit_id;
		// alert(type_payment);
		$.ajax({
		type:'POST',
		url: url,
		data:{payment_method: payment_method, amount_paid: amount_paid, type_payment: type_payment,service_id: service_id, cheque_number: cheque_number, insuarance_number: insuarance_number, mpesa_code: mpesa_code,username: username,password: password, payment_service_id: payment_service_id,debit_card_detail: debit_card_detail,deposit_detail: deposit_detail,change_payment:change_payment,waiver_amount: waiver_amount, waiver_service_id},
		dataType: 'json',
		success:function(data){

			if(data.result == 'success')
        	{
				alert(data.message);

			 	$('#add_payment_modal').modal('toggle');
			 	get_page_header(visit_id);
      			get_patient_receipt(visit_id,null);
				 
			}
			else
			{
				alert(data.message);
			}
		},
		error: function(xhr, status, error) {
		alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
			display_patient_bill(visit_id);
		}
		});
		return false;
	});
	function close_visit(visit_id)
	{
		var res = confirm('Are you sure you want to end this visit?');
     
	    if(res)
	    {
	    	var url = "<?php echo base_url();?>accounts/close_visit/"+visit_id;
		
			$.ajax({
			type:'POST',
			url: url,
			data:{visit_id: visit_id},
			dataType: 'json',
			success:function(data){
				alert(data.message);
				setTimeout(function() {
					send_message(visit_id);
				  }, 2000);
				display_patient_bill(visit_id);
			},
			error: function(xhr, status, error) {
			alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
				display_patient_bill(visit_id);
			}
			});
			return false;

	    }
	}
	function send_message(visit_id)
	{
		var url = "<?php echo base_url();?>accounts/send_message/"+visit_id;
		// alert(url);
			$.ajax({
			type:'POST',
			url: url,
			data:{visit_id: visit_id},
			dataType: 'json',
			success:function(data){

				window.location.href = "<?php echo base_url();?>queue";
			},
			error: function(xhr, status, error) {
			alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
				
			}
			});
			return false;
	}
	function get_change()
	{

		var visit_id = $('#visit_id_payments').val();
	
		var amount_paid = $('#amount_paid').val();
		var url = "<?php echo base_url();?>accounts/get_change/"+visit_id;
	
		$.ajax({
		type:'POST',
		url: url,
		data:{visit_id: visit_id, amount_paid: amount_paid},
		dataType: 'json',
		success:function(data){
			var change = data.change;

			document.getElementById("change_payment").value = change;
			$('#change_item').html("Kes."+data.change);

		},
		error: function(xhr, status, error) {
		alert("XMLHttpRequest=" + xhr.responseText + "\ntextStatus=" + status + "\nerrorThrown=" + error);
			display_patient_bill(visit_id);
		}
		});
		return false;
	}

 
</script>