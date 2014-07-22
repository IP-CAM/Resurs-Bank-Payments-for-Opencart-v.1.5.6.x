<?php
require_once(DIR_SYSTEM . '../admin/model/payment/resurs_util.php');
class ControllerTotalResursFee extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('total/resurs_fee');
		$this->load->language('payment/resurs');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');


		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('resurs_fee', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			/**
			$this->response->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
			**/
			$this->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
		}
		// All Fields
		$data['heading_title'] = $this->language->get('heading_title');
		$data['entry_invoice_line'] = $this->language->get('entry_invoice_line');
		$data['entry_fee'] = $this->language->get('entry_fee');
		$data['text_none'] = $this->language->get('text_none');
		$data['entry_fee_tax'] = $this->language->get('entry_fee_tax');

		
		$data['entry_paymentmethods'] = $this->language->get('entry_paymentmethods');
		
		if (isset($this->request->post['resurs_fee_status'])) {
			$data['resurs_fee_status'] = $this->request->post['resurs_fee_status'];
		} else {
			$data['resurs_fee_status'] = $this->config->get('resurs_fee_status');
		}
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		//Load Fields
		if (isset($this->request->post['resurs_fee'])) {
			$data['resurs_fee'] = $this->request->post['resurs_fee'];
		} else {
			$data['resurs_fee'] = $this->config->get('resurs_fee');
		}
		
		$data['countries'] = ResursUtils::getCountriesLanguage($this->language);
		
		if(!isset($data['resurs_fee'])){
			$data['resurs_fee'] = array();
		}
		
		foreach(ResursUtils::getCountries()  as $countryCode){
			
			$paymentMethods = $this->getPaymentMethods($countryCode);
			
			if(!isset($data['resurs_fee'][$countryCode])){
				$data['resurs_fee'][$countryCode] = array();
			}
			if(!isset($data['resurs_fee'][$countryCode]['paymentmethod'])){
				$data['resurs_fee'][$countryCode]['paymentmethod'] = array();
			}
			
			foreach($data['resurs_fee'][$countryCode]['paymentmethod'] as $savedPaymentMethod){	
				$data['resurs_fee'][$countryCode]['paymentmethod'][$savedPaymentMethod['id']]['enabled'] = true; 
			}
			
			if(isset($paymentMethods->return)) {
				foreach($paymentMethods->return as $paymentMethod){
					if(!isset($data['resurs_fee'][$countryCode]['paymentmethod'][$paymentMethod->id])){
						$data['resurs_fee'][$countryCode]['paymentmethod'][$paymentMethod->id] = array(
							"enabled"=>true,
							"name"=>"",
							"id"=>""
						);
					} 
					$data['resurs_fee'][$countryCode]['paymentmethod'][$paymentMethod->id]['enabled'] = true; 
					$data['resurs_fee'][$countryCode]['paymentmethod'][$paymentMethod->id]['name'] = $paymentMethod->description; 
					$data['resurs_fee'][$countryCode]['paymentmethod'][$paymentMethod->id]['id'] = $paymentMethod->id; 						
				}
			}
		}

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		$data['info_table_paymentmethod_id'] = $this->language->get('info_table_paymentmethod_id');
		$data['info_table_paymentmethod_name'] = $this->language->get('info_table_paymentmethod_name');
		$data['info_table_paymentmethod_fee'] = $this->language->get('info_table_paymentmethod_fee');
		$data['info_table_paymentmethod_taxclass'] = $this->language->get('info_table_paymentmethod_taxclass');
		$data['info_table_paymentmethod_invoiceline'] = $this->language->get('info_table_paymentmethod_invoiceline');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_total'),
			'href' => $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL')
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('total/resurs_fee', 'token=' . $this->session->data['token'], 'SSL')
		);


		$data['action'] = $this->url->link('total/resurs_fee', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');


		
		$this->load->model('localisation/tax_class');
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		/**
		$data['header'] = $this->load->controller('common/header');
		$data['menu'] = $this->load->controller('common/menu');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('total/resurs_fee.tpl', $data));
		*/
		$this->data = $data;
		
		
		$this->template = 'total/resurs_fee.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
		

	}

	
	public function getPaymentMethods($countryCode){
	
		$resurs = $this->config->get('resurs');	
		
		if(!isset($resurs[$countryCode]) 
		|| !isset($resurs[$countryCode]['server']) || strlen($resurs[$countryCode]['server']) == 0 
		|| !isset($resurs[$countryCode]['password']) || strlen($resurs[$countryCode]['password']) == 0 
		|| !isset($resurs[$countryCode]['username']) || strlen($resurs[$countryCode]['username']) == 0 
		){
			return array();
		}
		
		$server = $resurs[$countryCode]['server'];
		$password =$resurs[$countryCode]['password'];
		$login = $resurs[$countryCode]['username'];
		try { 
			$params = array();							
			$result = ResursUtils::getPaymentMethods($login,$password,$server);
		
			return $result;
		}catch (Exception $e) { 
			ResursUtils::log("Error:".$e->getMessage());
			return array();
		}
	}
	

	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/resurs')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
	
		return !$this->error;
	}
	
	
}