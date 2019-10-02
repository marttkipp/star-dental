<?php
   $customer_national_id = set_value('customer_national_id');
         $type = set_value('type');
         $amount = set_value('amount');
   
   $result = '';
   
   //if users exist display them
   if ($query->num_rows() > 0)
   {
   	$count = 0;
   	
   	$result .= 
   	'
   	<table class="table table-bordered table-striped table-condensed">
   		<thead>
   			<tr>
   				<th>#</th>
	             <th>Date</th>
	             <th>Account Name</th>
	             <th>Status</th>
	             <th></th>
                         
   			</tr>
   		</thead>
   		  <tbody>
   		  
   	';
   	
   	//get all administrators
   	$administrators = $this->users_model->get_active_users();
   	if ($administrators->num_rows() > 0)
   	{
   		$admins = $administrators->result();
   	}
   	
   	else
   	{
   		$admins = NULL;
   	}
   	
   	foreach ($query->result() as $row)
   	{
   				$account_id = $row->account_id;
                 $created = $row->created;        
                 $account_name = $row->account_name;
                 
                 $sale_status = $row->department_account_status;
                 
              
                
                 
                 //status
                 if($sale_status == 1)
                 {
                     $status = 'Active';
                 }
                 else
                 {
                     $status = 'Disabled';
                 }
                 
                 
                 //create deactivated status display
                 if($sale_status == 0)
                 {
                     $status = '<span class="label label-default">Deactivated</span>';
                     
                 }
                 //create activated status display
                 else if($sale_status == 1)
                 {
                     $status = '<span class="label label-success">Active</span>';
                    
                 }
   		
   		
   		$count++;
   		$result .= 
   		'
   			<tr>
   						<td>'.$count.'</td>
                        <td>'.$created.'</td>
                        <td>'.$account_name.'</td>
                        <td>'.$status.'</td>
                        <td><a href="'.site_url().'delete-department-account/'.$account_id.'/'.$department_id.'" class="btn btn-xs btn-danger" onclick="return confirm(\'Do you really want to delete this account from department ? \');">Delete</a></td>
   			</tr> 
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
   	$result .= "There are no accounts allocated to this department";
   }
   
   // var_dump($query); die();
   ?>
<section class="panel">
   <header class="panel-heading">
      
      <h2 class="panel-title"><?php echo $title;?> <?php echo date('Y-m-d')?></h2>

   </header>
   
   <div class="panel-body">
   	<div class="row" style="margin-bottom:20px;">
        <div class="col-lg-12">
            <a href="<?php echo site_url();?>hospital-administration/departments" class="btn btn-info btn-sm pull-right">Back to departments</a>
        </div>
    </div>
    
      <?php
         $success = $this->session->userdata('success_message');
         
         if(!empty($success))
         {
         echo '<div class="alert alert-success"> <strong>Success!</strong> '.$success.' </div>';
         $this->session->unset_userdata('success_message');
         }
         
         $error = $this->session->userdata('error_message');
         
         if(!empty($error))
         {
         echo '<div class="alert alert-danger"> <strong>Oh snap!</strong> '.$error.' </div>';
         $this->session->unset_userdata('error_message');
         }
         ?>
         <?php echo form_open($this->uri->uri_string(), array("class" => "form-horizontal", "role" => "form"));?>
		   <div class="row">
		         <?php
		          $where_customer = 'account_id > 0 AND parent_account = (SELECT account_id FROM account WHERE account_name = "Expense Accounts") AND account_id NOT IN (SELECT account_id FROM department_account WHERE department_id = '.$department_id.' AND deleted = 0)';
		          $table_customer = 'account';
		          $value_transaction = '*';
		          $query_customer_customer = $this->dashboard_model->get_content($table_customer, $where_customer,$value_transaction);

		          $customer_details ='';

		          if($query_customer_customer->num_rows() > 0)
		          {
		              foreach ($query_customer_customer->result() as $key => $customer_v) {
		                  # code...
		                  $account_id = $customer_v->account_id;
		                  $account_name = $customer_v->account_name;
		                  $customer_details .='<option value="'.$account_id.'">'.$account_name.'</option>';
		              }
		          }

		          ?>
		        <div class="col-md-3">
		           <div class="form-group">
		              <!-- <label class="col-lg-4 control-label">Number: </label> -->
		              <div class="col-lg-8">
		                 <!-- <input type="text" class="form-control" name="card_number" placeholder="card number" value=""> -->
		                  <select name="account_id" class="form-control custom-select" id="card_number" >
		                      <option value="">----Select an account ----</option>
		                      <?php echo $customer_details?>
		                  </select>
		              </div>
		           </div>
		        </div>
				<div class="col-md-1">
					<div class="form-actions center-align">
			            <button class="submit btn btn-primary btn-sm" type="submit">
			            Add 
			            </button>
			         </div>
				</div>
		   </div>
		   <?php echo form_close();?>
     <hr>
      <div class="table-responsive">
         <?php echo $result;?>
      </div>
   </div>
   <div class="panel-body">
      <?php if(isset($links)){echo $links;}?>
   </div>
</section>
<script type="text/javascript" charset="utf-8">
    $(function() {
        $("#product_code").customselect();
        $("#card_number").customselect();
   
    });
</script>