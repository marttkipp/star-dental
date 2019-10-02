 <section class="panel">
    <header class="panel-heading">
      <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?></h4>
      <div class="widget-icons pull-right">
        <a href="<?php echo site_url().'inventory/products';?>" class="btn btn-sm btn-default">Back to inventory</a>
        <a href="<?php echo site_url().'inventory/deduct-product/'.$product_id;?>" class="btn btn-sm btn-success">Deduct Product</a>
        </div>
      <div class="clearfix"></div>
    </header>             

        <!-- Widget content -->
                <div class="panel-body">
                <div class="padd">
                  <div class="center-align">
                    <?php
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
                    ?>
                  </div>
                  
                    <div class="row">
                        <div class="col-md-12">
                        	<div class="table-responsive">
                                <table border="0" class="table table-hover table-condensed">
                                    <thead> 
                                        <th>#</th>
                                      	<th>Sub Store</th>
                                        <th>Deduction Date</th>
                                        <th>Pack Size</th>
                                        <th>Quantity</th>
                                        <th></th>
                                    </thead>
                        
                                    <?php 
                                    //echo "current - ".$current_item."end - ".$end_item;
                                    $count = $page;
                                    $rs9 = $query->result();
                                    foreach ($rs9 as $rs10) :
                                        $deduction_date = $last_visit = date('jS M Y H:i:s',strtotime($rs10->product_deductions_date));
                                        $product_deduction_id = $rs10->product_deductions_id;
                                        $product_deduction_pack_size = $rs10->product_deductions_pack_size;
                                        $product_deduction_quantity = $rs10->product_deductions_quantity;
                                        $count++;
                                    ?>
                                   <tr>
                                        <td><?php echo $count;?></td>
                                        <td></td>
                                        <td><?php echo $deduction_date;?></td>	
                                        <td><?php echo $product_deduction_pack_size;?></td>
                                        <td><?php echo $product_deduction_quantity;?></td>
                                        <td><a href="<?php echo site_url().'inventory_management/edit_product_deduction/'.$product_deduction_id.'/'. $product_id;?>" class="btn btn-sm btn-primary">Edit</a></td>
                                    </tr>
                                    <?php endforeach;?>
                                </table>
                        	</div>
                        </div>
                    </div>
                
                </div>
            </div>
                
            <div class="widget-foot">
            <?php
            if(isset($links)){echo $links;}
            ?>
            </div>
         </div>
   </section>