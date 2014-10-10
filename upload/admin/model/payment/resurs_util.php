<?php
class ResursUtils {

	public static function log($string) {
		global $log;
		$log->write($string);
	}
	
	public static function getServerURL($server){
		if($server == "test"){
			return "https://test.resurs.com/ecommerce-test/ws/V4/";
		}
		elseif($server == "production"){
			return "https://ecommerce.resurs.com/ws/V4/";
		}
		return "error";
	}
	
	public static function getEvents(){
		return array('UNFREEZE', 'AUTOMATIC_FRAUD_CONTROL', 'ANNULMENT','FINALIZATION');
	}
	
	public static function getClientWithConfig($config,$countryCode,$wsdl,$timeout = 30){
	
		$resurs = $config->get('resurs');	
		$server = $resurs[$countryCode]['server'];
		$password =$resurs[$countryCode]['password'];
		$login = $resurs[$countryCode]['username'];
	
		return  ResursUtils::getClient($login,$password,$server,$wsdl,$timeout);	
	}
	
	
	public static function getClient($login,$password,$server,$wsdl,$timeout = 30){
		try{		
			$variabled = array('login'=> $login,
			'password' => $password,
			"connection_timeout"=>3,
			"default_socket_timeout"=>$timeout);
			
			return new SoapClient(ResursUtils::getServerURL($server).$wsdl."?wsdl",$variabled);	
		}catch (Exception $e) { 
			ResursUtils::log("Error:".$e->getMessage());
			return;
		}
	}
	
	
	public static function getCountries(){
		return array('SWE','DNK','NOR','FIN');
	}
	
