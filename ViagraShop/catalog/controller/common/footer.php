<?php  
class ControllerCommonFooter extends Controller {
	protected function index() {
		
		$this->language->load('common/footer');
		
		$this->data['name'] = $this->config->get('config_name');
		$this->data['adress'] = $this->config->get('config_address');
		$this->data['powered'] = $this->language->get('text_powered');
		
		$this->data['text_fb'] = $this->language->get('text_fb');
		$this->data['text_tw'] = $this->language->get('text_tw');
		$this->data['text_gp'] = $this->language->get('text_gp');
		
		$this->data['account'] = $this->url->link('account/account', '', 'SSL');
		
		$this->data['text_working_time'] = $this->language->get('text_working_time');
		
		$this->data['text_menu'] = $this->language->get('Menu');
		$this->data['text_working'] = $this->language->get('Working hours');
		$this->data['text_adress'] = $this->language->get('Address');
		$this->data['text_social'] = $this->language->get('Social');
		
		$this->data['menu_item1'] = $this->language->get('menu_item1');
		$this->data['menu_item2'] = $this->language->get('menu_item2');
		$this->data['menu_item3'] = $this->language->get('menu_item3');
		$this->data['menu_item4'] = $this->language->get('menu_item4');
		$this->data['menu_item5'] = $this->language->get('menu_item5');
		$this->data['menu_item6'] = $this->language->get('menu_item6');
		$this->data['menu_item7'] = $this->language->get('menu_item7');
		
		$this->data['text_site_name'] = $this->language->get('text_site_name');
	
		$this->data['menu_item2_link'] = $this->url->link('information/information', 'information_id=7');
		$this->data['menu_item3_link'] = $this->url->link('information/information', 'information_id=8');
		$this->data['menu_item4_link'] = $this->url->link('information/information', 'information_id=9');
		$this->data['menu_item5_link'] = $this->url->link('product/reviews');
		$this->data['menu_item6_link'] = $this->url->link('information/information', 'information_id=10');
			
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/footer.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/footer.tpl';
		} else {
			$this->template = 'default/template/common/footer.tpl';
		}
		
		$this->render();
	}
}
?>