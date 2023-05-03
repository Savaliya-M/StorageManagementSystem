<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `tenants` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
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
<form action="" id="tenant-form">
     <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fullname" class="control-label">FullName</label>
                    <input type="text" name="fullname" id="fullname" class="form-control rounded-0" value="<?php echo isset($fullname) ? $fullname :"" ?>" required>
                </div>
                <div class="form-group">
                    <label for="gender" class="control-label">Gender</label>
                    <select name="gender" id="gender" class="form-control rounded-0" required>
                        <option value="Male" <?php echo isset($gender) && $gender =="" ? "selected": "Male" ?> >Male</option>
                        <option value="Female" <?php echo isset($gender) && $gender =="" ? "selected": "Female" ?>>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="dob" class="control-label">Birth Date</label>
                    <input type="date" name="dob" id="dob" class="form-control rounded-0" value="<?php echo isset($dob) ? $dob :"" ?>" required>
                </div>
                <div class="form-group">
                    <label for="contact" class="control-label">Contact</label>
                    <input type="text" name="contact" id="contact" class="form-control rounded-0" value="<?php echo isset($contact) ? $contact :"" ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="address" class="control-label">Address</label>
                    <textarea rows="3" name="address" id="address" class="form-control rounded-0" required><?php echo isset($address) ? $address :"" ?></textarea>
                </div>
                <div class="form-group">
                    <label for="id_type" class="control-label">Provided ID Type</label>
                    <input type="text" name="id_type" id="id_type" class="form-control rounded-0" value="<?php echo isset($id_type) ? $id_type :"" ?>" required>
                </div>
                <div class="form-group">
                    <label for="id_no" class="control-label">Provided ID #/Code</label>
                    <input type="text" name="id_no" id="id_no" class="form-control rounded-0" value="<?php echo isset($id_no) ? $id_no :"" ?>" required>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    $(function(){
        $('#tenant-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_tenant",
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
                            $("html, body").animate({ scrollTop: 0 }, "fast");
                    }else{
						alert_toast("An error occured",'error');
                        console.log(resp)
					}
                    end_loader()
				}
			})
		})
	})
</script>