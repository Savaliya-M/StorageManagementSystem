<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `price_list` where unit_id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<div class="container-fluid">
	<form action="" id="pricing-form">
		<input type="hidden" name ="id" value="<?php echo isset($unit_id) ? $unit_id : '' ?>">
		<div class="form-group">
			<label for="unit_id" class="control-label">Storage Unit</label>
			<select name="unit_id" id="unit_id" class="form-control form-control-sm rounded-0 select2" required <?php echo isset($unit_id) ? "readonly" : ""  ?>>
				<option value="" disabled <?php echo !isset($unit_id) ? "selected" : '' ?>></option>
			<?php 
			$unit = $conn->query("SELECT * FROM unit_list order by unit_number asc");
			while($row = $unit->fetch_assoc()):
			?>
				<option value="<?php echo $row['id'] ?>" <?php echo isset($unit_id) && $unit_id == $row['id'] ? "selected" : ""  ?>><?php echo $row['unit_number'] ?></option>
			<?php endwhile; ?>
			</select>
		</div>
		<div class="form-group row px-2">
			<label for="monthly" class="control-label col-md-4">Montly</label>
			<input type="text" id="monthly" name="monthly" title="Enter Valid Amount" pattern="[0-9.]{0,}" class="form-control form-control-sm rounded-0 text-right col-md-8" value="<?php echo isset($monthly) ? $monthly :'' ?>" required>
		</div>
		<div class="form-group row px-2">
			<label for="quarterly" class="control-label col-md-4">Quarterly</label>
			<input type="text" id="quarterly" name="quarterly" title="Enter Valid Amount" pattern="[0-9.]{0,}" class="form-control form-control-sm rounded-0 text-right col-md-8" value="<?php echo isset($quarterly) ? $quarterly :'' ?>" required>
		</div>
		<div class="form-group row px-2">
			<label for="annually" class="control-label col-md-4">Annually</label>
			<input type="text" id="annually" name="annually" title="Enter Valid Amount" pattern="[0-9.]{0,}" class="form-control form-control-sm rounded-0 text-right col-md-8" value="<?php echo isset($annually) ? $annually :'' ?>" required>
		</div>
		
	</form>
</div>
<script>
  
	$(document).ready(function(){
		$('#monthly,#quarterly,#annually').on('input keyup',function(){
			$(this).removeClass('border-danger')
				$(this).siblings('.amount_err_msg').remove()
				console.log($.isNumeric($(this).val()))
			if(!$.isNumeric($(this).val())){
				$(this).addClass('border-danger').after("<div class='text-danger amount_err_msg'>Please enter a valid amount.</div>")
			}
		})
        $('.select2').select2({placeholder:"Please Select here",width:"relative"})
		$('#pricing-form').submit(function(e){
			e.preventDefault();
			if($('.amount_err_msg').length > 0){
				alert_toast('Please comply the form\'s requirement first.',"warning")
				return false;
			}
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_pricing",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.reload();
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            end_loader()
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})
	})
</script>