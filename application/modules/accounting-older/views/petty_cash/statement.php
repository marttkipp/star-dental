<!-- search -->
<?php echo $this->load->view('search/petty_cash_search', '', TRUE);

?>
<!-- end search -->
<!--begin the reports section-->
<?php
//unset the sessions set\
$search = $this->session->userdata('accounts_petty_search');
$search_title = $this->session->userdata('accounts_search_title');//echo $account;die();
if(!empty($account))
{
	//get account balance
	$opening_bal = $this->petty_cash_model->get_account_opening_bal($account);
}
else
{
	$opening_bal = $this->petty_cash_model->get_total_opening_bal();
}

?>
<!--end reports -->
<div class="row">
    <div class="col-md-12">

        <section class="panel">
            <header class="panel-heading">
                
                <h2 class="panel-title"><?php echo $title;?></h2>
                <a href="<?php echo base_url().'accounting/petty_cash/print_petty_cash';?>" class="btn btn-sm btn-warning pull-right" target="_blank" style="margin-top: -25px; margin-left: 5px"><i class="fa fa-print"></i> Print</a>
                 <a  class="btn btn-sm btn-success pull-right" id="open_visit" onclick="get_visit_trail();" style="margin-top: -25px;">Add Record</a>
                <a  class="btn btn-sm btn-default pull-right" id="close_visit" style="display:none;" onclick="close_visit_trail();" style="margin-top: -25px; margin-right: 5px;">Close </a></td>
            </header>
            
            <div class="panel-body">
            
                <div id="visit_trail" style="display: none">
                    <?php echo form_open("accounting/petty_cash/record_petty_cash", array("class" => "form-horizontal"));?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Transaction date: </label>
                                        
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                                <input data-format="yyyy-MM-dd" type="text" data-plugin-datepicker class="form-control" name="petty_cash_date" placeholder="Transaction date" value="<?php echo date('Y-m-d');?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Department *</label>
                                        
                                        <div class="col-md-8">
                                            <select class="form-control" name="department_id" id="department_id" onchange="get_department_expense(this.value)">
                                                <option value="0">-- Select a department --</option>
                                                <?php
                                                if($departments->num_rows() > 0)
                                                {
                                                    foreach($departments->result() as $res)
                                                    {
                                                        $department_id = $res->department_id;
                                                        $department_name = $res->department_name;
                                                        ?>
                                                        <option value="<?php echo $department_id;?>"><?php echo $department_name;?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="account_from_id" value="<?php echo $account?>">
                                    <input type="hidden" name="transaction_type_id" value="1">
                                    <div class="form-group" >
                                    <label class="col-md-4 control-label">Account To*</label>
                                    
                                    <div class="col-md-8">
                                        <select class="form-control" name="account_to_id" id="account_to_id">
                                           
                                        </select>
                                    </div>
                                </div>
                              
                                   
                                   
                            </div>
                            <div class="col-md-6">
                                
                                
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Description *</label>
                                    
                                    <div class="col-md-8">
                                        <textarea class="form-control" name="petty_cash_description"></textarea>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Amount *</label>
                                    
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="petty_cash_amount" placeholder="Amount"/>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="center-align">
                                    <button type="submit" class="btn btn-sm btn-primary">Save record</button>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                     <?php echo form_close();?>
                     <hr>
                    
                </div>
            	
                <!-- Modal -->
                <div class="modal fade" id="record_petty_cash" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Record Petty Cash</h4>
                            </div>
                            <div class="modal-body">
                               
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
			<?php
			if(!empty($search))
			{
				?>
                <a href="<?php echo base_url().'accounting/petty_cash/close_search';?>" class="btn btn-sm btn-success"><i class="fa fa-print"></i> Close Search</a>
                <?php
			}
			$error = $this->session->userdata('error_message');
			$success = $this->session->userdata('success_message');
			
			if(!empty($error))
			{
				echo '<div class="alert alert-danger">'.$error.'</div>';
				$this->session->unset_userdata('error_message');
			}
			
			if(!empty($success))
			{
				echo '<div class="alert alert-success">'.$success.'</div>';
				$this->session->unset_userdata('success_message');
			}
					
			$result =  '';
		
			// echo $result;

			$statement_result = $this->petty_cash_model->get_petty_cash_statement($account);

			
?>			<table class="table table-hover table-bordered ">
				 	<thead>
						<tr>
						  <th>Transaction Date</th>						  
						  <th>Account</th>
						  <th>Description</th>
						  <th>Money In</th>
						  <th>Money Out</th>	
                          <th>Balance</th>   					
						</tr>
					 </thead>
				  	<tbody>
				  		<?php  echo $statement_result['result'];?>
					</tbody>
				</table>

          	</div>
		</section>
    </div>
</div>

<script type="text/javascript">
	function get_visit_trail(){

        var myTarget2 = document.getElementById("visit_trail");
        var button = document.getElementById("open_visit");
        var button2 = document.getElementById("close_visit");

        myTarget2.style.display = '';
        button.style.display = 'none';
        button2.style.display = '';
    }
    function close_visit_trail(){

        var myTarget2 = document.getElementById("visit_trail");
        var button = document.getElementById("open_visit");
        var button2 = document.getElementById("close_visit");

        myTarget2.style.display = 'none';
        button.style.display = '';
        button2.style.display = 'none';
    }
    function get_department_expense(department_id)
    {
        // alert(department_id);

        var url = "<?php echo site_url();?>accounting/petty_cash/get_department_accounts/"+department_id;  
        // alert(url);
        //get department services
        $.get( url, function( data ) 
        {
            $( "#account_to_id" ).html( data );
            // $(".custom-select").customselect();
        });
    }
	
	$(document).on("change","select#transaction_type_id",function(e)
	{
		var transaction_type_id = $(this).val();
		
		if(transaction_type_id == '1')
		{
			// deposit
			$('#from_account_div').css('display', 'block');
			$('#account_to_div').css('display', 'block');
			// $('#consultation').css('display', 'block');
		}
		else if(transaction_type_id == '2')
		{
			// expenditure
			$('#from_account_div').css('display', 'block');
			$('#account_to_div').css('display', 'none');
			// $('#consultation').css('display', 'block');
		}
		else
		{
			$('#from_account_div').css('display', 'none');
			$('#account_to_div').css('display', 'none');
		}
		
		
	});
</script>