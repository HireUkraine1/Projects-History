<?php 
class ControllerProductReviews extends Controller {
	public function index() {
		
		$this->data['breadcrumbs'] = array();
		
		$this->language->load('product/reviews');
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
       		'separator' => false
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('product/reviews'),
       		'separator' => $this->language->get('text_separator')
   		);
		
		$this->load->model('catalog/information');
		$information_info = $this->model_catalog_information->getInformation(12);
		
		if ($information_info) {
			if ($information_info['seo_title']) {
				$this->document->setTitle($information_info['seo_title']);
			} else {
				$this->document->setTitle($this->language->get('heading_title'));
			}
			$this->document->setDescription($information_info['meta_description']);
			$this->document->setKeywords($information_info['meta_keyword']);
			
			if ($information_info['seo_h1']) {
				$this->data['heading_title'] = $information_info['seo_h1'];
			} else {
				$this->data['heading_title'] = $this->language->get('heading_title');
			}
			
			$this->data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');
		}
		
		$this->data['text_empty'] = $this->language->get('text_empty');
		
		$this->load->model('catalog/review');
		
		$this->data['reviews'] = array();
		
		$results = $this->model_catalog_review->getAllReview();
		
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
		
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/reviews.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/product/reviews.tpl';
		} else {
			$this->template = 'default/template/product/reviews.tpl';
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