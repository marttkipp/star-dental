<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    require_once "./application/modules/assets/controllers/assets.php";
    
    class Asset_Category extends assets
    {
    	function __construct()
    	{
    		parent:: __construct();
    		
    
    	}
        
    	/*
    	*
    	*	Default action is to show all the sections
    	*
    	*/
    	public function index() 
    	{
    		$where = 'asset_category_id > 0';
    		$table = 'asset_category';
    		$order = 'asset_category_name';
    		$order_method = 'ASC';
    		//pagination
    		$segment = 3;
    		$this->load->library('pagination');
    		$config['base_url'] = site_url().'asset-registry/asset-category';
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
    		$query = $this->assets_category_model->get_all_asset_category($table, $where, $config["per_page"], $page, $order, $order_method);
    		
    		//change of order method 
    		if($order_method == 'DESC')
    		{
    			$order_method = 'ASC';
    		}
    		
    		else
    		{
    			$order_method = 'DESC';
    		}
    		
    		$data['title'] = 'Asset Category';
    		$v_data['title'] = $data['title'];
    		
    		$v_data['order'] = $order;
    		$v_data['order_method'] = $order_method;
    		$v_data['query'] = $query;
    		$v_data['page'] = $page;
    		$data['content'] = $this->load->view('all_asset_category', $v_data, true);
    		
    		$this->load->view('admin/templates/general_page', $data);
    	}
        public function add_asset_category() 
    	{
    		//form validation rules
    		$this->form_validation->set_rules('asset_category_name', 'Asset Category', 'required|xss_clean');
    		//$this->form_validation->set_rules('asset_category_parent', 'Category Parent', 'required|xss_clean');
    		
    		//if form has been submitted
    		if ($this->form_validation->run())
    		{
    			//upload product's gallery images
    			
    			if($this->assets_category_model->add_asset_category_detail())
    			{
    				$this->session->set_userdata('success_message', 'Category added successfully');
    				redirect('asset-registry/asset-category');
    			}
    			
    			else
    			{
    				$this->session->set_userdata('error_message', 'Could not add category. Please try again');
    			}
    		}
    		
    		//open the add new category
    		$data['title'] = 'Add New Asset';
    		$v_data['title'] = 'Add New Asset ';
    		
    		$data['content'] = $this->load->view('add_asset_category', $v_data, true);
    		$this->load->view('admin/templates/general_page', $data);
    }
    
     
		
		

        public function edit_asset_category($asset_category_id) 
    	{
    		//form validation rules
    		$this->form_validation->set_rules('asset_category_name', 'Asset Name', 'required|xss_clean');
    		//$this->form_validation->set_rules('asset_category_status', 'Asset Status', 'required|xss_clean');
    		
    		//if form has been submitted
    		if ($this->form_validation->run())
    		{
    			
    			if($this->assets_category_model->update_asset_category($asset_category_id))
    			{
    				$this->session->set_userdata('success_message', 'Category updated successfully');
    				redirect('asset-registry/asset-category');
    			}
    			
    			else
    			{
    				$this->session->set_userdata('error_message', 'Could not update category. Please try again');
    			}
    		}
    		
    		//open the add new category
    		$data['title'] = 'Edit Asset Category';
    		$v_data['title'] = 'Edit Asset Category';

    		
    		//select the category from the database
    		$query = $this->assets_category_model->get_asset_category($asset_category_id);
    		$v_data['asset_category'] = $query->result();

    		//var_dump($query);die();
    		
    		if ($query->num_rows() > 0)
    		{
    			$v_data['asset_category'] = $query->result();
    			//$v_data['all_parent_categories'] = $this->categories_model->all_parent_categories();
    			
    			$data['content'] = $this->load->view('edit_asset_category', $v_data, true);
    		}
    		
    		else
    		{
    			$data['content'] = 'Asset does not exist';
    		}
    		
    		$this->load->view('admin/templates/general_page', $data);
    	}
    
      public function delete_asset_category($asset_category_id)
        	
        {
        		//delete asset category
         $query = $this->assets_category_model->get_asset_category($asset_category_id);
        		
        		if ($query->num_rows() > 0)
        		{
        			$result = $query->result();
        			
        		}
        		$this->assets_category_model->delete_asset_category($asset_category_id);
        		$this->session->set_userdata('success_message', 'Category has been deleted');
        		redirect('asset-registry/asset-category');
        	}
    
       public function activate_asset_category($asset_category_id)
    	{
    		$this->assets_category_model->activate_asset_category($asset_category_id);
    		$this->session->set_userdata('success_message', 'Asset Category activated successfully');
    		redirect('asset-registry/asset-category');
    	} 
    
    	public function deactivate_asset_category($asset_category_id)
    	{
    		if($this->assets_category_model->deactivate_asset_category($asset_category_id))
    		{
    			$this->session->set_userdata('success_message', 'Asset Category disabled successfully');
    			redirect('asset-registry/asset-category');
    		} 		
    
    	}		
   
    
        public function search_asset_categories()
    	
    	{
    
    		$asset_category_name = $this->input->post('asset_category_name');
    
    
    		if(!empty($category_name))
    		{
    			$asset_category_name = ' AND asset_category.asset_category_name LIKE \'%'.mysql_real_escape_string($asset_category_name).'%\' ';
    		}
    		
    		
    		$search = $asset_category_name;
    		$this->session->set_userdata('asset_category_search', $search);
    		
    		$this->index();
    		
    	}
    	public function close_asset_categories_search()
    	{
    		$this->session->unset_userdata('asset_category_search');
    		redirect('asset-registry/asset-category');
    	}
    
    }
        
    
    ?>
