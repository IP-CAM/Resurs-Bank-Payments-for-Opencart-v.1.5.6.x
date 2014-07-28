<?php echo $header; ?>

<?php 
/**
echo $menu; 
**/
?>

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
		<input type="hidden" name="resurs_status" value="1"/>
		<input type="hidden" name="resurs_salt" value="<?php echo $resurs_salt; ?>"/>

	   <div class="tab-content">
          <div class="tab-pane active" id="tab-general">
            <ul class="nav nav-tabs" id="country">
			  <li><a href="#tab-other" data-toggle="tab"><?php echo $text_other; ?></a></li>
              <?php foreach ($countries as $country) { ?>
              <li><a href="#tab-<?php echo $country['code']; ?>" data-toggle="tab"><?php echo $country['name']; ?></a></li>
              <?php } ?>
            </ul>
            <div class="tab-content">
			  <div class="tab-pane" id="tab-other">
				<br/>
			    <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-frozen_status_id"><?php echo $entry_frozen_status_id; ?></label>
                  <div class="col-sm-10">
                    <select name="resurs[frozen_status_id]" id="resurs[frozen_status_id]" class="form-control">
                      <?php foreach ($order_statuses as $order_status) { ?>
                      <?php if (isset($resurs['frozen_status_id']) && $order_status['order_status_id'] == $resurs['frozen_status_id']) { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                      <?php } else { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                      <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                </div>
				
				<div class="form-group">
                  <label class="col-sm-2 control-label" for="input-booked_status_id"><?php echo $entry_booked_status_id; ?></label>
                  <div class="col-sm-10">
                    <select name="resurs[booked_status_id]" id="input-stauts_id" class="form-control">
                      <?php foreach ($order_statuses as $order_status) { ?>
                      <?php if (isset($resurs['booked_status_id']) && $order_status['order_status_id'] == $resurs['booked_status_id']) { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                      <?php } else { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                      <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                </div>
				
				<div class="form-group">
                  <label class="col-sm-2 control-label" for="input-finalized_status_id"><?php echo $entry_finalized_status_id; ?></label>
                  <div class="col-sm-10">
                    <select name="resurs[finalized_status_id]" id="input-stauts_id" class="form-control">
                      <?php foreach ($order_statuses as $order_status) { ?>
                      <?php if (isset($resurs['finalized_status_id']) && $order_status['order_status_id'] == $resurs['finalized_status_id']) { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                      <?php } else { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                      <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                </div>
				
				<div class="form-group">
                  <label class="col-sm-2 control-label" for="input-annulled_status_id"><?php echo $entry_annulled_status_id; ?></label>
                  <div class="col-sm-10">
                    <select name="resurs[annulled_status_id]" id="input-stauts_id" class="form-control">
                      <?php foreach ($order_statuses as $order_status) { ?>
                      <?php if (isset($resurs['annulled_status_id']) && $order_status['order_status_id'] == $resurs['annulled_status_id']) { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                      <?php } else { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                      <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                </div>
				
			  	<div class="form-group">
                  <label class="col-sm-2 control-label" for="input-signing_status_id"><?php echo $entry_signing_status_id; ?></label>
                  <div class="col-sm-10">
                    <select name="resurs[signing_status_id]" id="input-stauts_id" class="form-control">
                      <?php foreach ($order_statuses as $order_status) { ?>
                      <?php if (isset($resurs['signing_status_id']) && $order_status['order_status_id'] == $resurs['signing_status_id']) { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                      <?php } else { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                      <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                </div>
				
				<div class="form-group">
                  <label class="col-sm-2 control-label" for="input-signing_status_id"><?php echo $entry_denied_status_id; ?></label>
                  <div class="col-sm-10">
                    <select name="resurs[denied_status_id]" id="input-denied_stauts_id" class="form-control">
                      <?php foreach ($order_statuses as $order_status) { ?>
                      <?php if (isset($resurs['denied_status_id']) && $order_status['order_status_id'] == $resurs['denied_status_id']) { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                      <?php } else { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                      <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                </div>
				
			  
			  </div>
			  <?php foreach ($countries as $country) { ?>
              <div class="tab-pane" id="tab-<?php echo $country['code']; ?>">
			  <br/>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-username<?php echo $country['code']; ?>"><?php echo $entry_username; ?></label>
                  <div class="col-sm-10">
                    <input type="text" name="resurs[<?php echo $country['code']; ?>][username]" value="<?php echo isset($resurs[$country['code']]) ? $resurs[$country['code']]['username'] : ''; ?>" placeholder="<?php echo $entry_username; ?>" id="input-username<?php echo $country['code']; ?>" class="form-control" />
                    <span class="help-block"><?php echo $help_username; ?></span> </div>
				</div>
				<div class="form-group">
                  <label class="col-sm-2 control-label" for="input-password<?php echo $country['code']; ?>"><?php echo $entry_password; ?></label>
                  <div class="col-sm-10">
                    <input type="password" name="resurs[<?php echo $country['code']; ?>][password]" value="<?php echo isset($resurs[$country['code']]) ? $resurs[$country['code']]['password'] : ''; ?>" id="input-password<?php echo $country['code']; ?>" class="form-control" />
                    <span class="help-block"><?php echo $help_password; ?></span> </div>
				</div>
			
				<div class="form-group">
                  <label class="col-sm-2 control-label" for="input-server<?php echo $country['code']; ?>"><?php echo $entry_server; ?></label>
                  <div class="col-sm-10">
                    <select name="resurs[<?php echo $country['code']; ?>][server]" id="input-server<?php echo $country['code']; ?>" class="form-control">
                      <?php if (isset($resurs[$country['code']]) && $resurs[$country['code']]['server'] == 'production') { ?>
                      <option value="production" selected="selected"><?php echo $text_production; ?></option>
                      <?php } else { ?>
                      <option value="production"><?php echo $text_production; ?></option>
                      <?php } ?>
                      <?php if (isset($resurs[$country['code']]) && $resurs[$country['code']]['server'] == 'test') { ?>
                      <option value="test" selected="selected"><?php echo $text_test; ?></option>
                      <?php } else { ?>
                      <option value="test"><?php echo $text_test; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>			
				<div class="form-group">
                  <label class="col-sm-2 control-label" for="paymentmethods<?php echo $country['code']; ?>"><?php echo $entry_paymentmethods; ?></label>
                  <div class="col-sm-10" name="paymentmethods<?php echo $country['code']; ?>">
				  </div>
				</div>
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




function loadPaymentMethods(countryCode){
	var  password = $('[name="resurs['+countryCode+'][password]"]').val();
	var username = $('[name="resurs['+countryCode+'][username]"]').val();
	var server = $('[name="resurs['+countryCode+'][server]"]').val();
	
	//always clear here. 
	$('[name="paymentmethods'+countryCode+'"]').empty();
	
	if(password.length == 0 || username.length == 0){
		return;
	}
	
	var dataMap = {
	    countryCode : countryCode,
	    username: username,
		password: password,
		server: server
	};

	$.post( "index.php?route=payment/resurs/getPaymentMethods&token="+QueryString.token, dataMap)
	.done(function( data ) {
		try{
			var html = '<table class="table"><thead><tr><th><?php echo $info_paymentmethod_checkbox; ?></th><th><?php echo $info_paymentmethod_id; ?></th><th><?php echo $info_paymentmethod_name; ?></th> <th><?php echo $info_custom_paymentmethod_name; ?></th></tr>';
			
			html= html+'<tr><th></th><th><?php echo $info_imageURL; ?></th><th><?php echo $info_imageWidth; ?></th><th><?php echo $info_imageHeight; ?></th></tr></thead>';
			var obj = jQuery.parseJSON(data);
			$.each( obj.paymentmethods, function( i, l ){
				
				var checked = "";
				if(l.enabled == "true") checked = "checked";

				html = html + '<tr>';	
				html = html + '<td><input type="checkbox" name="resurs['+countryCode+'][paymentmethods]['+l.id+']"  '+checked+' value="true" id="input-paymentmethods'+countryCode+''+l.id+'" class="form-control" /></td>';	
				html = html + '<td>'+l.id+'</td>'
				html = html + '<td>'+l.description+'</td>'
				html = html + '<td><input type="text" name="resurs['+countryCode+'][paymentmethod'+l.id+'][customDescription]" value="'+l.customDescription+'" class="form-control" /></td>'				
				html = html + '</tr><tr><td></td>'
				html = html + '<td><input style="width:500px"  type="text" name="resurs['+countryCode+'][paymentmethod'+l.id+'][imageURL]" value="'+l.imageURL+'" class="form-control" /></td>';
				html = html + '<td><input style="width:40px" type="text" name="resurs['+countryCode+'][paymentmethod'+l.id+'][imageWidth]" value="'+l.imageWidth+'" class="form-control" /></td>';
				html = html + '<td><input style="width:40px" type="text" name="resurs['+countryCode+'][paymentmethod'+l.id+'][imageHeight]" value="'+l.imageHeight+'" class="form-control" /></td>';				
				html = html + '</tr><tr><td colspan=4 style="background-color: #DDDDDD;height:1px;"></td></tr>';
			});
			
			html = html + "</table>"; 
			$('[name="paymentmethods'+countryCode+'"]').html(html);
		}catch (e) {
			$('[name="paymentmethods'+countryCode+'"]').html(data);
		}
		
	});
  
}

var QueryString = function () {

  var query_string = {};
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    if (typeof query_string[pair[0]] === "undefined") {
      query_string[pair[0]] = pair[1];
    } else if (typeof query_string[pair[0]] === "string") {
      var arr = [ query_string[pair[0]], pair[1] ];
      query_string[pair[0]] = arr;
    } else {
      query_string[pair[0]].push(pair[1]);
    }
  } 
  return query_string;
} ();

<?php foreach ($countries as $country) { ?>
$('[name="resurs[<?php echo $country['code']; ?>][password]"]' ).change(function() {
	loadPaymentMethods('<?php echo $country['code']; ?>');
});
$('[name="resurs[<?php echo $country['code']; ?>][username]"]' ).change(function() {
	loadPaymentMethods('<?php echo $country['code']; ?>');
});
$('[name="resurs[<?php echo $country['code']; ?>][server]"]' ).change(function() {
	loadPaymentMethods('<?php echo $country['code']; ?>');
});

loadPaymentMethods('<?php echo $country['code']; ?>');
$('#country a:first').tab('show');
<?php } ?>

</script> 

<?php echo $footer; ?> 