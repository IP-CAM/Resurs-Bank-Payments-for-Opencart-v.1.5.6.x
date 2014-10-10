<?php
require_once(DIR_SYSTEM . '../admin/model/payment/resurs_util.php');
class ControllerPaymentResurs extends Controller {
	public function index() {
		$this->load->language('payment/resurs');

		$data['button_confirm'] = $this->language->get('button_confirm');

		/**		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/resurs.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/resurs.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/resurs.tpl', $data);
		}
		**/	
	
		$this->data = $data;
		$this->template = 'default/template/payment/resurs.tpl';
		$this->response->setOutput($this->render());
		
	}

	public function createpayment() {


			$this->load->model('checkout/order');
			$this->load->language('payment/resurs');

			$amount_text = $this->language->get('amount_text');
			
			$order_details = $this->model_checkout_order->getOrder($this->session->data['order_id']);		
						
			if ($this->cart->hasShipping() && 
			!($order_details['payment_firstname'] == $order_details['shipping_firstname'] && 
			$order_details['payment_lastname'] == $order_details['shipping_lastname'] && 
			$order_details['payment_address_1'] == $order_details['shipping_address_1'] && 
			$order_details['payment_postcode'] == $order_details['shipping_postcode'] && 
			$order_details['payment_city'] == $order_details['shipping_city'] && 
			$order_details['payment_zone_id'] == $order_details['shipping_zone_id'] && 
			$order_details['payment_zone_code'] == $order_details['shipping_zone_code'] && 
			$order_details['payment_country_id'] == $order_details['shipping_country_id'] && 
			$order_details['payment_country'] == $order_details['shipping_country'] && 
			$order_details['payment_iso_code_3'] == $order_details['shipping_iso_code_3'])) {
				
				$deliveryAddress = array('fullName'=>$order_details['shipping_firstname'].' '.$order_details['shipping_lastname'],
				'firstName'=> $order_details['shipping_firstname'],
				'lastName'=>$order_details['shipping_lastname'],'addressRow1'=>$order_details['shipping_address_1'],
				'addressRow2'=>'','postalArea'=>$order_details['shipping_city'],
				'postalCode'=>$order_details['shipping_postcode'],
				'country'=>ResursUtils::getCountryCode($order_details['shipping_iso_code_3']));
			} else {
				$deliveryAddress =  null;
			}
		
			$governmentId = $this->request->post['governmentId'];
			$cardnumber = $this->request->post['cardnumber'];
			$contcat = $this->request->post['contcat'];
			$cardamount = $this->request->post['cardamount'];
			$type = $this->request->post['type'];
			
			$paymentMethod = $this->getPaymentMethod($order_details['payment_iso_code_3'],$this->request->post['paymentmethodid']);
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET payment_method = '" . 
			$this->db->escape($paymentMethod->description) . 
			"' WHERE order_id = '" . (int)$this->session->data['order_id']. "'");
			
			
			
			$address = array('fullName'=>$order_details['payment_firstname'].' '.$order_details['payment_lastname'],
				'firstName'=> $order_details['payment_firstname'],
				'lastName'=>$order_details['payment_lastname'],'addressRow1'=>$order_details['payment_address_1'],
				'addressRow2'=>'','postalArea'=>$order_details['payment_city'],
				'postalCode'=>$order_details['payment_postcode'],
				'country'=>ResursUtils::getCountryCode($order_details['payment_iso_code_3']));
			
			
			$paymentData =  array('customerIpAddress'=>$order_details['ip'],
			'preferredId'=>$this->session->data['order_id'],
			'paymentMethodId'=>$paymentMethod->id,
			'waitForFraudControl'=>'false');

