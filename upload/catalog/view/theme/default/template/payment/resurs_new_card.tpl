<div class="form-group row">
	<label class="col-sm-2 control-label" for="resursextra_governmentid<?php echo $paymentMethod->id ?>"><span class="required">*</span>&nbsp;<?php echo $entry_governmentid ?></label>
	<div class="col-sm-4">
		<input type="text" name="resursextra" value="" id="resursextra_governmentid<?php echo $paymentMethod->id ?>" class="form-control resursinput resursgovernmentid" onblur="setField(this.id,this.value)"/>
		<span class="help-block"><?php echo $help_governmentid ?></span>
	</div>
</div>
				
<div class="form-group row">
	<label class="col-sm-2 control-label" for="newcardamount<?php echo $paymentMethod->id ?>"><span class="required">*</span>&nbsp;<?php echo $entry_cardamount ?></label>
	<div class="col-sm-4">	
		<select name="resursextra" id="newcardamount<?php echo $paymentMethod->id ?>" class="form-control resursinput" onchange="setField(this.id,this.value)">          
				<option value="<?php echo $total ?>"><?php echo $total ?></option>
				<?php
				for ($x=0; $x<=$paymentMethod->maxLimit; $x = $x+$paymentMethod->maxLimit/10) {    
					if($x > $total){
					?>
						<option value="<?php echo $x ?>"><?php echo $x ?></option>
					
					<?php
					}
				}?>		 
		</select>
		<span class="help-block"><?php echo $help_cardamount ?></span>
	</div>
</div>
