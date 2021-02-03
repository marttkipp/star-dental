<?php
$accounts = $this->budget_model->get_all_parent_expense_accounts();

$changed = '';
// $current_parent = 0;
// $parent_account = 0;

$month = $this->budget_model->get_month();
 if($accounts->num_rows() > 0)
 {
 	$count = 0;
     foreach($accounts->result() as $row):
         // $company_name = $row->company_name;
         $account_name = $row->account_name;
         $account_id = $row->account_id;
         $parent_account = $row->parent_account;

         $child_accounts = $this->budget_model->get_all_child_expense_accounts($account_id);
         // var_dump($child_accounts->num_rows());die();
         $changed .= '<tr class="success">
                            <td colspan="2">'.strtoupper($account_name).'</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <th>Amount</th>
                        </tr>
                        <tbody>';
         
         $one = 0;
         $two = 0;
         $three = 0;
         $four = 0;
         $five = 0;
         $six = 0;
         $seven = 0;
         $eight = 0;
         $nine=0;
         $ten = 0;
         $eleven = 0;
         $twelve = 0;
       
         if($child_accounts->num_rows() > 0)
         {
         		$month_items = "";
            foreach ($child_accounts->result() as $key => $value) {
                # code...
                 $child_account_name = $value->account_name;
                 $child_account_id = $value->account_id;
             

                // if($child_account_name == "SALARIES/WAGES")
                // {
                //     $month_value =  $this->budget_model->get_actual_salary_expenses($budget_year,$month_id);
                // }
                // else if($child_account_name == "NSSF")
                // {
                //     $month_value =  $this->budget_model->get_actual_nssf_expenses($budget_year,$month_id);
                // }
                // else if($child_account_name == "NHIF")
                // {
                //     $month_value =  $this->budget_model->get_actual_nhif_expenses($budget_year,$month_id);
                // }
                // else if($child_account_name == "PAYE")
                // {
                //     $month_value =  $this->budget_model->get_actual_paye_expenses($budget_year,$month_id);
                // }
                // else
                // {
                    $month_value = $this->budget_model->get_total_amount_summary_actual($budget_year,$child_account_id);
                // }
                

                $twelve += $month_value;


                // $month_items .= '<td>'.number_format($month_value,2).'</td>';
                 
               
                 $changed .= ' <tr>
                                    <td>'.strtoupper($child_account_name).'</td>
                                     <td>'.number_format($month_value,2).'</td>
                                </tr>';
        
            }

            $changed .= '<tr class="primary">
                            <td>'.strtoupper($account_name).' SUBTOTAL </td>
                            <td>'.number_format($twelve,2).'</td>
                        </tr>
                        </tbody>';

         }

         $count++;

     	 
     	
     endforeach;
 }




?>
<table class="table table-condensed table-bordered" id="testTable">
	
		<?php echo $changed;?>
		
	
</table>