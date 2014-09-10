<?php
require_once(DIR_SYSTEM . '../admin/model/payment/resurs_util.php');
class ModelTotalResursFee extends Model {
    public function getTotal(&$total_data, &$total, &$taxes) {

		$status = true;

		$resurs_fee = $this->config->get('resurs_fee');

		if(!$this->cart->getSubTotal() || !$this->cart->getSubTotal()){
			$status=false;
		}
		
		if (isset($this->session->data['payment_address_id'])) {
			$this->load->model('account/address');
			$address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
		} elseif (isset($this->session->data['guest']['payment'])) {
			$address = $this->session->data['guest']['payment'];
		}elseif (isset($this->session->data['payment_address'])) {
			$address = $this->session->data['payment_address'];
		}

		if (!isset($address)) {
			$status = false;
		} elseif (!isset($this->session->data['payment_method']['code']) || $this->session->data['payment_method']['code'] != 'resurs') {
			$status = false;
		} 

        if ($status && isset($this->session->data['paymentMethodId'])) {
			$paymentMethodId = $this->session->data['paymentMethodId'];

			if(isset($resurs_fee[$address['iso_code_3']]['paymentmethod'][$paymentMethodId])) {

				$fees = $resurs_fee[$address['iso_code_3']]['paymentmethod'][$paymentMethodId];			
	
				if($fees['fee'] > 0) {

					$tax_rates = $this->tax->getRates($fees['fee'], $fees['fee_tax_class_id']);
					
					foreach ($tax_rates as $tax_rate) {
						if (!isset($taxes[$tax_rate['tax_rate_id']])) {
							$taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
						} else {
							$taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
						}
					}
					
					$total_data[] = array(
						'code'       => 'resurs_fee',
						'title'      => $fees['invoice_line'],
						'text'       => $this->currency->format($fees['fee']),
						'value'      => $fees['fee'],
						'sort_order' =>  $this->config->get('resurs_fee_sort_order')
					);
					$total += $fees['fee'];
				}
			
			}
        }
    }
}