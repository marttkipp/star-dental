<?php echo $this->load->view('search/expense_search', '', TRUE);?>
 <!-- <div class="row">
	<div class="col-md-12">
		<div class="pull-right">
		 <a href="<?php echo site_url()?>/administration/new_expense" class="btn btn-sm btn-success">Add a New Expense </a>

		</div>
	</div>
</div> -->
<?php echo $this->load->view('dashboard/expense_summary', '', TRUE);?>
<div class="row">
    <div class="col-md-12">

      <!-- Widget -->
      <div class="widget boxed">
        <!-- Widget head -->
        <div class="widget-head">
          <h4 class="pull-left"><i class="icon-reorder"></i><?php echo $title;?> </h4>
          <div class="widget-icons pull-right">
            <a href="#" class="wminimize"><i class="icon-chevron-up"></i></a> 
            <a href="#" class="wclose"><i class="icon-remove"></i></a>
          </div>
          <div class="clearfix"></div>
        </div>             

        <!-- Widget content -->
        <div class="widget-content">
          <div class="padd">
          
<?php
		$error = $this->session->userdata('service_error_message');
		$success = $this->session->userdata('service_success_message');
		
		if(!empty($success))
		{
			echo '
				<div class="alert alert-success">'.$success.'</div>
			';
			$this->session->unset_userdata('service_success_message');
		}
		
		if(!empty($error))
		{
			echo '
				<div class="alert alert-danger">'.$error.'</div>
			';
			$this->session->unset_userdata('service_error_message');
		}
		$search = $this->session->userdata('expense_search');
		
		if(!empty($search))
		{
			echo '<a href="'.site_url().'administration/close_expense_search/'.$module.'" class="btn btn-warning">Close Search</a>';
		}
		$result = '';
		
		//if users exist display them
		if ($query->num_rows() > 0)
		{
			$count = $page;
			
			$result .= 
				'
					<table class="table table-hover table-bordered ">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Recepient Name</th>
						  <th>Amount</th>
						  <th>Recepient</th>
						  <th>Reason</th>
						  <th>Date of issue</th>
						  <th>Date recorded</th>
						  <th colspan="3">Actions</th>
						</tr>
					  </thead>
					  <tbody>
				';
			
			foreach ($query->result() as $row)
			{
				
				$expenses_id = $row->expenses_id;
				$reason = $row->reason;
				$recepient = $row->recipient;
				$amount = $row->amount;
				$date = $row->date;
				$dateofissue = $row->dateofissue;
				$expense_status = $row->expense_status;
				
				$count++;
				$result .= 
					'
						<tr>
							<td>'.$count.'</td>
							<td>'.$recepient.'</td>
							<td>'.$amount.'</td>
							<td>'.$recepient.'</td>
							<td>'.$reason.'</td>
							<td>'.$date.'</td>
							<td>'.$dateofissue.'</td>
							<td><a href="'.site_url().'administration/edit_expense/'.$expenses_id.'" class="btn btn-sm btn-info"> Edit </a></td>
							<td><a href="'.site_url().'administration/delete_service/'.$expenses_id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'Do you really want to delete this service?\'"> Delete </a></td>
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
			$result .= "There are no services";
		}
		
		echo $result;
?>
          </div>
          
          <div class="widget-foot">
                                
				<?php if(isset($links)){echo $links;}?>
            
                <div class="clearfix"></div> 
            
            </div>
        </div>
        <!-- Widget ends -->

      </div>
    </div>
  </div>