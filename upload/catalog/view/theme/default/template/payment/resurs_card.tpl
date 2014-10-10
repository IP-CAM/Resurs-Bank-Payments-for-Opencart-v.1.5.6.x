<div class="form-group row">
	<label class="col-sm-2 control-label" for="resursextra_governmentid<?php echo $paymentMethod->id ?>"><span class="required">*</span>&nbsp;<?php echo $entry_governmentid ?></label>
	<div class="col-sm-4">
		<input type="text" name="resursextra" value="" id="resursextra_governmentid<?php echo $paymentMethod->id ?>" class="form-control resursinput resursgovernmentid" onblur="setField(this.id,this.value)"/>
		<span class="help-block"><?php echo $help_governmentid ?></span> 
	</div>
</div>
<div class="form-group row">
	<label class="col-sm-2 control-label" for="resursextra_cardnumber<?php echo $paymentMethod->id ?>"><span class="required">*</span>&nbsp;<?php echo $entry_cardnumber ?></label>
	<div class="col-sm-4">
		<input type="text" name="resursextra" value="" id="resursextra_cardnumber<?php echo $paymentMethod->id ?>" class="form-control resursinput" onblur="setField(this.id,this.value)"/>
		<span class="help-block"><?php echo $help_cardnumber ?></span> 
	</div>
</div>
			