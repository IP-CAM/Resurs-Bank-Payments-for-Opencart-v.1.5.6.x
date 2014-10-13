<script><!--

<?php if($this->session->data['currency'] == 'SEK') : ?>
$("#search-governmentId-div").show();
<?php endif; ?>

var errormessage = "<?php echo isset($error_getaddress) ? $error_getaddress : '�r inte ett giltigt person/orginisations-nummer.'; ?>";

$( "#button-search-governmentId" ).click(function() {
	var governmentId =  $("#search-governmentId")[0].value;
	
	if(isValidNaturalSe(governmentId)) {
		loadAddress(governmentId,true);
	} else if(isValidLegalSe(governmentId)){
		loadAddress(governmentId,false);
	} else{
		alert(errormessage);
	}
});


function loadAddress(governmentId,isNatural){
	$.ajax({
		url: 'index.php?route=payment/resurs/getAddress&governmentId='+governmentId+'&isNatural='+isNatural,
		dataType: 'json',
		success: function(data) {
		
		if(data)  {
			$('.resursgovernmentid').val(governmentId);
			if(isNatural) {
				$('[name="firstname"]').val(data.firstName).prop('disabled', true);
				$('[name="lastname"]').val(data.lastName).prop('disabled', true);
			} else {			
				$('[name="company"]').val(data.fullName).prop('disabled', true);
			}
			$('[name="address_1"]').val(data.addressRow1).prop('disabled', true);
			$('[name="address_2"]').val(data.addressRow2).prop('disabled', true);
			$('[name="city"]').val(data.postalArea).prop('disabled', true);
			$('[name="postcode"]').val(data.postalCode).prop('disabled', true);
			$('[name="country_id"]').val(203).prop('disabled', true);
		} else {
			$('[name="company"]').val('').prop('disabled', false);
			$('[name="firstname"]').val('').prop('disabled', false);
			$('[name="lastname"]').val('').prop('disabled', false);
			$('[name="address_1"]').val('').prop('disabled', false);
			$('[name="address_2"]').val('').prop('disabled', false);
			$('[name="city"]').val('').prop('disabled', false);
			$('[name="postcode"]').val('').prop('disabled', false);
			$('[name="country_id"]').val('').prop('disabled', false);
			alert(errormessage);
		}		
	}});	
}


function isValidLegalSe(governmentId) {
    var pattern = new RegExp(/^(16\d{2}|18\d{2}|19\d{2}|20\d{2}|\d{2})(\d{2})(\d{2})(\-|\+)?([\d]{4})$/);
    return pattern.test(governmentId);
};
function isValidNaturalSe(governmentId) {
	var pattern = new RegExp(/(18\d{2}|19\d{2}|20\d{2}|\d{2})(0[1-9]|1[0-2])([0][1-9]|[1-2][0-9]|3[0-1])(\-|\+)?([\d]{4})$/);
    return pattern.test(governmentId);
};

//--></script>