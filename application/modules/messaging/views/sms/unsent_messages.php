<?php
		
		$result = '';
		$action_point = '';
		//if users exist display them
		if ($query->num_rows() > 0)
		{

			$count = $page;
			
			$result .= 
			'
			<table class="table table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th>#</th>
						<th>Phone Number</th>
						<th>Message</th>
						<th>Delivery Message</th>
					</tr>
				</thead>
				  <tbody>
				  
			';
			
			
			
			foreach ($query->result() as $row)
			{
				$message_id = $row->message_id;
				$message = $row->message;
				$phone_number = $row->phone_number;
				$delivery_message = $row->delivery_message;

		


				$count++;
				$result .= 
				'
					<tr>
						<td>'.$count.'</td>
						<td>'.$phone_number.'</td>
						<td>'.$message.'</td>
						<td>'.$delivery_message.'</td>
					</tr> 
				';

				// $action_point = '<div class="pull-right"><a class="btn btn-success btn-sm" href="'.site_url().'messaging/send-messages" style="margin-top: -5px;" onclick="return confirm(\'Do you want to send the messages ?\');" title="Send Message">Send Unsent Messages</a></div>';
			}
			
			$result .= 
			'
						  </tbody>
						</table>
			';
		}
		
		else
		{
			$result .= "There are no messages";
		}
?>

						<section class="panel">
							<header class="panel-heading">						
								<h2 class="panel-title"><?php echo $title;?> </h2>

							</header>
							<div class="panel-body">
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

								<?php
					                if(isset($import_response))
					                {
					                    if(!empty($import_response))
					                    {
					                        echo $import_response;
					                    }
					                }
					                
					                if(isset($import_response_error))
					                {
					                    if(!empty($import_response_error))
					                    {
					                        echo '<div class="center-align alert alert-danger">'.$import_response_error.'</div>';
					                    }
					                }
					            ?>
                            	
                                
								<div class="table-responsive">
                                	
									<?php echo $result;?>
							
                                </div>
							</div>
                            <div class="panel-footer">
                            	<?php if(isset($links)){echo $links;}?>
                            </div>
						</section>