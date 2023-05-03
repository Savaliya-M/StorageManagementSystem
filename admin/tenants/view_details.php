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
    #uni_modal .modal-footer{
        display:none
    }
</style>
<div class="container fluid">
    <callout class="callout-primary">
        <dl class="row">
            <dt class="col-md-4">Tenant</dt>
            <dd class="col-md-8">: <?php echo $fullname ?></dd>
            <dt class="col-md-4">Gender</dt>
            <dd class="col-md-8">: <?php echo $gender ?></dd>
            <dt class="col-md-4">DoB</dt>
            <dd class="col-md-8">: <?php echo date("M d,Y",strtotime($dob)) ?></dd>
            <dt class="col-md-4">Contact</dt>
            <dd class="col-md-8">: <?php echo $contact ?></dd>
            <dt class="col-md-4">Address</dt>
            <dd class="col-md-8">: <?php echo $address ?></dd>
            <dt class="col-md-4">Provided ID Type</dt>
            <dd class="col-md-8">: <?php echo $id_type ?></dd>
            <dt class="col-md-4">Provided ID #/Code</dt>
            <dd class="col-md-8">: <?php echo $id_no ?></dd>
        </dl>
    </callout>
    <div class="row px-2 justify-content-end">
        <div class="col-1">
            <button class="btn btn-dark btn-flat btn-sm" type="button" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>