 <?php
      $invoice_where = 'order_supplier.order_id = '.$order_id.' AND order_supplier.order_item_id = order_item.order_item_id AND order_item.product_id = product.product_id';
      $invoice_table = 'order_supplier,product,order_item';
      $invoice_order = 'order_supplier.order_supplier_id';

      $invoice_query = $this->creditors_model->get_creditors_list($invoice_table, $invoice_where, $invoice_order);

      $result_payment ='<table class="table table-bordered table-striped table-condensed">
                          <thead>
                            <tr>
                              <th >#</th>
                              <th >Product</th>
                              <th >Total Amount</th>
                            </tr>
                          </thead>
                            <tbody>';
      $grand_amount = 0;
      $total_vat_amount = 0;
      if($invoice_query->num_rows() > 0)
      {
        $x = 0;

        foreach ($invoice_query->result() as $key => $row) {
          // code...
        	$product_id = $row->product_id;
			$product_name = $row->product_name;
			$product_status = $row->product_status;
			// $category_name = $row->product_category_name;
			$reorder_level = $row->reorder_level;
			$store_id = $row->store_id;
			// $opening_quantity = $row->opening_quantity;			
			$product_unitprice = $row->product_unitprice;
            $product_deleted = $row->product_deleted;
            // $creditor_name = $row->creditor_name;
            // $supplier_invoice_date = $row->supplier_invoice_date;
            // $supplier_invoice_number = $row->supplier_invoice_number;
            $quantity_received = $row->quantity_received;
            $pack_size = $row->pack_size;
            $unit_price = $row->unit_price;
			$total_amount = $row->less_vat;
			$vat = $row->vat;
			$discount = $row->discount;
            $selling_unit_price = $row->selling_unit_price;
			

			$units_received = $quantity_received * $pack_size;
			 if ($units_received == 0)
			 {


			$bp_unit = 0;
			 }
			 else
			 {

			$bp_unit = $unit_price / $units_received;
			 }

			//status
			if($product_status == 1)
			{
				$status = 'Active';
			}
			else
			{
				$status = 'Disabled';
			}

			
			$button = '';
			
			// $search_end_date = $supplier_invoice_date;

			
				$markup = round(($product_unitprice * 1.33), 0);
				$markdown = $markup;//round(($markup * 0.9), 0);


			$grand_amount += $total_amount;				

        

          $x++;
          // $result_payment .= form_open("accounts/update_invoice_item/".$creditor_invoice_item_id."/".$creditor_id, array("class" => "form-horizontal"));
 
  
          $result_payment .= '<tr>
                                  <td>'.$x.'</td>
                                  <td>'.$product_name.'</td>
                                  <td>'.number_format($total_amount,2).'</td>
                              </tr>';
          // $result_payment .=form_close();
        }
        $result_payment .= '<tr>
                                  <td colspan="2">Total Invoice</td>
                                  <td ><strong>'.number_format($grand_amount,2).'</strong></td>
                              </tr>';

        // display button

        $display = TRUE;
      }
      else {
        $display = FALSE;
      }

      $result_payment .='</tbody>
                      </table>';
      ?>

      <?php echo $result_payment;?>



       <?php
        

      $where = 'creditor_payment.creditor_payment_id = creditor_payment_item.creditor_payment_id AND creditor_payment.creditor_payment_status = 1 AND creditor_payment_item.invoice_type = 1 AND creditor_payment.account_from_id = account.account_id AND creditor_payment_item.creditor_invoice_id ='.$order_id;
      $table = 'creditor_payment,creditor_payment_item,account';
      $select = 'creditor_payment.*,SUM(creditor_payment_item.amount_paid) AS sum_paid,account.account_name';
      $group_by = 'creditor_payment.creditor_payment_id';


      $creditor_payments = $this->creditors_model->get_content($table, $where,$select,$group_by,$limit=NULL);

      $payment_result ='
                      <h4>Payments</h4>
                      <table class="table table-bordered table-striped table-condensed">
                          <thead>
                            <tr>
                              <th >#</th>
                               <th>Payment Date</th>
                                <th>Payment Account</th>
                                <th>Reference Number</th>
                                <th>Document Number</th>
                                <th>Total Amount</th>
                            </tr>
                          </thead>
                            <tbody>';
      $total_amount = 0;
      $total_vat_amount = 0;
       if($creditor_payments->num_rows() > 0)
                {
                  $y = 0;
                  foreach ($creditor_payments->result() as $key) {
                    # code...
                    $total_amount = $key->sum_paid;
                    $creditor_payment_id = $key->creditor_payment_id;
                    $transaction_date = $key->transaction_date;
                    $document_number = $key->document_number;
                    $reference_number = $key->reference_number;
                    $account_name = $key->account_name;
                    $created = $key->created;
                    $created_by = $key->created_by;

                   
                    $payment_explode = explode('-', $transaction_date);

                    $invoice_note_date = date('jS M Y',strtotime($transaction_date));
                    $created = date('jS M Y',strtotime($created));
                    $y++;

                    $payment_result .= '<tr >
                                          <td >'.$y.'</td>
                                          <td >'.$transaction_date.'</td>
                                          <td >'.$account_name.'</td>
                                          <td >'.$reference_number.'</td>
                                          <td >'.$document_number.'</td>
                                          <td >'.number_format($total_amount,2).'</td>
                                        </tr>';

            }
          }

      $payment_result .='</tbody>
                      </table>';
      ?>

      <?php echo $payment_result;?>