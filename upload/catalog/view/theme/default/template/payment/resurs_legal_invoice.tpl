<div class="form-group row">
	<label class="col-sm-2 control-label" for="resursextra_governmentid<?php echo $paymentMethod->id ?>"><span class="required">*</span>&nbsp;<?php echo $entry_governmentid_legal ?></label>
	<div class="col-sm-4">
		<input type="text" name="resursextra" value="" id="resursextra_governmentid<?php echo $paymentMethod->id ?>" class="form-control resursinput" onblur="setField(this.id,this.value)"/>
		<span class="help-block"><?php echo $help_governmentid_legal ?></span> 
	</div>
</div>
<div class="form-group row">
	 <label class="col-sm-2 control-label" for="resursextra_governmentid_contact<?php echo $paymentMethod->id ?>">
	 <span class="required">*</span>&nbsp;<?php echo $entry_governmentid_legal_contact ?></label>
	 <div class="col-sm-4">
		<input type="text" name="resursextra" value="" id="resursextra_governmentid_contact<?php echo $paymentMethod->id ?>" class="form-control resursinput" onblur="setField(this.id,this.value)"/>
		<span class="help-block"><?php echo $help_governmentid_legal_contact ?></span> 
	</div>
</div>
<input type="hidden" name="resursextra_type<?php echo $paymentMethod->id ?>" value="LEGAL"/>