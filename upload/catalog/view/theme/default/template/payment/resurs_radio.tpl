<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/resurs.css" />
<tr>
	<td>
		<input type="radio" name="payment_method" resurs="true" 
		paymentMethodId="<?php echo $paymentMethod->id ?>" value="resurs" 
		<?php echo $checked ?>" 
		onclick="hideAll();show('<?php echo $paymentMethod->id ?>');"> 
	</td>
<td>
	<img src="<?php echo $imageurl ?>" alt="Resurs PaymentMethod" height="<?php echo $image_height ?>" width="<?php echo $image_width ?>"> 
</td>

<td>
	<label>
		<?php echo $description ?>
		<?php
			if($paymentMethodFee > 0) {
				echo '&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;'.$paymentMethodFee;			
			}
		?>
	<label>
	</td>
</tr>

<tr>
	<td>
	</td>
	<td colspan="2">
		<div name="payment_method_extrainfo" 
		id="paymentMethodContent<?php echo $paymentMethod->id?>" 
		style ="<?php echo $displayContent ?>" >
			<?php echo $extrafields ?>
			<br/>
			<?php echo $legallinks ?>
		</div>
		<br/>
	</td>
</tr>
