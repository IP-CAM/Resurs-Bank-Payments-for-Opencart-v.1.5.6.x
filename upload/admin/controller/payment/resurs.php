<?php
require_once(DIR_SYSTEM . '../admin/model/payment/resurs_util.php');
class ControllerPaymentResurs extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('payment/resurs');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('resurs', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			/**
			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
			**/
			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}
		// All Fields
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_production'] = $this->language->get('text_production');
		$data['text_test'] = $this->language->get('text_test');
		$data['entry_server'] = $this->language->get('entry_server');
		$data['countries'] = ResursUtils::getCountriesLanguage($this->language);
		$data['entry_frozen_status_id'] = $this->language->get('entry_frozen_status_id');
		$data['text_other'] = $this->language->get('text_other');		
		$data['entry_signing_status_id'] = $this->language->get('entry_signing_status_id');
		$data['entry_annulled_status_id'] = $this->language->get('entry_annulled_status_id');
		$data['entry_booked_status_id'] = $this->language->get('entry_booked_status_id');
		$data['entry_finalized_status_id'] = $this->language->get('entry_finalized_status_id');
		$data['entry_denied_status_id'] = $this->language->get('entry_denied_status_id');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');		
		$data['entry_username'] = $this->language->get('entry_username');
		$data['help_username'] = $this->language->get('help_username');		
		$data['entry_password'] = $this->language->get('entry_password');
		$data['help_password'] = $this->language->get('help_password');
		$data['info_save'] = $this->language->get('info_save');
		$data['info_paymentmethod_checkbox'] = $this->language->get('info_paymentmethod_checkbox');
		$data['info_paymentmethod_id'] = $this->language->get('info_paymentmethod_id');
		$data['info_paymentmethod_name'] = $this->language->get('info_paymentmethod_name');
		$data['info_custom_paymentmethod_name'] = $this->language->get('info_custom_paymentmethod_name');
		$data['entry_image_height'] = $this->language->get('entry_image_height');
		$data['entry_image_width'] = $this->language->get('entry_image_width');
		$data['help_image_width'] = $this->language->get('help_image_width');
		$data['help_image_height'] = $this->language->get('help_image_height');

		$data['info_imageURL'] = $this->language->get('info_imageURL');
		$data['info_imageWidth'] = $this->language->get('info_imageWidth');
		$data['info_imageHeight'] = $this->language->get('info_imageHeight');

		
		
		$data['entry_paymentmethods'] = $this->language->get('entry_paymentmethods');
		
		if (isset($this->request->post['resurs_status'])) {
			$data['resurs_status'] = $this->request->post['resurs_status'];
		} else {
			$data['resurs_status'] = $this->config->get('resurs_status');
		}	
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->request->post['resurs'])) {
			$data['resurs'] = $this->request->post['resurs'];
		} else {
			$data['resurs'] = $this->config->get('resurs');
		}
		
		if (isset($this->request->post['resurs_salt'])) {
			$data['resurs_salt'] = $this->request->post['resurs_salt'];
		} else if($this->config->get('resurs_salt')){
			$data['resurs_salt'] = $this->config->get('resurs_salt');
		} else {
			$data['resurs_salt'] = ResursUtils::rand_string(10);
		}	

		$this->load->model('localisation/order_status');
		
		$this->load->model('setting/store');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

	
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);
		

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/resurs', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/resurs', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		/**
		$data['header'] = $this->load->controller('common/header');
		$data['menu'] = $this->load->controller('common/menu');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('payment/resurs.tpl', $data));
		**/	

		$this->data = $data;
		$this->template = 'payment/resurs.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}



	public function getPaymentMethods(){
		$currentResursData = $this->request->post['countryCode'];
		
		$server = $this->request->post['server'];
		$password = $this->request->post['password'];
		$login = $this->request->post['username'];
		$country = $this->request->post['countryCode'];
		
		$resurs = $this->config->get('resurs');
		
		try { 
															
			$result = ResursUtils::getPaymentMethods($login,$password,$server);

			$jsonresponse = '{ "paymentmethods": [';
			
			foreach ($result->return as $payment_method){
				
				
				if(ResursUtils::isPaymentMethodEnabled($payment_method->id,$country,$this->config,$this->request->post)){
					$enabled = "true";
				} else{
					$enabled = "false";
				}
				$customDescription  = '';
				$imageURL = '';
				$imageWidth = '';
				$imageHeight = '';
				
				
				if(isset($resurs) && isset($resurs[$country]) && isset($resurs[$country]['paymentmethod'.$payment_method->id])
				&& isset($resurs[$country]['paymentmethod'.$payment_method->id]['customDescription'])){
					$customDescription   = $resurs[$country]['paymentmethod'.$payment_method->id]['customDescription'];
				}		
				
				if(isset($resurs) && isset($resurs[$country]) && isset($resurs[$country]['paymentmethod'.$payment_method->id])
				&& isset($resurs[$country]['paymentmethod'.$payment_method->id]['imageURL'])){
					$imageURL   = $resurs[$country]['paymentmethod'.$payment_method->id]['imageURL'];
				}		
				
				if(isset($resurs) && isset($resurs[$country]) && isset($resurs[$country]['paymentmethod'.$payment_method->id])
				&& isset($resurs[$country]['paymentmethod'.$payment_method->id]['imageWidth'])){
					$imageWidth   = $resurs[$country]['paymentmethod'.$payment_method->id]['imageWidth'];
				}		
				
				if(isset($resurs) && isset($resurs[$country]) && isset($resurs[$country]['paymentmethod'.$payment_method->id])
				&& isset($resurs[$country]['paymentmethod'.$payment_method->id]['imageHeight'])){
					$imageHeight = $resurs[$country]['paymentmethod'.$payment_method->id]['imageHeight'];
				}		
				
				
				
				$jsonresponse = 
				$jsonresponse.'{"id":"'.$payment_method->id.'","description":"'
				.$payment_method->description.'","enabled":"'
				.$enabled.'","customDescription":"'.$customDescription
				.'","imageURL":"'.$imageURL.'","imageWidth":"'.$imageWidth.'","imageHeight":"'.$imageHeight.'"},';
			}
			$jsonresponse  = rtrim($jsonresponse, ",");
			$jsonresponse = $jsonresponse.']}';
			$this->response->setOutput($jsonresponse);
			
		}catch (Exception $e) { 
				ResursUtils::log("Error failed to retreive paymentMethods:".$e->getMessage());
				$this->response->setOutput("Error: Failed to connect to Resurs Bank, wrong username, password or service is down.");
				return;
		}
		return;
	}
	
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/resurs')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if(!$this->error) {
			$countries = ResursUtils::getCountries();

			foreach($countries as $country){
				$settingsPerCountry = $this->request->post['resurs'][$country];
				if($settingsPerCountry['username'] && $settingsPerCountry['password'] && $settingsPerCountry['server'] ){			
						
					$username = $settingsPerCountry['username'];
					$password = $settingsPerCountry['password'];
					$server = $settingsPerCountry['server'];
					$salt =  $this->request->post['resurs_salt'];

					if(strlen($username) > 0 && strlen($password) > 0 && strlen($server)){
						/** Unregister all eventCallbacks.*/
						$this->unregisterEvent($username,$password,$server);
						/** Register all eventCallbacks.*/
						$this->registerEvent($username,$password,$server,$salt);
					}
					
				}
			}
		}		
		return !$this->error;
	}
	

	private function registerEvent($login,$password,$server,$salt){
	
			$client = ResursUtils::getClient($login,$password,$server,'ConfigurationService');
			$events = ResursUtils::getEvents();
			
			foreach($events as $event) {					
					try{
						
						$urlParams = 'paymentId={paymentId}&digest={digest}&type='.$event;
						if($event == 'AUTOMATIC_FRAUD_CONTROL') {
							$urlParams = $urlParams.'result={result}';
						}
				
						$paramsDigest = array('digestAlgorithm'=>'MD5','digestParameters'=>'paymentId','digestSalt'=>$salt);
						
						$params = array('eventType'=>$event,
						'uriTemplate'=>HTTP_CATALOG . 'index.php?route=payment/resurs/callback/run&'.$urlParams
						,'digestConfiguration'=>$paramsDigest);
						
						$result = $client->__soapCall("registerEventCallback",array($params));	
						ResursUtils::log('Registered new event: '.$event.' for username: '.$login.' in environment:'.$server);
					}catch (Exception $e) { 
						ResursUtils::log('Failed to Registered new event:'.$event.' for username:'.$login.' in environment:'.$server.' exception:'.$e->getMessage());
				}
			}
	}
	
	private function unregisterEvent($login,$password,$server){
			$client = ResursUtils::getClient($login,$password,$server,'ConfigurationService');

			$events = ResursUtils::getEvents();
			foreach($events as $event) {					
					try{
						$params = array('eventType'=>$event);
						$result = $client->__soapCall("unregisterEventCallback",array($params));	
						ResursUtils::log('Unregister new event:'.$event.' for username:'.$login.' in environment:'.$server);						
					}catch (Exception $e) { 
						ResursUtils::log('Failed to Unregister new event:'.$event.' for username:'.$login.' in environment:'.$server.' exception:'.$e->getMessage());						

					}
			}
	}
	
}