	public static function getCountriesToCurrency(){
		return array(
				'NOR' => 'NOK',
				'SWE' => 'SEK',
				'FIN' => 'EUR',
				'DNK' => 'DKK',
			);
	}
	
	
	public static function rand_string( $length ) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";  
		$size = strlen( $chars );
		$str = "";
		for( $i = 0; $i < $length; $i++ ) {
			$str .= $chars[ rand( 0, $size - 1 ) ];
		}
		return $str;
	}
	
	public static function isPaymentMethodEnabled($id,$countrycode,$config,$post){
		if (isset($post['resurs'])) {
			$currentResursData = $post['resurs'];
		} else {
			$currentResursData = $config->get('resurs');
		}
	
		if (isset($currentResursData[$countrycode]) && isset($currentResursData[$countrycode]['paymentmethods']) && !empty($currentResursData[$countrycode]['paymentmethods'])) {
			
			foreach(array_keys($currentResursData[$countrycode]['paymentmethods']) as $paramName){
				  if($paramName == $id) return true;
			}
		}
		return false;
	}
	
	public static function getAddress($config,$countryCode,$governmentId,$isNatural,$customerIP,$timeout = 30){
		try{
			$client = ResursUtils::getClientWithConfig($config,$countryCode,'ShopFlowService',$timeout);					
			
			$customerType = 'NATURAL';
			if(!$isNatural) {
				$customerType = 'LEGAL';
			}
			$params = array('governmentId'=>$governmentId,'customerType'=>$customerType,'customerIpAddress'=>$customerIP);		
		
			return $client->__soapCall("getAddress",array($params))->return;	
		}catch (Exception $e) { 
			ResursUtils::log("Error failed to get PaymentMethods:".$e->getMessage());
			return;
		}	
	}
	
	public static function getPaymentMethodsWithConfig($config,$countryCode,$timeout = 30){
		try{
			$client = ResursUtils::getClientWithConfig($config,$countryCode,'ShopFlowService',$timeout);					
			return $client->__soapCall("getPaymentMethods",array());	
		}catch (Exception $e) { 
			ResursUtils::log("Error failed to get PaymentMethods:".$e->getMessage());
			return;
		}	
		
	}
	
	public static function getPaymentMethods($login,$password,$server,$timeout = 30){
		try{
			$client = ResursUtils::getClient($login,$password,$server,'ShopFlowService',$timeout);					
			return $client->__soapCall("getPaymentMethods",array());	
		}catch (Exception $e) { 
			ResursUtils::log("Error failed to get PaymentMethods:".$e->getMessage());
			return;
		}		
	}
	
	public static function getCountriesLanguage($language){
		$mycountries = array();

		$countries = ResursUtils::getCountries();
		
		foreach($countries as $country){
			$mycountries[] = array(
				'name' => $language->get('resurs_text_'.$country),
				'code' => $country
			);
		}
		return $mycountries;
	}
	
	
	public static function getCountryCode($iso_3){
		if($iso_3 == 'SWE') {
			return "SE";
		}
		elseif($iso_3 == 'NOR') {
			return "NO";
		}
		elseif($iso_3 == 'DNK') {
			return "DK";
		}
		elseif($iso_3 == 'FIN') {
			return "FI";
		}	
	}
	
	
	public static function isInvoceOrNewAccount($paymentMethod){
		if($paymentMethod->type == 'INVOICE'){
			return true;
		}
		return false;
	}
	
	public static function isPartPayment($paymentMethod){
		if($paymentMethod->type == 'REVOLVING_CREDIT' && (
		stripos($paymentMethod->description,'del') !== false ||
		stripos($paymentMethod->description,'part') !== false)){
			return true;
		}
		return false;
	}
	
	public static function isNewCard($paymentMethod){
		if($paymentMethod->type == 'REVOLVING_CREDIT'){
			return true;
		}
		return false;
	}
	
	public static function isCard($paymentMethod){
		if($paymentMethod->type == 'CARD'){
			return true;
		}
		return false;
	}
	
	public static function isLegalInvoice($paymentMethod){
		if($paymentMethod->type == 'INVOICE' &&
		$paymentMethod->customerType == 'LEGAL') {
			return true;
		}
		return false;
	
	}
	
	public static function isValidPhoneNumner($phoneNumner,$countryCode){
	
		if($countryCode == 'SWE'){
			return preg_match('/^(0|\\+46)[ |-]?(200|20|70|73|76|74|[1-9][0-9]{0,2})([ |-]?[0-9]){5,8}$/',$phoneNumner);
		}
		elseif($countryCode == 'NOR'){
			return preg_match('/^(\\+47|)?[ |-]?[2-9]([ |-]?[0-9]){7,7}$/',$phoneNumner);
		}
		elseif($countryCode == 'DNK'){
			return preg_match('/^(\\+45|)?[ |-]?[2-9]([ |-]?[0-9]){7,7}$/',$phoneNumner);
		}
		elseif($countryCode == 'FIN'){
			return preg_match('/^(\\+358|0)[-| ]?(1[1-9]|[2-9]|[1][0][1-9]|201|2021|[2][0][2][4-9]|[2][0][3-8]|29|[3][0][1-9]|71|73|[7][5][0][0][3-9]|[7][5][3][0][3-9]|[7][5][3][2][3-9]|[7][5][7][5][3-9]|[7][5][9][8][3-9]|[5][0][0-9]{0,2}|[4][0-9]{1,3})([-| ]?[0-9]){3,10}$/',$phoneNumner);
		}
		return false;
	}
	
	public static function isValidNaturalGovernmnetId($governmentId,$countryCode){
		if($countryCode == 'SWE'){
			return preg_match('/^([0-9]{2})(0[1-9]|1[0-2])([0][1-9]|[1-2][0-9]|3[0-1])(\\-|\\+)?([\\d]{4})$/',$governmentId);
		}
		elseif($countryCode == 'NOR'){
			return preg_match('/^([0-9]{11})$/',$governmentId);
		}
		elseif($countryCode == 'DNK'){
			return preg_match('/^((3[0-1])|([1-2][0-9])|(0[1-9]))((1[0-2])|(0[1-9]))(\\d{2})(\\-)?([\\d]{4})$/',$governmentId);
		}
		elseif($countryCode == 'FIN'){
			return preg_match('/^([\\d]{6})[\\+\\-A]([\\d]{3})([0123456789ABCDEFHJKLMNPRSTUVWXY])$/',$governmentId);
		}
		return false;
	
	}
	
	
	public static function isValidLegalGovernmnetId($governmentId,$countryCode){
		if($countryCode == 'SWE'){
			return preg_match('/^(18\\d{2}|19\\d{2}|20\\d{2}|\\d{2})(0[1-9]|1[0-2])([0][1-9]|[1-2][0-9]|3[0-1])(\\-|\\+)?([\\d]{4})$/',$governmentId);
		}
		elseif($countryCode == 'NOR'){
			return preg_match('/^([0][1-9]|[1-2][0-9]|3[0-1])(0[1-9]|1[0-2])(\\d{2})(\\-)?([\\d]{5})$/',$governmentId);
		}
		elseif($countryCode == 'FIN'){
			return preg_match('/^((\\d{7})(\\-)?\\d)$/',$governmentId);
		}
		return false;
	
	}
	
	public static function isValidCardNumber($cardnumber,$countryCode){
		return preg_match('/^([1-9][0-9]{3}[ ]{0,1}[0-9]{4}[ ]{0,1}[0-9]{4}[ ]{0,1}[0-9]{4})$/',$cardnumber);
	}
	
	public static function isValidEmail($email,$countryCode){
		//^([A-Za-z0-9!#%&amp;'*+/=?^_`~-]+(\\.[A-Za-z0-9!#%&amp;'*+/=?^_`~-]+)*@([A-Za-z0-9]+)(([\\.\\-]?[a-zA-Z0-9]+)*)\\.([A-Za-z]{2,}))?$

		// The "i" after the pattern delimiter indicates a case-insensitive search
		//return preg_match("/^([A-Za-z0-9!#%&amp;'*+/=?^_`~-]+(\\.[A-Za-z0-9!#%&amp;'*+/=?^_`~-]+)*@([A-Za-z0-9]+)(([\\.\\-]?[a-zA-Z0-9]+)*)\\.([A-Za-z]{2,}))?$/",$email);
		return true;
	}
	
	public static function isValidContactName($contactName,$countryCode){
		return preg_match('/^[A-ZÅÄÖÜ\\- a-zåäöü]{6,100}$/',$contactName);

	}
	
	
}