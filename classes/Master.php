<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function save_unit(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(isset($_POST['description'])){
			if(!empty($data)) $data .=",";
				$data .= " `description`='".addslashes(htmlentities($description))."' ";
		}
		$check = $this->conn->query("SELECT * FROM `unit_list` where `unit_number` = '{$unit_number}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Unit Number already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `unit_list` set {$data} ";
		}else{
			$sql = "UPDATE `unit_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Unit successfully saved.");
			else
				$this->settings->set_flashdata('success',"Unit successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_unit(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `unit_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Unit successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_pricing(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				$v = addslashes(trim($v));
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$check = $this->conn->query("SELECT * FROM `price_list` where `unit_id` = '{$unit_id}' ")->num_rows;
			if($this->capture_err())
				return $this->capture_err();
			if($check > 0){
				$resp['status'] = 'failed';
				$resp['msg'] = "Storage Unit already has a price list.";
				return json_encode($resp);
				exit;
			}
		}

		if(empty($id)){
			$sql = "INSERT INTO `price_list` set {$data} ";
		}else{
			$sql = "UPDATE `price_list` set {$data} where unit_id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"Unit Pricing successfully saved.");
			else
				$this->settings->set_flashdata('success',"Unit Pricing  successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_pricing(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `price_list` where unit_id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Unit Pricing successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_tenant(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				$v = addslashes(trim($v));
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `tenants` where `id_type` = '{$id_type}' and `id_no` = '{$id_no}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Tenant already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `tenants` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `tenants` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New tenant successfully saved.");
			else
				$this->settings->set_flashdata('success',"tenant successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_tenant(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `tenants` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"tenant successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function get_price(){
		extract($_POST);
		 $qry = $this->conn->query("SELECT * FROM price_list where unit_id = '{$unit_id}'");
		 $this->capture_err();
		 if($qry->num_rows > 0){
			 $res = $qry->fetch_array();
			 switch($rent_type){
				 case '1':
					$resp['price'] = $res['monthly'];
					break;
				case '2':
					$resp['price'] = $res['quarterly'];
					break;
				case '3':
					$resp['price'] = $res['annually'];
					break;
			 }
		 }else{
			 $resp['price'] = "0";
		 }
		 return json_encode($resp);
	}
	function save_rent(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id')) && !is_array($_POST[$k])){
				if(!empty($data)) $data .=",";
				$v = addslashes($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		switch ($rent_type) {
			case 1:
				$data .= ", `date_end`='".date("Y-m-d",strtotime($date_rented.' +1 month'))."' ";
				break;
			
			case 2:
				$data .= ", `date_end`='".date("Y-m-d",strtotime($date_rented.' +3 month'))."' ";
				break;
			case 3:
				$data .= ", `date_end`='".date("Y-m-d",strtotime($date_rented.' +1 year'))."' ";
				break;
			default:
				# code...
				break;
		}
		if(empty($id)){
			$sql = "INSERT INTO `rent_list` set {$data} ";
		}else{
			$sql = "UPDATE `rent_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Rent successfully saved.");
			else
				$this->settings->set_flashdata('success',"Rent successfully updated.");
			$this->settings->conn->query("UPDATE `unit_list` set `status` = '{$status}' where id = '{$unit_id}'");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_rent(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `rent_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Rent successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function delete_img(){
		extract($_POST);
		if(is_file($path)){
			if(unlink($path)){
				$resp['status'] = 'success';
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = 'failed to delete '.$path;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = 'Unkown '.$path.' path';
		}
		return json_encode($resp);
	}
	function renew_rent(){
		extract($_POST);
		$qry = $this->conn->query("SELECT * FROM `rent_list` where id ='{$id}'");
		$res = $qry->fetch_array();
		switch ($res['rent_type']) {
			case 1:
				$date_end = " `date_end`='".date("Y-m-d",strtotime($res['date_end'].' +1 month'))."' ";
				break;
			case 2:
				$date_end = " `date_end`='".date("Y-m-d",strtotime($res['date_end'].' +3 month'))."' ";
				break;
			case 3:
				$date_end = " `date_end`='".date("Y-m-d",strtotime($res['date_end'].' +1 year'))."' ";
				break;
			default:
				# code...
				break;
		}
		$update = $this->conn->query("UPDATE `rent_list` set {$date_end}, date_rented = date_end where id = '{$id}' ");
		if($update){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Rent successfully renewed.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_unit':
		echo $Master->save_unit();
	break;
	case 'delete_unit':
		echo $Master->delete_unit();
	break;
	case 'save_pricing':
		echo $Master->save_pricing();
	break;
	case 'delete_pricing':
		echo $Master->delete_pricing();
	break;
	case 'save_tenant':
		echo $Master->save_tenant();
	break;
	case 'delete_tenant':
		echo $Master->delete_tenant();
	break;
	case 'get_price':
		echo $Master->get_price();
		break;
	case 'save_rent':
		echo $Master->save_rent();
	break;
	case 'delete_rent':
		echo $Master->delete_rent();
	break;
	case 'renew_rent':
		echo $Master->renew_rent();
	break;
	
	default:
		// echo $sysset->index();
		break;
}