			/**
			$signing  = array('successUrl'=>$this->config->get('config_url').$this->url->link('payment/resurs/ok/ok&order_id='.$this->session->data['order_id'].'&countryCode='.$order_details['payment_iso_code_3']),
							  'failUrl'=>$this->config->get('config_url').$this->url->link('payment/resurs/fail/fail&order_id='.$this->session->data['order_id'].'&countryCode='.$order_details['payment_iso_code_3']));
			**/
			$signing  = array('successUrl'=>$this->url->link('payment/resurs/ok/ok&order_id='.$this->session->data['order_id'].'&countryCode='.$order_details['payment_iso_code_3']),
							  'failUrl'=>$this->url->link('payment/resurs/fail/fail&order_id='.$this->session->data['order_id'].'&countryCode='.$order_details['payment_iso_code_3']));
				
		
			$customer  =  array('governmentId'=>$governmentId,'cellPhone'=>$order_details['telephone']
			,'phone'=>$order_details['telephone'],'email'=>$order_details['email'],
			'yourCustomerId'=>$order_details['customer_id'],'type'=>$type,
			'address'=>$address);		

					
			if(isset($contcat) && strlen($contcat) > 0) {
				$customer['contactGovernmentId']=$contcat;
			}
					
			if($deliveryAddress != null){
				$customer['deliveryAddress']=$deliveryAddress;
			}

			$product_query = $this->db->query("SELECT  `total` ,`name`, `model`, `price`, `quantity`, `tax` / `price` * 100 AS 'tax_rate' FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = " 
			.(int)$order_details['order_id'] . " UNION ALL SELECT  `amount`,'', `code`, `amount`, '1', 0.00 FROM `" . DB_PREFIX . "order_voucher` WHERE `order_id` = " . (int)$order_details['order_id']);
			
			$totalAmount = 0;
			$totalAmountVat = 0;
			
			$i = 0;
			foreach ($product_query->rows as $product) {
				$specLine[] = array(
						'id'   => $i,
						'artNo'   => $product['model'],
						'description'   => $product['name'],
						'quantity'   => (int)$product['quantity'],
						'unitMeasure'   => $amount_text,
						'unitAmountWithoutVat' => $product['price'],
						'vatPct'   => $product['tax_rate'],
						'totalVatAmount'   => $product['tax_rate']/100*(int)$product['quantity']*$product['price'],
						'totalAmount'   => $product['total']+$product['tax_rate']/100*(int)$product['quantity']*$product['price']
				);
				$totalAmount += $product['total'];
				$totalAmountVat += $product['tax_rate']/100*(int)$product['quantity']*$product['price'];		
				$i++;				
			}
			
			$shipingmethod = $this->session->data['shipping_method'];
			if(isset($shipingmethod) && $shipingmethod['cost'] > 0){
				$taxamount = 0;
				
				if ($this->session->data['shipping_method']['tax_class_id']) {
					$tax_rates = $this->tax->getRates($shipingmethod['cost'],$shipingmethod['tax_class_id']);
					foreach ($tax_rates as $tax_rate) {
							$taxamount += $tax_rate['amount'];
	
					}					
				}
					
				$specLine[] = array(
						'id'   => $shipingmethod['code'],
						'artNo'   => $shipingmethod['code'] ,
						'description'   => $shipingmethod['title'],
						'quantity'   => 1,
						'unitMeasure'   => $amount_text,
						'unitAmountWithoutVat' => $shipingmethod['cost'],
						'vatPct'   => $taxamount/$shipingmethod['cost']*100,
						'totalVatAmount'   => $taxamount,
						'totalAmount'   => $shipingmethod['cost']+$taxamount
				);
				$totalAmount += $shipingmethod['cost'];
				$totalAmountVat += $taxamount;
			}
			
			$resurs_fee = $this->config->get('resurs_fee');
			$fees = $resurs_fee[$order_details['payment_iso_code_3']]['paymentmethod'][$this->request->post['paymentmethodid']];			
	
			if($fees['fee'] > 0) {
			
				$tax_rates = $this->tax->getRates($fees['fee'], $fees['fee_tax_class_id']);
									
				$vat = 0;
				foreach ($tax_rates as $tax_rate) {
					$vat  += $tax_rate['amount'];
				}
				
				$specLine[] = array(
						'id'   => 'invoicefee',
						'artNo'   => 'invoicefee' ,
						'description'   => $fees['invoice_line'],
						'quantity'   => 1,
						'unitMeasure'   => $amount_text,
						'unitAmountWithoutVat' => $fees['fee'],
						'vatPct'   => $vat/$fees['fee']*100,
						'totalVatAmount'   => $vat,
						'totalAmount'   => $fees['fee']+$vat
				);
				$totalAmount += $fees['fee'];
				$totalAmountVat += $vat;			
			}		
			
