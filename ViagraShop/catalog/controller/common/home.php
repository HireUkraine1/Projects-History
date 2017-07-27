<?php  
class ControllerCommonHome extends Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));

		$this->data['heading_title'] = $this->config->get('config_title');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/home.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/home.tpl';
		} else {
			$this->template = 'default/template/common/home.tpl';
		}
		
		$this->language->load('common/home');
		$this->load->model('catalog/information');
		
		$information_info = $this->model_catalog_information->getInformation(11);
		
		$this->load->model('tool/image');
		$this->load->model('catalog/product');
		
		$this->data['button_cart'] = $this->language->get('button_cart');
		$this->data['text_our_products'] = $this->language->get('text_our_products');
		$this->data['text_from'] = $this->language->get('text_from');
		
		$this->data['products'] = array();
		
		$data = array(
			'filter_category_id' => 59
		);
		
		$results = $this->model_catalog_product->getProducts($data);
		
		foreach ($results as $result) {
				
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				}
				
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}
				
				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$special = false;
				}	
				
				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price']);
				} else {
					$tax = false;
				}				
				
				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}
				
				$opt_count = $this->model_catalog_product->getProductsOptionCount($result['product_id']);
								
				$this->data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'href'        => $this->url->link('product/product', 'path=59&product_id=' . $result['product_id']),
					'opt_count'   => $opt_count
				);
		}
		
		$this->data['banner2'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');
		
		$this->load->model('catalog/review');
		
		$this->data['reviews'] = array();
		
		$results = $this->model_catalog_review->getAllReview(5);
		
		foreach ($results as $result) {
        	$this->data['reviews'][] = array(
        		'author'     => $result['author'],
				'text'       => $result['text'],
				'rating'     => (int)$result['rating'],
        		'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'name'       => $result['name'],
				'href'       => $this->url->link('product/product', 'product_id=' . $result['product_id'])
        	);
      	}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
										
		$this->response->setOutput($this->render());
	}
}
?>