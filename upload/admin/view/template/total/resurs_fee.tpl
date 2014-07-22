<?php echo $header; ?>

<?php /**echo $menu;**/ ?>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>


<div id="content">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <?php if ($error_warning) { ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
  <?php } ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <div class="pull-right">
        <!--
		<button type="submit" form="form-resurs" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn"><i class="fa fa-check-circle"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn"><i class="fa fa-reply"></i></a>
		-->
		
		<a onclick="$('#form-resurs').submit();" class="button"><?php echo $button_save; ?></a>
		<a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a>
		
		</div>
      <h1 class="panel-title"><i class="fa fa-credit-card fa-lg"></i> <?php echo $heading_title; ?></h1>
    </div>
	<div class="panel-body">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-resurs" class="form-horizontal">
		<input type="hidden" name="resurs_fee_status" value="1"/>
		<div class="tab-content">
          <div class="tab-pane active" id="tab-general">
            <ul class="nav nav-tabs" id="country">
              <?php foreach ($countries as $country) { ?>
              <li><a href="#tab-<?php echo $country['code']; ?>" data-toggle="tab"><?php echo $country['name']; ?></a></li>
              <?php } ?>
            </ul>
            <div class="tab-content">
              <?php foreach ($countries as $country) { ?>
              <div class="tab-pane" id="tab-<?php echo $country['code']; ?>">
				<br/>
			
				<table class="table"><thead><tr><th><?php echo $info_table_paymentmethod_id; ?></th>
				<th><?php echo $info_table_paymentmethod_name; ?></th><th><?php echo $info_table_paymentmethod_fee; ?></th>
				<th><?php echo $info_table_paymentmethod_taxclass; ?></th><th><?php echo $info_table_paymentmethod_invoiceline; ?></th></tr></thead>
				
					<?php 
					foreach ($resurs_fee[$country['code']]['paymentmethod'] as $paymentmethod) { 
						if($paymentmethod['enabled'] == 1) {	
					?>
						<tr>
							<td style="width:240px"><?php echo $paymentmethod['id']; ?><input type="hidden" name="resurs_fee[<?php echo $country['code']; ?>][paymentmethod][<?php echo $paymentmethod['id']; ?>][id]" id="resurs_fee[<?php echo $country['code']; ?>][paymentmethod][<?php echo $paymentmethod['id']; ?>][id]" value="<?php echo isset($paymentmethod['id']) ? $paymentmethod['id'] : ''; ?>"/></td>
							<td style="width:240px"><?php echo $paymentmethod['name']; ?></td>
							<td  style="width:240px"><input type="text" name="resurs_fee[<?php echo $country['code']; ?>][paymentmethod][<?php echo $paymentmethod['id']; ?>][fee]" id="resurs_fee[<?php echo $country['code']; ?>][paymentmethod][<?php echo $paymentmethod['id']; ?>][fee]" value="<?php echo isset($paymentmethod['fee']) ? $paymentmethod['fee'] : ''; ?>" placeholder="<?php echo $entry_fee; ?>" class="form-control" /></td>
							<td style="width:240px">
							  <div>
								<select name="resurs_fee[<?php echo $country['code']; ?>][paymentmethod][<?php echo $paymentmethod['id']; ?>][fee_tax_class_id]" id="resurs_fee[<?php echo $country['code']; ?>][paymentmethod][<?php echo $paymentmethod['id']; ?>][fee_tax_class_id]" class="form-control">
								  <option value="0"><?php echo $text_none; ?></option>
								  <?php foreach ($tax_classes as $tax_class) { ?>
								  <?php if (isset($resurs_fee[$country['code']]) && $resurs_fee[$country['code']]['paymentmethod'][$paymentmethod['id']]['fee_tax_class_id'] == $tax_class['tax_class_id']) { ?>
								  <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
								  <?php } else { ?>
								  <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
								  <?php } ?>
								  <?php } ?>
								</select>
							  </div>
							<td><input type="text" name="resurs_fee[<?php echo $country['code']; ?>][paymentmethod][<?php echo $paymentmethod['id']; ?>][invoice_line]" id="resurs_fee[<?php echo $country['code']; ?>][paymentmethod][<?php echo $paymentmethod['id']; ?>][invoice_line]" value="<?php echo isset($paymentmethod['invoice_line']) ? $paymentmethod['invoice_line'] : ''; ?>" placeholder="<?php echo $entry_invoice_line; ?>" class="form-control" /></td>
						</tr>
					<?php }
					} ?>
				</table>			 
               </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
$('#country a:first').tab('show');
</script> 

<?php echo $footer; ?> 