			$orderData = array('specLines'=>$specLine,'totalAmount'=>$totalAmount+$totalAmountVat,'totalVatAmount'=>$totalAmountVat);			
			$metaData = array();
			$metaData[] = array("key"=>"comment","value"=>$order_details['comment']);
			$metaData[] = array("key"=>"user_agent","value"=>$order_details['user_agent']);
			
			$card = array();
			
			if(ResursUtils::isPartPayment($paymentMethod) && (!isset($cardamount) || strlen($cardamount) == 0)){
				$cardamount = $totalAmount+$totalAmountVat;
			}
			
			if(isset($cardnumber) && strlen($cardnumber) > 0) {
				$card  ['cardNumber']=$cardnumber;
			}
			if(isset($cardamount) && strlen($cardamount) > 0) {
				$card['amount']=$cardamount;
			}

			$bookPayment = array(
				'paymentData'=>$paymentData,
				'orderData'=>$orderData,
				'metaData'=>$metaData,
				'customer'=>$customer,
				'card'=> $card,
				'signing'=> $signing);
				
		
			$json = $this->validatePaymentData($bookPayment,$order_details['payment_iso_code_3'],$paymentMethod);
			
			if(count($json['error']) == 0){
				$result = $this->book($bookPayment,$order_details['payment_iso_code_3']);	
				$json['redirect'] = $this->checkBookingResult($result,$this->session->data['order_id']);
			}

