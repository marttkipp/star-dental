<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Assets extends MX_Controller 
{
	function __construct()
	{
		parent:: __construct();

       $this->load->model('assets_category_model');
       $this->load->model('admin/users_model');
       $this->load->model('site/site_model');
       $this->load->model('admin/sections_model');
       $this->load->model('admin/admin_model');
       $this->load->model('assets/assets_model');
        $this->load->model('hr/personnel_model');


    }

  public function index() 
    	{
    		$where = 'asset_id > 0 AND assets_details.asset_category_id = asset_category.asset_category_id';
    		$table = 'assets_details,asset_category';
    		$order = 'asset_category_name,asset_name';
    		$order_method = 'ASC';

            $search = $this->session->userdata('asset_search');      
            $where .= $search;      
    		//pagination
    		$segment = 3;
    		$this->load->library('pagination');
    		$config['base_url'] = site_url().'asset-registry/assets';
    		$config['total_rows'] = $this->users_model->count_items($table, $where);
    		$config['uri_segment'] = $segment;
    		$config['per_page'] = 20;
    		$config['num_links'] = 5;
    		
    		$config['full_tag_open'] = '<ul class="pagination pull-right">';
    		$config['full_tag_close'] = '</ul>';
    		
    		$config['first_tag_open'] = '<li>';
    		$config['first_tag_close'] = '</li>';
    		
    		$config['last_tag_open'] = '<li>';
    		$config['last_tag_close'] = '</li>';
    		
    		$config['next_tag_open'] = '<li>';
    		$config['next_link'] = 'Next';
    		$config['next_tag_close'] = '</span>';
    		
    		$config['prev_tag_open'] = '<li>';
    		$config['prev_link'] = 'Prev';
    		$config['prev_tag_close'] = '</li>';
    		
    		$config['cur_tag_open'] = '<li class="active"><a href="#">';
    		$config['cur_tag_close'] = '</a></li>';
    		
    		$config['num_tag_open'] = '<li>';
    		$config['num_tag_close'] = '</li>';
    		$this->pagination->initialize($config);
    		
    		$page = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
            $v_data["links"] = $this->pagination->create_links();
    		$query = $this->assets_model->get_all_asset($table, $where, $config["per_page"], $page, $order, $order_method);
    		
    		//change of order method 
    		if($order_method == 'DESC')
    		{
    			$order_method = 'ASC';
    		}
    		
    		else
    		{
    			$order_method = 'DESC';
    		}
    		$v_data['all_categories'] = $this->assets_category_model->get_asset_category();
    		$data['title'] = 'Sections';
    		$v_data['title'] = $data['title'];
    		
    		$v_data['order'] = $order;
    		$v_data['order_method'] = $order_method;
    		$v_data['query'] = $query;
    		$v_data['page'] = $page;
    		$data['content'] = $this->load->view('asset/all_asset', $v_data, true);
    		
    		$this->load->view('admin/templates/general_page', $data);
    	}

        public function add_asset() 
    	{
    		//form validation rules
    		$this->form_validation->set_rules('asset_name', 'Title', 'xss_clean');
    		$this->form_validation->set_rules('asset_serial_no', 'Serial number', 'xss_clean');
    		$this->form_validation->set_rules('asset_description', 'Description', 'required|xss_clean');
    		$this->form_validation->set_rules('asset_model_no', 'Model number', 'xss_clean');
    		$this->form_validation->set_rules('asset_pd_period', 'Purcahse date period', 'xss_clean');
    		$this->form_validation->set_rules('ldl_type', 'ldl type', 'xss_clean');
    		$this->form_validation->set_rules('ldl_date', 'ldl date', 'xss_clean');
    		$this->form_validation->set_rules('asset_supplier_no', 'Supplier number', 'xss_clean');
    		$this->form_validation->set_rules('asset_project_no', 'Project number', 'xss_clean');
    		$this->form_validation->set_rules('asset_owner_name', 'Owner name', 'xss_clean');
    		$this->form_validation->set_rules('asset_inservice_period', 'Inservice period', 'xss_clean');
    		$this->form_validation->set_rules('asset_disposal_period', 'Disposal period', 'xss_clean');
            $this->form_validation->set_rules('asset_number', 'Number', 'xss_clean');
    		$this->form_validation->set_rules('asset_category_id', 'Asset Category', 'xss_clean');
    		$this->form_validation->set_rules('asset_number', ' Number', 'xss_clean');

            $depreciation_type = $this->input->post('depriciation_type');


            if($depreciation_type == 1)
            {
                $this->form_validation->set_rules('usefull_life', 'Usefull Life', 'required|xss_clean');
                $this->form_validation->set_rules('salvage_value', 'Salvage', 'required|xss_clean');
            }
            else if($depreciation_type == 2)
            {
                $this->form_validation->set_rules('installment', 'Usefull Life', 'xss_clean');
                $this->form_validation->set_rules('rate', 'rate', 'required|number|xss_clean');
                $this->form_validation->set_rules('salvage', 'Salvage', 'required|numeric|xss_clean');
            }



    		
    		//if form has been submitted
    		if ($this->form_validation->run())
    		{
    			//upload product's gallery images
    			
    			if($this->assets_model->add_asset_details())
    			{
    				$this->session->set_userdata('success_message', 'Category added successfully');
    				redirect('asset-registry/assets');
    			}
    			
    			else
    			{
    				$this->session->set_userdata('error_message', 'Could not add category. Please try again');
    			}
    		}
    		
    		//open the add new category
    		$data['title'] = 'Add New Asset';
    		$v_data['title'] = 'Add New Asset ';
    		$v_data['all_categories'] = $this->assets_category_model->get_asset_category();
    		
    		$data['content'] = $this->load->view('asset/add_asset', $v_data, true);
    		$this->load->view('admin/templates/general_page', $data);
        }


        public function edit_asset($asset_id) 
    	{
    		//form validation rules
       		$this->form_validation->set_rules('asset_name', 'Title', 'required|xss_clean');
    		$this->form_validation->set_rules('asset_serial_no', 'Serial number', 'xss_clean');
    		$this->form_validation->set_rules('asset_description', 'Asset Description', 'required|xss_clean');
    		$this->form_validation->set_rules('asset_model_no', 'Model number', 'xss_clean');
    		$this->form_validation->set_rules('asset_pd_period', 'Pd number', 'required|xss_clean');
    		$this->form_validation->set_rules('ldl_type', 'ldl type', 'xss_clean');
    		$this->form_validation->set_rules('ldl_date', 'ldl date', 'xss_clean');
    		$this->form_validation->set_rules('asset_supplier_no', 'Supplier number', 'xss_clean');
    		$this->form_validation->set_rules('asset_project_no', 'Project number', 'xss_clean');
    		// $this->form_validation->set_rules('asset_owner_name', 'Owner name', 'required|xss_clean');
    		$this->form_validation->set_rules('asset_inservice_period', 'Inservice period', 'xss_clean');
    		$this->form_validation->set_rules('asset_disposal_period', 'Disposal period', 'xss_clean');
            $this->form_validation->set_rules('asset_number', 'Number', 'xss_clean');


            $depreciation_type = $this->input->post('depriciation_type');


            if($depreciation_type == 1)
            {
                $this->form_validation->set_rules('usefull_life', 'Usefull Life', 'required|xss_clean');
                $this->form_validation->set_rules('salvage_value', 'Salvage', 'required|xss_clean');
            }
            else if($depreciation_type == 2)
            {
                $this->form_validation->set_rules('installment', 'Usefull Life', 'xss_clean');
                $this->form_validation->set_rules('rate', 'rate', 'required|number|xss_clean');
                $this->form_validation->set_rules('salvage', 'Salvage', 'required|numeric|xss_clean');
            }

    		
    		//if form has been submitted
    		if ($this->form_validation->run())
    		{
    			
    			if($this->assets_model->update_asset($asset_id))
    			{
    				$this->session->set_userdata('success_message', 'Category updated successfully');
    				redirect('asset-registry/assets');
    			}
    			
    			else
    			{
    				$this->session->set_userdata('error_message', 'Could not update category. Please try again');
    			}
    		}
    		
    		//open the add new category
    		$data['title'] = 'Edit Asset';
    		$v_data['title'] = 'Edit Asset';
    	    $v_data['all_categories'] = $this->assets_category_model->get_asset_category();



    		
    		//select the category from the database
    		$query = $this->assets_model->get_asset($asset_id);
    		$v_data['assets_details'] = $query->result();

    		//var_dump($query);die();
    		
    		if ($query->num_rows() > 0)
    		{
    			$v_data['assets_details'] = $query->result();
    			//$v_data['all_parent_categories'] = $this->categories_model->all_parent_categories();
    			
    			$data['content'] = $this->load->view('asset/edit_asset', $v_data, true);
    		}
    		
    		else
    		{
    			$data['content'] = 'Asset does not exist';
    		}
    		
    		$this->load->view('admin/templates/general_page', $data);
    	}  


      public function delete_asset($asset_id)
        	
        {
        		//delete category image
        		$query = $this->assets_model->get_asset($asset_id);
        		
        		if ($query->num_rows() > 0)
        		{
        			$result = $query->result();
        			
        		}
        		$this->assets_model->delete_asset($asset_id);
        		$this->session->set_userdata('success_message', 'Asset has been deleted');
        		redirect('asset-registry/assets');
        }
    
       public function activate_asset($asset_id)
    	{
    		$this->assets_model->activate_asset($asset_id);
    		$this->session->set_userdata('success_message', 'Asset activated successfully');
    		redirect('asset-registry/assets');
    	} 
    
    	public function deactivate_asset($asset_id)
    	{
    		$this->assets_model->deactivate_asset($asset_id);
    		$this->session->set_userdata('success_message', 'Asset disabled successfully');
    		redirect('asset-registry/assets');
    
    	}		
    
    
        public function search_asset()    	
    	{
            $asset_name = $this->input->post('asset_name');
            $asset_category_id = $this->input->post('asset_category_id');
        
    
    		if(!empty($asset_name))
    		{
    			$asset_name =' AND assets_details.asset_name LIKE \'%'.mysql_real_escape_string($asset_name).'%\' ';
    		}

            if(!empty($asset_category_id))
            {
                $asset_category_id =' AND assets_details.asset_category_id = '.$asset_category_id;
            }
    		
    		
    		$search = $asset_name.$asset_category_id;

            // var_dump($search); die();
    		$this->session->set_userdata('asset_search', $search);
    		
    		redirect('asset-registry/assets');
    		
    	}
    	public function close_asset()
    	{
    		$this->session->unset_userdata('asset_search');
    		redirect('asset-registry/assets');
    	}


        public function calculate_amortization($loans_plan_id, $no_of_repayments,$proposed_amount,$actual_application_date,$salvage_value,$rate=0)
        {

               
            $v_data['loan_amount'] = $proposed_amount;
            $v_data['no_of_repayments'] = $no_of_repayments;
            $v_data['first_date'] = $actual_application_date;
            $v_data['interest_id'] = $loans_plan_id; // type of depreciation
            $v_data['interest_rate'] = $rate; // the rate of calculation
            $v_data['salvage_value'] = $salvage_value;
            $v_data['save'] = 1;

                    

              

            echo $this->load->view('get_amortization_table', $v_data, true);

            //$v_data['individual_loan_id'] = 1;
            //$v_data['individual_id'] = 95;
            
        }





}





?>