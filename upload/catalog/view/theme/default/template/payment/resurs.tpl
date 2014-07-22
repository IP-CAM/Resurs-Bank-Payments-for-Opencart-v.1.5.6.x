<form id="order_resurs_form" action="index.php?route=payment/resurs/createpayment" method="post">
  <input type="hidden" id="governmentId" name="governmentId" value="" />
  <input type="hidden" id="cardnumber" name="cardnumber" value="" />
  <input type="hidden" id="contcat" name="contcat" value="" />
  <input type="hidden" id="cardamount" name="cardamount" value="" />
  <input type="hidden" id="type" name="type" value="" />
  <input type="hidden" id="paymentmethodid" name="paymentmethodid" value="" /> 
</form> 
  <div class="buttons">
    <div class="pull-right">
      <!--<input type="submit" id="button-order2" value="<?php echo $button_confirm; ?>"  onClick="updateValue();" class="btn btn-primary" />-->
	  <input type="button" onClick="updateValue();" value="<?php echo $button_confirm; ?>" id="button-order" class="button" />
    </div>
  </div>
<script type="text/javascript"><!--
$('#button-order').die();
$('#button-order').live('click', function() {
	$.ajax({
		url: 'index.php?route=payment/resurs/createpayment',
		type: 'post',
		data: $("#order_resurs_form" ).serialize() ,
		dataType: 'json',
		beforeSend: function() {
			$('#button-order').attr('disabled', true);
			$('#button-order').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
		},	
		complete: function() {
			$('#button-order').attr('disabled', false); 
			$('.wait').remove();
		},			
		success: function(json) {
			$('.warning, .error').remove();
			
			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				
				$.each(json['error'], function(key, value) {
			
					//Do something
				
					if (key == 'warning') {
						$('#confirm .checkout-content').prepend('<div class="warning" style="display: none;">' + value + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');					
						$('.warning').fadeIn('slow');
					}
					
					else if (key  ==  'email') {					
						$('#payment-address input[name=\'email\'] + br').after('<span class="error">' + value + '</span>');
					}
					
					else if (key  ==  'telephone') {
						$('#payment-address input[name=\'telephone\'] + br').after('<span class="error">' + value + '</span>');
					}	
																			
					else{
						$('#'+key).after('<span class="error">' + value + '</span>');
					}
					
				});
			
			} 	 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
});

function updateValue(){
	document.getElementById('governmentId').value ="";
	document.getElementById('type').value ="";
	document.getElementById('cardnumber').value =""; 
	document.getElementById('cardamount').value ="";
	document.getElementById('contcat').value ="";

	var paymentId = $("input[name='payment_method']:checked")[0].attributes['paymentMethodId'].value;
	
	document.getElementById('paymentmethodid').value =  paymentId;
		
	var governmentId = document.getElementById('resursextra_governmentid'+paymentId);
	var carnumber = document.getElementById('resursextra_cardnumber'+paymentId);
	var newcardmount = document.getElementById('newcardamount'+paymentId);
	var concat = document.getElementById('resursextra_governmentid_contact'+paymentId);
	var type = document.getElementById('resursextra_type'+paymentId);

	if(type){
		document.getElementById('type').value = type.value;
	} else{
		document.getElementById('type').value = 'NATURAL'; 
	}
	if(governmentId){
		document.getElementById('governmentId').value = governmentId.value;
	}
	if(carnumber){
		document.getElementById('cardnumber').value =carnumber.value; 
	}
	if(newcardmount){
		document.getElementById('cardamount').value =newcardmount.value;
	}
	if(concat){
		document.getElementById('contcat').value =concat.value;
	}
}
//-->
</script>
