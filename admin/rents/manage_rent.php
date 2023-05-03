<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `rent_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<style>
    span.select2-selection.select2-selection--single {
        border-radius: 0;
        padding: 0.25rem 0.5rem;
        padding-top: 0.25rem;
        padding-right: 0.5rem;
        padding-bottom: 0.25rem;
        padding-left: 0.5rem;
        height: auto;
    }
</style>
<div class="card card-outline card-info">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Update Rent Details": "New Rent" ?> </h3>
	</div>
	<div class="card-body">
		<form action="" id="rent-form">
			<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">

            <div class="col-md-6 form-group">
			<label for="tenant_id">Tenant</label>
			<select name="tenant_id" id="tenant_id" class="custom-select custom-select-sm rounded-0 select2">
					<option value="" disabled <?php echo !isset($tenant_id) ? "selected" :'' ?>></option>
					<?php 
						$tenant_qry = $conn->query("SELECT * FROM `tenants` order by fullname asc");
						while($row = $tenant_qry->fetch_assoc()):
					?>
					<option value="<?php echo $row['id'] ?>" <?php echo isset($tenant_id) && $tenant_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['fullname'] ?></option>
					<?php endwhile; ?>
				</select>
			</div>
			<div class="col-md-6 form-group">
				<label for="unit_id">Storage Unit #</label>
				<select name="unit_id" id="unit_id" class="custom-select rounded-0 select2">
					<option value="" disabled <?php echo !isset($unit_id) ? "selected" :'' ?>></option>
					<?php 
						$units_qry = $conn->query("SELECT * FROM `unit_list` where `status` = 0 ".(isset($unit_id) ? " or id = '{$unit_id}'" : '')." order by unit_number asc");
						while($row = $units_qry->fetch_assoc()):
					?>
					<option value="<?php echo $row['id'] ?>" <?php echo isset($unit_id) && $unit_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['unit_number'] ?></option>
					<?php endwhile; ?>
				</select>
			</div>
			<?php if(isset($unit_id)): ?>
				<script>
					$('#unit_id option').each(function(){
						if($(this).attr('value') != '<?php echo $unit_id ?>'){
							$(this).attr('disabled',true)
						}else{
							$(this).attr('disabled',false)
						}
					})
				</script>
			<?php endif; ?>
			<div class="form-group col-md-6">
				<label for="rent_type" class="control-laberl">Type</label>
				<select name="rent_type" id="rent_type" class="form-control form-control-sm" required>
					<option value="1" <?php echo isset($rent_type) && $rent_type == 1 ? 'selected' : '' ?>>Monthly</option>
					<option value="2" <?php echo isset($rent_type) && $rent_type == 2 ? 'selected' : '' ?>>Quarterly</option>
					<option value="3" <?php echo isset($rent_type) && $rent_type == 3 ? 'selected' : '' ?>>Annually</option>
				</select>
			</div>
			<div class="form-group col-md-6">
				<label for="billing_amount" class="control-laberl">Amount</label>
				<input type="text" id="billing_amount" name="billing_amount" class="form-control rounded-0 text-right" readonly="readonly" value="<?php echo isset($billing_amount) ? $billing_amount : 0 ?>" required>
			</div>
			<div class="form-group col-md-6">
				<label for="date_rented" class="control-laberl">Rent Date</label>
				<input type="date" id="date_rented" name="date_rented" class="form-control rounded-0 text-right" value="<?php echo isset($date_rented) ? $date_rented : date("Y-m-d") ?>" required>
			</div>
			<div class="form-group col-md-6">
				<label for="" class="control-laberl">Status</label>
				<select name="status" id="status" class="form-control form-control-sm" required>
					<option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
					<option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
				</select>
			</div>
		</form>
	</div>
	<div class="card-footer">
		<button class="btn btn-flat btn-primary" form="rent-form">Save</button>
		<a class="btn btn-flat btn-default" href="?page=rents">Cancel</a>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#rent_type,#unit_id').on('keyup input change',function(){
			if($('#rent_type').val() == '' && $('#unit_id').val()=='')
			return false;

			var unit_id = $('#unit_id').val()
			var rent_type = $('#rent_type').val()
			$.ajax({
				url:_base_url_+"classes/Master.php?f=get_price",
				method:'POST',
				data:{unit_id:unit_id,rent_type:rent_type},
				dataType:"json",
				error:err=>{
					console.log(error)
					alert_toast("An error occured","error")
				},
				success:function(resp){
					if(!!resp.price && ($.isNumeric(resp.price) || resp.price==0)){
						$('#billing_amount').val(resp.price)
					}else{
					alert_toast("An error occured while fetching the price","error")
					}
				}
			})
		})
        $('.select2').select2({placeholder:"Please Select here",width:"relative"})
		$('#rent-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			 if($('#schedule_id').val() <= 0){
				alert_toast(" Schedule is required.",'warning')
				$('#schedule_id').focus()
				return false;
			}
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_rent",
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
						location.href = "./?page=rents";
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
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