			$this->response->setOutput(json_encode($json));	
	}
	
	private function validatePaymentData($bookPayment,$countryCode,$paymentMethod){
		$json = array();
		$json['error'] = array();
		
		$this->load->language('payment/resurs');
		
		//**Validate**/

		//Check Email
		if(!isset($bookPayment['customer']['email']) || empty($bookPayment['customer']['email'])){
			$error =$this->language->get('error_empty_email');
			$json['error']['email'] = $error;
			$this->addWarning($json,$error,$this->language->get('error_account_and_billing'));
		}
		elseif(!ResursUtils::isValidEmail($bookPayment['customer']['email'], $countryCode)){
			$error =$this->language->get('error_email').' ('.$bookPayment['customer']['email'].')';
			$json['error']['email'] = $error;
			$this->addWarning($json,$error,$this->language->get('error_account_and_billing'));
		}
		
		//Telephone
		if(!isset($bookPayment['customer']['phone']) || empty($bookPayment['customer']['phone'])){
			$error =$this->language->get('error_empty_phone');
			$json['error']['telephone'] = $error;
			$this->addWarning($json,$error,$this->language->get('error_account_and_billing'));
		}
		elseif(!ResursUtils::isValidPhoneNumner($bookPayment['customer']['phone'],$countryCode)){
			$error = $this->language->get('error_phone').' ('.$bookPayment['customer']['phone'].')';
			$json['error']['telephone'] = $error;			
			$this->addWarning($json,$error,$this->language->get('error_account_and_billing'));
		}
		
		
		if(ResursUtils::isLegalInvoice($paymentMethod)){
		
			if(!isset($bookPayment['customer']['governmentId']) || empty($bookPayment['customer']['governmentId'])){
				$error =$this->language->get('error_empty_legal_governmentId');
				$json['error']['resursextra_governmentid'.$paymentMethod->id] = $error;
				$this->addWarning($json,$error,$this->language->get('error_payment_method'));
			}
			elseif(!ResursUtils::isValidLegalGovernmnetId($bookPayment['customer']['governmentId'],$countryCode)){
				$error = $this->language->get('error_legal_governmentId').' ('.$bookPayment['customer']['governmentId'].')';
				$json['error']['resursextra_governmentid'.$paymentMethod->id] = $error;
				$this->addWarning($json,$error,$this->language->get('error_payment_method'));
			}

			if(!isset($bookPayment['customer']['contactGovernmentId']) || empty($bookPayment['customer']['contactGovernmentId'])){
				$error =$this->language->get('error_empty_contactGovernmentId');
				$json['error']['resursextra_governmentid_contact'.$paymentMethod->id] = $error;
				$this->addWarning($json,$error,$this->language->get('error_payment_method'));
			}
			elseif(!ResursUtils::isValidContactName($bookPayment['customer']['contactGovernmentId'],$countryCode)){
				$error = $this->language->get('error_contactGovernmentId').' ('.$bookPayment['customer']['contactGovernmentId'].')';
				$json['error']['resursextra_governmentid_contact'.$paymentMethod->id] =$error; 
				$this->addWarning($json,$error,$this->language->get('error_payment_method'));

			}
			
		} else {	
			if(!isset($bookPayment['customer']['governmentId']) || empty($bookPayment['customer']['governmentId'])){
				$error =$this->language->get('error_empty_governmentId');
				$json['error']['resursextra_governmentid'.$paymentMethod->id] = $error;
				$this->addWarning($json,$error,$this->language->get('error_payment_method'));
			}		
			elseif(!ResursUtils::isValidNaturalGovernmnetId($bookPayment['customer']['governmentId'],$countryCode)){
				$error = $this->language->get('error_governmentId').' ('.$bookPayment['customer']['governmentId'].')';
				$json['error']['resursextra_governmentid'.$paymentMethod->id] = $error; 
				$this->addWarning($json,$error,$this->language->get('error_payment_method'));				
			}

			if(ResursUtils::isInvoceOrNewAccount($paymentMethod)) {

			}elseif(ResursUtils::isCard($paymentMethod)) {
			
				if(!isset($bookPayment['card']['cardNumber']) || empty($bookPayment['card']['cardNumber'])){
					$error =$this->language->get('error_empty_cardNumber');
					$json['error']['resursextra_cardnumber'.$paymentMethod->id] = $error;
					$this->addWarning($json,$error,$this->language->get('error_payment_method'));
				}		
				elseif(!ResursUtils::isValidCardNumber($bookPayment['card']['cardNumber'],$countryCode)){
					$error = $this->language->get('error_cardNumber').' ('.$bookPayment['card']['cardNumber'].')';
					$json['error']['resursextra_cardnumber'.$paymentMethod->id] = $error;
					$this->addWarning($json,$error,$this->language->get('error_payment_method'));
				}
			}elseif(ResursUtils::isNewCard($paymentMethod)) {	

			}
		}
		return $json;	
	}
	
	public function addWarning(&$json,$string,$section){
		if(!isset($json['error']['warning'])){
			$json['error']['warning'] = '';
		}
		$json['error']['warning'] = $json['error']['warning'].$section.' : '.$string.'<br/>';	
	}
	
	public function callback(){
		
		$resurs_salt = $this->config->get('resurs_salt');		
		$paymentId = $this->request->get['paymentId'];
		$type = $this->request->get['type'];
		$digest = $this->request->get['digest'];
		
		$calculatedDigest = md5($paymentId.$resurs_salt); 

		if(strtolower($calculatedDigest)  != strtolower($digest)) {
			ResursUtils::log("Error: PaymentId:".$paymentId." EventType : ".$type." not matching supplied digest:".$digest);
			header($this->request->server['SERVER_PROTOCOL'] .'/1.0 400 Bad Request');
			return ;
		}
		elseif($type == 'AUTOMATIC_FRAUD_CONTROL'){
			$type = $this->request->get['result'];
			if($type == "FROZEN"){
				$this->setFrozenStatus($paymentId);
			} elseif($type == "THAWED"){
				$this->setBookedStatus($paymentId);
			}
		} elseif($type == 'ANNULMENT'){
			$this->setAnnulmentStatus($paymentId);
		} elseif($type == 'FINALIZATION'){
			$this->setFinalizationStatus($paymentId);	
		} elseif($type == 'UNFREEZE'){
			$this->setBookedStatus($paymentId);
		}	
		else{
			header($this->request->server['SERVER_PROTOCOL'] .'/1.0 400 Bad Request');
			return ;
		}

		ResursUtils::log("Succes: Callback PaymentId:".$paymentId." EventType: ".$type);
		header($this->request->server['SERVER_PROTOCOL'] .'/1.0 202 Accepted');
		return;

	}
	
	private function setFrozenStatus($order_id){		
		ResursUtils::log("Resurs Bank: Frozen Status for order:".$order_id);		
	}
	
	private function setBookedStatus($order_id){
		$resurs = $this->config->get('resurs');	
		
		$order_status = $this->config->get('config_order_status_id');
		$this->load->model('checkout/order');

		$this->model_checkout_order->confirm($order_id,$order_status,'Payment request to Resurs Bank has returned ok.',1);

		$this->model_checkout_order->update($order_id, $resurs['booked_status_id'], "Payment Book by Resurs Bank.");
		
		$order_details = $this->model_checkout_order->getOrder($order_id);	

		try { 
			$params = array('paymentId'=>$order_id);		

			$client =  ResursUtils::getClientWithConfig($this->config,$order_details['payment_iso_code_3'],"AfterShopFlowService");
			$result = $client->__soapCall("getPayment",array($params));	
			
			if(isset($result->return)){
				$result = $result->return;
			}
			
			if(isset($result->customer) && isset($result->customer->address)){
				$this->db->query("update `" . DB_PREFIX . "order` set ".
			" payment_firstname ='".$result->customer->address->firstName."'".
			" ,payment_lastname ='".$result->customer->address->lastName."'".
			" ,payment_address_1='".$result->customer->address->addressRow1."'".
			" ,payment_city='".$result->customer->address->postalArea."'".
			" ,payment_postcode='".$result->customer->address->postalCode."'".			
			" WHERE order_id = '" . (int)$order_id."'");		
			}
			
			if(isset($result->deliveryAddress)){
				$this->db->query("update `" . DB_PREFIX . "order` set ".
			" ,shipping_firstname ='".$result->deliveryAddress->firstName."'".
			" ,shipping_lastname ='".$result->deliveryAddress->lastName."'".
			" ,shipping_address_1='".$result->deliveryAddress->addressRow1."'".
			" ,shipping_city='".$result->deliveryAddress->postalArea."'".
			" ,shipping_postcode='".$result->deliveryAddress->postalCode."'".			
			" WHERE order_id = '" . (int)$order_id."'");
			} elseif(isset($result->customer) && isset($result->customer->address)){
				$this->db->query("update `" . DB_PREFIX . "order` set ".
			" shipping_firstname ='".$result->customer->address->firstName."'".
			" ,shipping_lastname ='".$result->customer->address->lastName."'".
			" ,shipping_address_1='".$result->customer->address->addressRow1."'".
			" ,shipping_city='".$result->customer->address->postalArea."'".
			" ,shipping_postcode='".$result->customer->address->postalCode."'".			
			" WHERE order_id = '" . (int)$order_id."'");
			}
			
		}catch (Exception $e) { 
			ResursUtils::log("Error failed to getPayment:".$e->getMessage());
		}		
		
	}
	
	private function setFinalizationStatus($order_id){
		$resurs = $this->config->get('resurs');	
		$this->load->model('checkout/order');
		$this->model_checkout_order->update($order_id,$resurs['finalized_status_id'], "Payment Finalized by Resurs Bank.");
	}
	private function setAnnulmentStatus($order_id){
		$resurs = $this->config->get('resurs');	
		$this->load->model('checkout/order');
		$this->model_checkout_order->update($order_id,$resurs['annulled_status_id'], "Payment has been Annulled by Resurs Bank.");
	}
	
	private function setSignedStatus($order_id){
		ResursUtils::log("Resurs Bank: Signing Required for order:".$order_id);		
	}

	private function setDeniedStatus($order_id){
		ResursUtils::log("Resurs Bank: Denied order:".$order_id);		
	}

	private function setFailureStatus($order_id){
		ResursUtils::log("Resurs Bank: Failed to book order:".$order_id);		
	}

	private function checkBookingResult($result,$orderId){

		$this->load->model('checkout/order');
	
		if(isset($result) && isset($result->return) && isset($result->return->bookPaymentStatus)) {
			if($result->return->bookPaymentStatus=='SIGNING') {
				$this->setSignedStatus($result->return->paymentId);
				return $result->return->signingUrl;
			}
			elseif($result->return->bookPaymentStatus=='FINALIZED') {	
				$this->setFinalizationStatus($result->return->paymentId);
				return $this->url->link('checkout/success');
			}
			elseif($result->return->bookPaymentStatus=='BOOKED') {
				$this->setBookedStatus($result->return->paymentId);
				return  $this->url->link('checkout/success');
			}elseif($result->return->bookPaymentStatus=='FROZEN') {
				$this->setFrozenStatus($result->return->paymentId);
				return $this->url->link('checkout/success');
			}
			elseif($result->return->bookPaymentStatus=='DENIED') {
				$this->setDeniedStatus($result->return->paymentId);
				return $this->url->link('payment/resurs/fail');
			}			
		}
		ResursUtils::log($result);
		$this->setFailureStatus($orderId);
		return $this->url->link('payment/resurs/fail');

	}
	public function ok() {
		$result = $this->bookSignedPayment($this->request->get['countryCode'],$this->request->get['order_id']);
		$this->response->redirect($this->checkBookingResult($result,$this->request->get['order_id']));
	}

	
	public function fail() {
	
		$this->language->load('payment/resurs');		
		$this->document->setTitle($this->language->get('heading_title'));
		$this->data['breadcrumbs'] = array(); 

      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('common/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => false
      	); 
		
      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('checkout/cart'),
        	'text'      => $this->language->get('text_basket'),
        	'separator' => $this->language->get('text_separator')
      	);
				
		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
			'text'      => $this->language->get('text_checkout'),
			'separator' => $this->language->get('text_separator')
		);	
					
      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('payment/resurs/fail'),
        	'text'      => $this->language->get('text_fail'),
        	'separator' => $this->language->get('text_separator')
      	);
		
    	$this->data['heading_title'] = $this->language->get('heading_title');

		if ($this->customer->isLogged()) {
    		$this->data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', 'SSL'), $this->url->link('account/order', '', 'SSL'), $this->url->link('account/download', '', 'SSL'), $this->url->link('information/contact'));
		} else {
    		$this->data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}
		
    	$this->data['button_continue'] = $this->language->get('button_continue');

    	$this->data['continue'] = $this->url->link('common/home');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/resurs_failure.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/resurs_failure.tpl';
		} else {
			$this->template = 'default/template/payment/resurs_failure.tpl';
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

	
	public function getPaymentMethod($countryCode,$id){		
		$paymentMethods = $this->getPaymentMethods($countryCode);	
		foreach($paymentMethods->return as $paymentMethod){
			if($paymentMethod->id == $id){
				return $paymentMethod;
			}		
		}
	}
	
	public function bookSignedPayment($countryCode,$id){
			try { 
				$client = ResursUtils::getClientWithConfig($this->config,$countryCode,"SimplifiedShopFlowService");
				$params = array('paymentId'=>$id);		
				$result = $client->__soapCall("bookSignedPayment", array($params));			
			return $result;
			
		}catch (Exception $e) { 
			ResursUtils::log("Error failed to bookSignedPayment:".$id.":".$e->getMessage());
		}
	}
	
	public function book($paymentData,$countryCode){		
			try { 
				$client =  ResursUtils::getClientWithConfig($this->config,$countryCode,"SimplifiedShopFlowService");
				$result = $client->__soapCall("bookPayment",array('parameters'=>$paymentData)			
			);	
			return $result;
			
		}catch (Exception $e) { 
			ResursUtils::log("Error failed to book payment:".$e->getMessage());
		}
	}
	
	public function setPaymentMethodInSession(){
			$paymentMethodId = $this->request->get['paymentMethodId'];
			$this->session->data['paymentMethodId'] = $paymentMethodId;
	}
	
	public function getPaymentMethods($countryCode){	
		try { 
			return ResursUtils::getPaymentMethodsWithConfig($this->config,$countryCode);
			
		}catch (Exception $e) { 
			ResursUtils::log("Error failed to get PaymentMethods:".$e->getMessage());
		}		
		return ;
	}


}