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
                            <td colspan="13">'.strtoupper($account_name).'</td>
                        </tr>';
         
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
            foreach ($child_accounts->result() as $key => $value) {
                # code...
                 $child_account_name = $value->account_name;
                 $child_account_id = $value->account_id;
                $month_items = '';
                if($month->num_rows() > 0){
                    foreach ($month->result() as $row):
                        $mth = $row->month_name;
                        $mth_id = $month_id = $row->month_id;
                        if($mth_id < 10)
                        {
                            $mth_id = '0'.$mth_id;
                        }

                        
                        $month_value = $this->budget_model->get_total_amount_sum($budget_year,$mth_id,$child_account_id);


                        if($mth_id == "01")
                        {
                            $one += $month_value;
                        }
                        else  if($mth_id == "02")
                        {
                            $two += $month_value;
                        }
                         else  if($mth_id == "03")
                        {
                            $three += $month_value;
                        }
                         else  if($mth_id == "04")
                        {
                            $four += $month_value;
                        }
                         else  if($mth_id == "05")
                        {
                            $five += $month_value;
                        }
                         else  if($mth_id == "06")
                        {
                            $six += $month_value;
                        }
                         else  if($mth_id == "07")
                        {
                            $seven += $month_value;
                        }
                         else  if($mth_id == "08")
                        {
                            $eight += $month_value;
                        }
                         else  if($mth_id == "09")
                        {
                            $nine += $month_value;
                        }
                         else  if($mth_id == "10")
                        {
                            $ten += $month_value;
                        }
                         else  if($mth_id == "11")
                        {
                            $eleven += $month_value;
                        }
                        else  if($mth_id == "12")
                        {
                            $twelve += $month_value;
                        }

                        $month_items .= '<td >'.number_format($month_value,2).'</td>';
                    endforeach;
                }
               
                 $changed .= ' <tr>
                                    <td>'.strtoupper($child_account_name).'</td>
                                     '.$month_items.'
                                </tr>';
        
            }

            $changed .= '<tr class="primary">
                            <td>'.strtoupper($account_name).' SUBTOTAL </td>
                            <td>'.number_format($one,2).'</td>
                            <td>'.number_format($two,2).'</td>
                            <td>'.number_format($three,2).'</td>
                            <td>'.number_format($four,2).'</td>
                            <td>'.number_format($five,2).'</td>
                            <td>'.number_format($six,2).'</td>
                            <td>'.number_format($seven,2).'</td>
                            <td>'.number_format($eight,2).'</td>
                            <td>'.number_format($nine,2).'</td>
                            <td>'.number_format($ten,2).'</td>
                            <td>'.number_format($eleven,2).'</td>
                            <td>'.number_format($twelve,2).'</td>
                        </tr>';

         }

         $count++;

     	 
     	
     endforeach;
 }




?>
<table class="table table-condensed table-bordered" id="testTable">
	<thead>
		<th>Name</th>
		<th>JAN</th>
		<th>FEB</th>
		<th>MAR</th>
		<th>APR</th>
		<th>MAY</th>
		<th>JUN</th>
		<th>JUL</th>
		<th>AUG</th>
		<th>SEPT</th>
		<th>OCT</th>
		<th>NOV</th>
		<th>DEC</th>
	</thead>
	<tbody>
		<?php echo $changed;?>
		
	</tbody>
</table>