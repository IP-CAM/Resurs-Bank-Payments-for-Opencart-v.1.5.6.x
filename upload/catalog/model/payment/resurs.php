<?php
require_once(DIR_SYSTEM . '../admin/model/payment/resurs_util.php');
class ModelPaymentResurs extends Model {
	public function getMethod($address, $total) {
		$this->load->language('payment/resurs');
		$status = true;
			
		$resurs = $this->config->get('resurs');

		if (!isset($resurs[$address['iso_code_3']])) {
			$status = false;
		}

		if ($status) {
			// Maps countries to currencies
			$country_to_currency = ResursUtils::getCountriesToCurrency();
	
			if (!isset($country_to_currency[$address['iso_code_3']]) || !$this->currency->has($country_to_currency[$address['iso_code_3']])) {
				$status = false;
			}
			if($this->currency->getCode() != $country_to_currency[$address['iso_code_3']]) {
				$status = false;
			}
		}
	
		$method_data = array();

		if ($status) {
			
			$paymentMethods = $this->getPaymentMethods($address['iso_code_3']);
			/**
			$title = '</label></div>';
			**/
			$title = '</td></tr>';
			
			$title = $title.'<script>
			function hideAll(){
				$(\'[name="payment_method_extrainfo"]\').hide();
			}
			function show(id){
				$(\'#paymentMethodContent\'+id+\'\').show();
				$.get("index.php?route=payment/resurs/setPaymentMethodInSession&paymentMethodId="+id);
				
			}
			</script>';
		
			$i = 0;
			if(isset($paymentMethods) && isset($paymentMethods->return)) {
				usort($paymentMethods->return , function($a, $b){
					if ($a->id == $b->id)
					{
						return 0;
					}
					else if ($a->id > $b->id)
					{
						return -1;
					}
					else {
						return 1;
					}
				});
				foreach($paymentMethods->return as $paymentMethod) {	
					
				
					if($total < $paymentMethod->maxLimit && $total > $paymentMethod->minLimit && $this->isPaymentMethodEnabled($paymentMethod->id,$address['iso_code_3'])) {
							
							$paymentMethodFee = $this->getPaymentMethodFee($address['iso_code_3'],$paymentMethod->id);

							$checked ='';
							$displayContent = 'display:none;';
							if($i == 0){
								$checked ='checked="checked"';
								$displayContent = '';
							}	
							
							
							$image_height = '';
							$image_width = '';

							$imageurl = $this->config->get('config_url').'catalog/view/image/payment/resurs.png';

							$description = $paymentMethod->description;
							if(isset($resurs[$address['iso_code_3']]['paymentmethod'.$paymentMethod->id])
							&& strlen($resurs[$address['iso_code_3']]['paymentmethod'.$paymentMethod->id]['customDescription']) > 0){
							$description = $resurs[$address['iso_code_3']]['paymentmethod'.$paymentMethod->id]['customDescription']; 
							}
							if(isset($resurs[$address['iso_code_3']]['paymentmethod'.$paymentMethod->id])
							&& strlen($resurs[$address['iso_code_3']]['paymentmethod'.$paymentMethod->id]['imageURL']) > 0){
								$imageurl = $resurs[$address['iso_code_3']]['paymentmethod'.$paymentMethod->id]['imageURL']; 
							}
							if(isset($resurs[$address['iso_code_3']]['paymentmethod'.$paymentMethod->id])
							&& $resurs[$address['iso_code_3']]['paymentmethod'.$paymentMethod->id]['imageWidth']){
							$image_width = $resurs[$address['iso_code_3']]['paymentmethod'.$paymentMethod->id]['customDescription']; 
							}
							if(isset($resurs[$address['iso_code_3']]['paymentmethod'.$paymentMethod->id])
							&& $resurs[$address['iso_code_3']]['paymentmethod'.$paymentMethod->id]['imageHeight'] > 0){
								$image_height = $resurs[$address['iso_code_3']]['paymentmethod'.$paymentMethod->id]['imageHeight']; 
							}
							
													
							
							$title = $title.$this->view('default/template/payment/resurs_radio.tpl',
							array("paymentMethod"=>$paymentMethod,
							"description"=>$description,
							"imageurl"=>$imageurl,
							"image_width"=>$image_width,
							"image_height"=>$image_height,
							"checked"=>$checked,
							"paymentMethodFee"=>$this->currency->format($paymentMethodFee),
							"total"=>$total,
							"extrafields"=>$this->getExtraFields($paymentMethod,$address['iso_code_3'],$total+$paymentMethodFee),
							"legallinks"=>$this->getLegalLinks($paymentMethod,$total+$paymentMethodFee)
							,"displayContent"=>$displayContent
							));

						 $i++;
					}				
				}
				$title = $title."<script>$(\"input[name='payment_method'][value='resurs'][resurs!='true']\").parent().parent().remove()</script>";

				$method_data = array(
					'code'       => 'resurs',
					'title'      => $title,
					'terms'      => '',
					'sort_order' => $this->config->get('resurs_sort_order'),
				);
				$this->session->data['payment_methods'] = $method_data;
				return $method_data;
			}
			ResursUtils::log("Failed to populate any Payment Methods for Resurs Bank");
			return array();
		}

	}
	
	private function getPaymentMethodFee($countryCode,$paymentMethodId){
		$resurs_fee = $this->config->get('resurs_fee');
		$fees = $resurs_fee[$countryCode]['paymentmethod'][$paymentMethodId];	
		$tax_rates = $this->tax->getRates($fees['fee'], $fees['fee_tax_class_id']);
		$vat = 0;
		foreach ($tax_rates as $tax_rate) {
			$vat  += $tax_rate['amount'];
		}
		return $fees ['fee']+$vat;
	}
	
	
	private function getExtraFields($paymentMethod,$countryCode,$total){
		$this->load->language('payment/resurs');
		$extrafields ="";
		if(ResursUtils::isLegalInvoice($paymentMethod)){
			$extrafields = $this->view('default/template/payment/resurs_legal_invoice.tpl',
				array("paymentMethod"=>$paymentMethod,
				"entry_governmentid_legal"=>$this->language->get('entry_governmentid_legal'),
				"help_governmentid_legal"=>$this->language->get('help_governmentid_legal'),
				"entry_governmentid_legal_contact"=>$this->language->get('entry_governmentid_legal_contact'),
				"help_governmentid_legal_contact"=>$this->language->get('help_governmentid_legal_contact'))
			);
		}
		elseif(ResursUtils::isInvoceOrNewAccount($paymentMethod)) {
			$extrafields = $this->view('default/template/payment/resurs_natural_invoice.tpl',
				array("paymentMethod"=>$paymentMethod,
				"entry_governmentid"=>$this->language->get('entry_governmentid'),
				"help_governmentid"=>$this->language->get('help_governmentid'),
				"help_invoice"=>$this->language->get('help_invoice'))
			);
		}
		elseif(ResursUtils::isPartPayment($paymentMethod)) {
			$extrafields = $this->view('default/template/payment/resurs_natural_partpayment.tpl',
				array("paymentMethod"=>$paymentMethod,
				"entry_governmentid"=>$this->language->get('entry_governmentid'),
				"help_governmentid"=>$this->language->get('help_governmentid'),
				"help_part"=>$this->language->get('help_part'))
			);
		}
		elseif(ResursUtils::isCard($paymentMethod)) {
			$extrafields = $this->view('default/template/payment/resurs_card.tpl',
				array("paymentMethod"=>$paymentMethod,
				"entry_governmentid"=>$this->language->get('entry_governmentid'),
				"help_governmentid"=>$this->language->get('help_governmentid'),
				"entry_cardnumber"=>$this->language->get('entry_cardnumber'),
				"help_cardnumber"=>$this->language->get('help_cardnumber'))
			);
		}elseif(ResursUtils::isNewCard($paymentMethod)) {	

			$extrafields = $this->view('default/template/payment/resurs_new_card.tpl',
				array("paymentMethod"=>$paymentMethod,
				"entry_governmentid"=>$this->language->get('entry_governmentid'),
				"help_governmentid"=>$this->language->get('help_governmentid'),
				"total"=>$total,
				"help_cardamount"=>$this->language->get('help_cardamount'),
				"entry_cardamount"=>$this->language->get('entry_cardamount')
				)				
			);
		}
		return $extrafields;
	}
	
	private function getLegalLinks($paymentMethod,$total){
		$this->load->language('payment/resurs');
		$linkHTML = "<label><b>".$this->language->get('label_priceinformation')."</b></label>";
		if(isset($paymentMethod->legalInfoLinks)) {
			foreach($paymentMethod->legalInfoLinks as $legalInfoLink){
				$url = $legalInfoLink->url;
				if(isset($legalInfoLink->appendPriceLast) && $legalInfoLink->appendPriceLast==1){
					$url = $url.(int)$total;
				}
				$linkHTML = $linkHTML."<a target='_blank' href='".$url."'>".$legalInfoLink->endUserDescription."</a><br/>";			
			}
		}
		return $linkHTML;
	}
	
	private function isPaymentMethodEnabled($id,$countrycode){
		$currentResursData = $this->config->get('resurs');
	
		if (isset($currentResursData[$countrycode]) && isset($currentResursData[$countrycode]['paymentmethods']) && !empty($currentResursData[$countrycode]['paymentmethods'])) {			
			foreach(array_keys($currentResursData[$countrycode]['paymentmethods']) as $paramName){
				  if($paramName == $id) return true;
			}
		}
		return false;
	}
	
	
	public function getPaymentMethods($countryCode){
		try { 
			return ResursUtils::getPaymentMethodsWithConfig($this->config,$countryCode,5);
		}catch (Exception $e) { 
				 ResursUtils::log("Error, failed to get Payment Methods:".$e->getMessage());
		}	
		return ;
	}
	
	public function view($template, $data = array()) {
		$file = DIR_TEMPLATE . $template;
		if (file_exists($file)) {
			extract($data);

			ob_start();

			require($file);

			$output = ob_get_contents();

			ob_end_clean();

			return $output;
		} else {
			trigger_error('Error: Could not load template ' . $file . '!');
			exit();
		}
	}
}

