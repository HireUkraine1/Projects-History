<?php   
class ControllerCommonHeader extends Controller {
	protected function index() {
		$this->data['title'] = $this->document->getTitle();
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		$this->data['base'] = $server;
		$this->data['description'] = $this->document->getDescription();
		$this->data['keywords'] = $this->document->getKeywords();
		$this->data['links'] = $this->document->getLinks();	 
		$this->data['styles'] = $this->document->getStyles();
		$this->data['scripts'] = $this->document->getScripts();
		$this->data['lang'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');
		$this->data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
		$this->data['name'] = $this->config->get('config_name');
		
		$this->data['text_site_name'] = $this->language->get('text_site_name');
		
		if ($this->config->get('config_icon') && file_exists(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->data['icon'] = $server . 'image/' . $this->config->get('config_icon');
		} else {
			$this->data['icon'] = '';
		}
		
		if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
			$this->data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$this->data['logo'] = '';

		}		
		
		$this->language->load('common/header');
		$this->data['og_url'] = (isset($this->request->server['HTTPS']) ? HTTPS_SERVER : HTTP_SERVER) . substr($this->request->server['REQUEST_URI'], 1, (strlen($this->request->server['REQUEST_URI'])-1));
		$this->data['og_image'] = $this->document->getOgImage();
		
		$this->data['text_home'] = $this->language->get('text_home');
		$this->data['text_shopping_cart'] = $this->language->get('text_shopping_cart');
		$this->data['text_welcome'] = sprintf($this->language->get('text_welcome'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'));
		$this->data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', 'SSL'), $this->url->link('account/logout', '', 'SSL'));
		$this->data['text_account'] = $this->language->get('text_account');
		$this->data['text_checkout'] = $this->language->get('text_checkout');
		$this->data['text_page'] = $this->language->get('text_page');
				
		$this->data['home'] = $this->url->link('common/home');
		$this->data['logged'] = $this->customer->isLogged();
		$this->data['account'] = $this->url->link('account/account', '', 'SSL');
		$this->data['shopping_cart'] = $this->url->link('checkout/cart');
		$this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');

		$this->data['menu_item1'] = $this->language->get('menu_item1');
		$this->data['menu_item2'] = $this->language->get('menu_item2');
		$this->data['menu_item3'] = $this->language->get('menu_item3');
		$this->data['menu_item4'] = $this->language->get('menu_item4');
		$this->data['menu_item5'] = $this->language->get('menu_item5');
		$this->data['menu_item6'] = $this->language->get('menu_item6');
		$this->data['menu_item7'] = $this->language->get('menu_item7');
		
		$this->data['text_menu'] = $this->language->get('text_menu');
		
		$this->data['menu_item2_link'] = $this->url->link('information/information', 'information_id=7');
		$this->data['menu_item3_link'] = $this->url->link('information/information', 'information_id=8');
		$this->data['menu_item5_link'] = $this->url->link('product/reviews');
		$this->data['menu_item4_link'] = $this->url->link('information/information', 'information_id=9');
		$this->data['menu_item6_link'] = $this->url->link('information/information', 'information_id=10');
		
		// Daniel's robot detector
		$status = true;
		
		if (isset($this->request->server['HTTP_USER_AGENT'])) {
			$robots = explode("\n", trim($this->config->get('config_robots')));

			foreach ($robots as $robot) {
				if ($robot && strpos($this->request->server['HTTP_USER_AGENT'], trim($robot)) !== false) {
					$status = false;

					break;
				}
			}
		}
		
		// Menu
		
		
		$this->children = array(
			'module/language',
			'module/currency',
			'module/cart'
		);
				
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/header.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/header.tpl';
		} else {
			$this->template = 'default/template/common/header.tpl';
		}
		
    	$this->render();
	} 	
}
?>
