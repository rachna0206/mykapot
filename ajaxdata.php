<?php
session_start();
date_default_timezone_set("Asia/Kolkata");
//error_reporting(E_ALL);
include("db_connect.php");
$obj=new DB_Connect();
if(isset($_REQUEST['action']))
{
	// get notification
	if($_REQUEST['action']=="get_notification")
	{
		$html="";
		
		$stmt_clist = $obj->con1->prepare("select * from notification where status=1 order by id desc");
	 	$stmt_clist->execute();
	  	$notification = $stmt_clist->get_result();
	  	$count=mysqli_num_rows($notification);
	  	$stmt_clist->close();
		$html='';
	  	while($noti=mysqli_fetch_array($notification))  		
	  	{
	  		if($noti["noti_type"]=="customer_reg")
	  		{
	  			$stmt_cust = $obj->con1->prepare("select * from customer_reg where id='".$noti["noti_type_id"]."'");

	  		}
	  		else if($noti["noti_type"]=="delivery_reg")
	  		{
	  			$stmt_cust = $obj->con1->prepare("select * from delivery_boy where db_id='".$noti["noti_type_id"]."'");
	  		}
	  		else 
	  		{
	  			$stmt_cust = $obj->con1->prepare("select c.* from post p,customer_reg c where p.sender_id=c.id and p.id='".$noti["noti_type_id"]."'");
	  		}
	  		
		 	$stmt_cust->execute();
		 	$noti_resp=$stmt_cust->get_result();
		  	$notification_cust = $noti_resp->fetch_assoc();
		  	
		  	$stmt_cust->close();
		  	if($noti["noti_type"]=="customer_reg")
	  		{
	  			$html.= '<li class="list-group-item list-group-item-action dropdown-notifications-item"><div class="d-flex flex-column"><a class="dropdown-item" href="javascript:removeNotification('.$noti["id"].',\''.$noti["noti_type"].'\')"><span class="align-middle">New user registered <br/><small class="text-primary fw-semibold">'.$notification_cust["name"].'- '.$notification_cust["contact"].'</small></span></a></div>	</li>';
	  		}
	  		else if($noti["noti_type"]=="delivery_reg")
	  		{
	  			$html.= '<li class="list-group-item list-group-item-action dropdown-notifications-item"><div class="d-flex flex-column"><a class="dropdown-item" href="javascript:removeNotification('.$noti["id"].',\''.$noti["noti_type"].'\')"><span class="align-middle">New delivery boy registered <br/><small class="text-primary fw-semibold">'.$notification_cust["name"].'- '.$notification_cust["contact"].'</small></span></a></div>	</li>';
	  		}
	  		else if($noti["noti_type"]=="post_accepted")
            {
                $html.= '<li class="list-group-item list-group-item-action dropdown-notifications-item"><div class="d-flex flex-column"><a class="dropdown-item" href="javascript:removeNotification('.$noti["id"].',\''.$noti["noti_type"].'\')"><span class="align-middle">A Post Job has been Accepted <br/><small class="text-primary fw-semibold">'.$notification_cust["name"].'- '.$notification_cust["contact"].'  / Post Id - '.$noti["noti_type_id"].'</small></span></a></div> </li>';
            }
            else if($noti["noti_type"]=="post_dispatched")
            {
                $html.= '<li class="list-group-item list-group-item-action dropdown-notifications-item"><div class="d-flex flex-column"><a class="dropdown-item" href="javascript:removeNotification('.$noti["id"].',\''.$noti["noti_type"].'\')"><span class="align-middle">A Post Job has been Sucessfully Dispatched <br/><small class="text-primary fw-semibold">'.$notification_cust["name"].'- '.$notification_cust["contact"].'  / Post Id - '.$noti["noti_type_id"].'</small></span></a></div> </li>';
            }
            else if($noti["noti_type"]=="post_rejected")
            {
                $html.= '<li class="list-group-item list-group-item-action dropdown-notifications-item"><div class="d-flex flex-column"><a class="dropdown-item" href="javascript:removeNotification('.$noti["id"].',\''.$noti["noti_type"].'\')"><span class="align-middle">A Delivery Boy has rejected a Post Job <br/><small class="text-primary fw-semibold">'.$notification_cust["name"].'- '.$notification_cust["contact"].'  / Post Id - '.$noti["noti_type_id"].'</small></span></a></div> </li>';
            }
            else if($noti["noti_type"]=="post_in_transit")
            {
                $html.= '<li class="list-group-item list-group-item-action dropdown-notifications-item"><div class="d-flex flex-column"><a class="dropdown-item" href="javascript:removeNotification('.$noti["id"].',\''.$noti["noti_type"].'\')"><span class="align-middle">Your Post is in Transit <br/><small class="text-primary fw-semibold">'.$notification_cust["name"].'- '.$notification_cust["contact"].'  / Post Id - '.$noti["noti_type_id"].'</small></span></a></div> </li>';
            }
	  		else
	  		{
	  			$html.= '<li class="list-group-item list-group-item-action dropdown-notifications-item"><div class="d-flex flex-column"><a class="dropdown-item" href="javascript:removeNotification('.$noti["id"].',\''.$noti["noti_type"].'\')"><span class="align-middle">You have got new Post Job<br/><small class="text-primary fw-semibold">'.$notification_cust["name"].' - '.$notification_cust["contact"].'  / Post Id - '.$noti["noti_type_id"].'</small></span></a></div>	</li>';
	  		}

			
	  	}
	  	echo $html."@@@@".$count."@@@@";
	}
	// remove notification
	if($_REQUEST['action']=="removenotification")
	{
		$html="";
		$id=$_REQUEST["id"];
		$stmt_list = $obj->con1->prepare("update notification set `status`=0,`playstatus`=0 where id=?");
		$stmt_list->bind_param("i",$id);
	 	$stmt_list->execute();
  	
  	$stmt_list->close();
	}


	// get play notification
	if($_REQUEST['action']=="get_Playnotification")
	{
		$html="";
		$ids="";
		$stmt_clist = $obj->con1->prepare("select * from notification where playstatus=1");
	 	$stmt_clist->execute();
	  	$lead_notification = $stmt_clist->get_result();
	  	$count=mysqli_num_rows($lead_notification);

	  	$stmt_clist->close();
			$html='';
		  	while($noti=mysqli_fetch_array($lead_notification))
		  	{
					$ids.=$noti["id"].",";
		  	}
	  	echo $count."@@@@".rtrim($ids,",");
	}
	// remove play sound
	if ($_REQUEST["action"] == "removeplaysound") {

    $ids=explode(',',$_REQUEST["id"]);
  
   
    for($i=0;$i<sizeof($ids);$i++)
    {
      

      $stmt_clist = $obj->con1->prepare("UPDATE `notification` SET `playstatus`=0 WHERE id=?");
      $stmt_clist->bind_param("i",$ids[$i]);
	$stmt_clist->execute();
  	$stmt_clist->close();
    }
    
}

// read all
	if($_REQUEST['action']=="mark_read_all")
	{
		$html="";
		
		$stmt_list = $obj->con1->prepare("update notification set `status`=0,`playstatus`=0 ");
		$stmt_list->bind_param("i",$id);
	 	$stmt_list->execute();
  	
  	$stmt_list->close();
	}
	if($_REQUEST['action']=="areaList")
	{	
		$html="";
		$city_id=$_REQUEST['city_id'];
		
		$stmt_area = $obj->con1->prepare("select * from area where city=? and status='enable'");
		$stmt_area->bind_param("i",$city_id);
		$stmt_area->execute();
		$res = $stmt_area->get_result();
		$stmt_area->close();

		$html='<option value="">Select Area</option>';
	              while($area=mysqli_fetch_array($res))
	              {
	              	$html.='<option value="'.$area["aid"].'">'.$area["area_name"].'</option>';
	              }
  	echo $html;
	}
	if($_REQUEST["action"]=="get_address")
	{
		$html="";
		$uid=$_REQUEST["uid"];
		$stmt_address= $obj->con1->prepare("select ca.*,c1.city_name,a1.area_name from customer_address ca, area a1,city c1 where ca.area_id=a1.aid and ca.city_id=c1.city_id and ca.cust_id=?");
		$stmt_address->bind_param("i",$uid);
		$stmt_address->execute();
		$res_address  = $stmt_address->get_result();
		$stmt_address->close();


		//$stmt_coupon= $obj->con1->prepare("select * from coupon c1, coupon_counter c2 where c1.c_id=c2.coupon_id and c1.status='enable' and customer_id=?");
		$stmt_coupon= $obj->con1->prepare("(select c_id,name,couponcode,discount from coupon c1, coupon_counter c2 where c1.c_id=c2.coupon_id and c2.counter>0 and c1.status='enable' and c1.type='specific' and c2.customer_id=? and curdate()>=start_date and curdate()<=end_date) union (select c_id,name,couponcode,discount from coupon c1 where c1.status='enable' and c1.type='generic' and curdate()>=start_date and curdate()<=end_date)");
		$stmt_coupon->bind_param("i",$uid);
		$stmt_coupon->execute();
		$res_coupon  = $stmt_coupon->get_result();
		$stmt_coupon->close();
		$coupon_count=mysqli_num_rows($res_coupon);
		echo $coupon_count;
		$html='<option value="">Select Collection Address</option>';
	      while($address=mysqli_fetch_array($res_address))
	      {
	      	$html.='<option value="'.$address["ca_id"].'">'.$address["address_label"].'-'.$address["house_no"].','.$address["street"].','.$address["area_name"].','.$address["city_name"].'</option>';
	      }


	    $html.="@@@@@";

	    $coupon_result = "";
	    $f=0;

	    $coupon_result.='<option value="">Select Coupon</option>';
	      while($coupon=mysqli_fetch_array($res_coupon))
	      {
	      	if($coupon_count>0){
	      		//if($coupon["counter"]>0){
	      			$coupon_result.='<option value="'.$coupon["c_id"].'">'.$coupon["couponcode"].'</option>';
	      			$f=1;
	      		
	      	}
	      }

	    if($f==1){
	    	$html.=$coupon_result;
	    }
	    else{
	    	$html.=1;
	    }
		echo $html;              
	}
	if($_REQUEST["action"]=="get_amount")
	{
		$html="";
		$basic_charges="";	
		$weight=$_REQUEST["weight"];
		$mail_type=$_REQUEST["mail_type"];
		
		$stmt_amt= $obj->con1->prepare("SELECT *,count(*) as count FROM mail_type m1,mail_type_tariff m2 WHERE m2.mail_type=m1.id and m1.id=? and gm_from<=? and gm_to>=?");
		$stmt_amt->bind_param("idd",$mail_type,$weight,$weight);
		$stmt_amt->execute();
		$res_amt = $stmt_amt->get_result();
		$stmt_amt->close();
		while($mail_amount=mysqli_fetch_array($res_amt))
		{
			if($mail_amount["count"]>0)
			{
				$basic_charges=$mail_amount["amount"];
			}
			else
			{
				$basic_charges=0;
			}
		}

		echo $basic_charges;

	}
	if($_REQUEST["action"]=="get_coupon_disc")
	{
		$html="";
		$coupon_id=$_REQUEST["coupon_id"];
		$total_amt=$_REQUEST["total_charge"];
		//$total_del_charge=$_REQUEST["total_del_charge"];
		$del_charge=$_REQUEST["del_charge"];

		$stmt_coupon = $obj->con1->prepare("select * from coupon where c_id=?");
		$stmt_coupon->bind_param("i",$coupon_id);
		$stmt_coupon->execute();
		$res_coupon = $stmt_coupon->get_result();
		$stmt_coupon->close();
		$row_num = mysqli_num_rows($res_coupon);

		 
		if ($row_num > 0) {
        	$date = date('Y-m-d');
            $p_row = mysqli_fetch_array($res_coupon);
            $start_date = $p_row['start_date'];
            $end_date = $p_row['end_date'];
            $discount = $p_row['discount'];
            $max_discount_amount = $p_row['max_discount'];
            $min_amount = $p_row['min_amount'];
            $percentage = $p_row["discount"];
            
            $paymentDate = date('Y-m-d', strtotime($date));
            //echo $paymentDate; // echos today! 
            $contractDateBegin = date('Y-m-d', strtotime($start_date));
            $contractDateEnd = date('Y-m-d', strtotime($end_date));

           
            if ((strtotime($date) >= strtotime($start_date)) && (strtotime($date) <= strtotime($end_date))) {

                if ($total_amt >= $min_amount) {

                    // $amount_discount1 = $total_amt * $discount;
                    // $amount_discount = $amount_discount1 / 100;
                    //$amount_discount1 = $total_del_charge * $discount;
                    $amount_discount1 = $del_charge * $discount;
                    $amount_discount = $amount_discount1 / 100;

                    if ($amount_discount < $max_discount_amount) {
                        $final_discount = $amount_discount;
                        $final_amount = $total_amt - $final_discount;
                       
                        echo $final_amount . "@@@@@" . $final_discount;
                    } else {

                        $final_discount = $max_discount_amount;
                        $final_amount = $total_amt - $final_discount;
                        
                        echo $final_amount . "@@@@@" . $final_discount;
                    }
                } else {
                    echo 2;
                }
            } else {
              	echo 1;
            }
        } else {   
            echo 1;
        }	

	}

	if($_REQUEST['action']=="check_deliboy_contact")
	{
		$html="";
		$contact_no=$_REQUEST["contact_no"];
		$id=$_REQUEST['id'];
		if($id!=""){
			$stmt_contact = $obj->con1->prepare("select * from delivery_boy where contact=? and db_id!=?");
			$stmt_contact->bind_param("si",$contact_no,$id);
		}
		else{	
			$stmt_contact = $obj->con1->prepare("select * from delivery_boy where contact=?");
			$stmt_contact->bind_param("s",$contact_no);
		}
		$stmt_contact->execute();
		$res = $stmt_contact->get_result();
		$stmt_contact->close();
		if(mysqli_num_rows($res)>0){
			$html=1;
		}
		else{
			$html=0;
		}

		echo $html;
	}

	if($_REQUEST['action']=="check_deliboy_email")
	{
		$html="";
		$email_id=$_REQUEST["email_id"];
		$id=$_REQUEST['id'];
		if($id!=""){
			$stmt_email = $obj->con1->prepare("select * from delivery_boy where email=? and db_id!=?");
			$stmt_email->bind_param("si",$email_id,$id);
		}
		else{	
			$stmt_email = $obj->con1->prepare("select * from delivery_boy where email=?");
			$stmt_email->bind_param("s",$email_id);
		}
		$stmt_email->execute();
		$res = $stmt_email->get_result();
		$stmt_email->close();
		if(mysqli_num_rows($res)>0){
			$html=1;
		}
		else{
			$html=0;
		}

		echo $html;
	}

	if($_REQUEST['action']=="check_cust_contact")
	{
		$html="";
		$contact_no=$_REQUEST["contact_no"];
		$id=$_REQUEST['id'];
		if($id!=""){
			$stmt_contact = $obj->con1->prepare("select * from customer_reg where contact=? and id!=?");
			$stmt_contact->bind_param("si",$contact_no,$id);
		}
		else{	
			$stmt_contact = $obj->con1->prepare("select * from customer_reg where contact=?");
			$stmt_contact->bind_param("s",$contact_no);
		}
		$stmt_contact->execute();
		$res = $stmt_contact->get_result();
		$stmt_contact->close();
		if(mysqli_num_rows($res)>0){
			$html=1;
		}
		else{
			$html=0;
		}

		echo $html;
	}

	if($_REQUEST['action']=="check_cust_email")
	{
		$html="";
		$email_id=$_REQUEST["email_id"];
		$id=$_REQUEST['id'];
		if($id!=""){
			$stmt_email = $obj->con1->prepare("select * from customer_reg where email=? and id!=?");
			$stmt_email->bind_param("si",$email_id,$id);
		}
		else{	
			$stmt_email = $obj->con1->prepare("select * from customer_reg where email=?");
			$stmt_email->bind_param("s",$email_id);
		}
		$stmt_email->execute();
		$res = $stmt_email->get_result();
		$stmt_email->close();
		if(mysqli_num_rows($res)>0){
			$html=1;
		}
		else{
			$html=0;
		}

		echo $html;
	}

	if($_REQUEST['action']=="check_cust_coupon")
	{
		$html="";
		$customer_id=$_REQUEST["customer_id"];
		$coupon_id=$_REQUEST['coupon_id'];
		$stmt = $obj->con1->prepare("select * from coupon_counter where customer_id=? and coupon_id=?");
		$stmt->bind_param("ii",$customer_id,$coupon_id);
		$stmt->execute();
		$res = $stmt->get_result();
		$stmt->close();
		if(mysqli_num_rows($res)>0){
			$html=1;
		}
		else{
			$html=0;
		}

		echo $html;
	}

	if($_REQUEST['action']=="check_coupon_name")
	{
		$html="";
		$coupon_name=$_REQUEST["name"];
		$id=$_REQUEST['id'];
		if($id!=""){
			$stmt_name = $obj->con1->prepare("select * from coupon where name=? and c_id!=?");
			$stmt_name->bind_param("si",$coupon_name,$id);
		}
		else{	
			$stmt_name = $obj->con1->prepare("select * from coupon where name=?");
			$stmt_name->bind_param("s",$coupon_name);
		}
		$stmt_name->execute();
		$res = $stmt_name->get_result();
		$stmt_name->close();
		if(mysqli_num_rows($res)>0){
			$html=1;
		}
		else{
			$html=0;
		}

		echo $html;
	}

	if($_REQUEST['action']=="check_coupon_code")
	{
		$html="";
		$coupon_code=$_REQUEST["code"];
		$id=$_REQUEST['id'];
		if($id!=""){
			$stmt_code = $obj->con1->prepare("select * from coupon where couponcode=? and c_id!=?");
			$stmt_code->bind_param("si",$coupon_code,$id);
		}
		else{	
			$stmt_code = $obj->con1->prepare("select * from coupon where couponcode=?");
			$stmt_code->bind_param("s",$coupon_code);
		}
		$stmt_code->execute();
		$res = $stmt_code->get_result();
		$stmt_code->close();
		if(mysqli_num_rows($res)>0){
			$html=1;
		}
		else{
			$html=0;
		}

		echo $html;
	}

	if($_REQUEST['action']=="getPincode")
	{
		$html="";
		$area_id = $_REQUEST['area_id'];
		$stmt = $obj->con1->prepare("select pincode from area where aid=?");
		$stmt->bind_param("i",$area_id);
		$stmt->execute();
		$res = $stmt->get_result();
		$stmt->close();

		$html = mysqli_fetch_array($res);

		echo $html[0];
	}

	if($_REQUEST['action']=="post_update_modal")
	{
		$post_id=$_REQUEST["post_id"];
		
		$html="";

		// get data from post
		$stmt_post = $obj->con1->prepare("select p1.*, c1.name as cust_name from post p1, customer_reg c1 where p1.sender_id=c1.id and p1.id=?");
		$stmt_post->bind_param("i",$post_id);
		$stmt_post->execute();
		$post_data = $stmt_post->get_result()->fetch_assoc();
		$stmt_post->close();

	    // get dboy from zone
	    $stmt_dboy = $obj->con1->prepare("select * from job_assign j1, delivery_boy d1 where d1.db_id=j1.delivery_boy_id and j1.post_id=? and status='enable'");
	    $stmt_dboy->bind_param("i",$post_id);
	    $stmt_dboy->execute();
	    $Resp_dboy=$stmt_dboy->get_result();
	    $stmt_dboy->close();

	    // get dboy from job_assign whose job_status=accept 
	    $stmt_dboy1 = $obj->con1->prepare("select delivery_boy_id from job_assign where post_id=? and (job_status='accept' or job_status='dispatched' or job_status='transit')");
	    $stmt_dboy1->bind_param("i",$post_id);
	    $stmt_dboy1->execute();
	    $Resp_dboy1=$stmt_dboy1->get_result()->fetch_assoc();
	    $stmt_dboy1->close();
	    
	
		$html='<form  method="post"><div class="modal-body" ><div class="row">
		<input type="hidden" name="post_id" value="'.$post_data["id"].'">
		  <div class="col mb-0">
            <label for="nameWithTitle" class="form-label">Order ID: '.$post_data['id'].'</label>
          </div>
          <div class="col mb-0">
            <label for="nameWithTitle" class="form-label">Customer Name: '.$post_data['cust_name'].'</label>
          </div>
        </div>

        <div class="row g-2">
          <div class="col mb-0">
            <label for="dobWithTitle" class="form-label">Order Date: '.$post_data['order_date'].'</label>
          </div>
          <div class="col mb-0">
            <label for="dobWithTitle" class="form-label">Order Status: '.$post_data['post_status'].'</label>
          </div>
        </div>
        
        <label for="dobWithTitle" class="form-label">Order Status</label>
        <hr class="m-0">

        <div class="row g-2">
        	<div class="col mb-3">
        	<div class="form-check form-check-inline mt-3">
                <input class="form-check-input" type="radio" name="order_status" id="pending" value="pending" '.($post_data["post_status"]=="pending"?"checked":"").' disabled>
                <label class="form-check-label" for="inlineRadio1">Pending</label>
              </div>
              <div class="form-check form-check-inline mt-3">
                <input class="form-check-input" type="radio" name="order_status" id="accept" value="accept" '.($post_data["post_status"]=="accept"?"checked":"").'>
                <label class="form-check-label" for="inlineRadio1">Accept</label>
              </div>
              <div class="form-check form-check-inline mt-3">
                <input class="form-check-input" type="radio" name="order_status" id="transit" value="transit" '.($post_data["post_status"]=="transit"?"checked":"").' disabled>
                <label class="form-check-label" for="inlineRadio1">In Transit</label>
              </div>
              <div class="form-check form-check-inline mt-3">
                <input class="form-check-input" type="radio" name="order_status" id="dispatched" value="dispatched" '.($post_data["post_status"]=="dispatched"?"checked":"").' disabled>
                <label class="form-check-label" for="inlineRadio1">Dispatched</label>
              </div>
              <div class="form-check form-check-inline mt-3">
                <input class="form-check-input" type="radio" name="order_status" id="rejected_admin" value="rejected_admin" '.($post_data["post_status"]=="rejected_admin"?"checked":"").'>
                <label class="form-check-label" for="inlineRadio1">Rejected (By Admin)</label>
              </div>
              <div class="form-check form-check-inline mt-3">
                <input class="form-check-input" type="radio" name="order_status" id="canceled_user" value="canceled_user" '.($post_data["post_status"]=="canceled_user"?"checked":"").' disabled>
                <label class="form-check-label" for="inlineRadio1">Canceled (By User)</label>
              </div>
          </div>
        </div>

        <label for="dobWithTitle" class="form-label">Payment Status</label>
        <hr class="m-0">

        <div class="row g-2">
        	<div class="col mb-3">
        	<div class="form-check form-check-inline mt-3">
                <input class="form-check-input" type="radio" name="payment_status" id="paid" value="paid" '.($post_data["payment_status"]=="paid"?"checked":"").' disabled>
                <label class="form-check-label" for="inlineRadio1">Paid</label>
              </div>
              <div class="form-check form-check-inline mt-3">
                <input class="form-check-input" type="radio" name="payment_status" id="unpaid" value="unpaid" '.($post_data["payment_status"]=="unpaid"?"checked":"").' disabled>
                <label class="form-check-label" for="inlineRadio1">Unpaid</label>
              </div>
              <div class="form-check form-check-inline mt-3">
                <input class="form-check-input" type="radio" name="payment_status" id="refund" value="refund" '.($post_data["payment_status"]=="refund"?"checked":"").' disabled>
                <label class="form-check-label" for="inlineRadio1">Refund</label>
              </div>
          </div>
        </div>

        <label for="dobWithTitle" class="form-label">Delivery Boy</label>
        <hr class="m-0">

        <div class="row g-2">
	        <div class="col mb-0">
	            <select name="delivery_boy" id="delivery_boy" class="form-control">
	            	<option value="">Select</option>';   
	            	while($dboy=mysqli_fetch_array($Resp_dboy))
	              	{
	              		$html.='<option value="'.$dboy["db_id"].'" '.($dboy["db_id"]==$Resp_dboy1["delivery_boy_id"]?"selected":"").'>'.$dboy["name"].'</option>';
	              	}
	    $html.='</select>
	          </div>
	    </div>';

        $html.='<div class="modal-footer">
	        <button type="submit" class="btn btn-primary" name="btn_modal_update">Save Changes</button>
	        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
	          Close
	        </button>
	        </form>
	      </div>';

      echo $html;
	}

	if($_REQUEST['action']=="getCollectionTime")
	{
		$html="";
		$collection_date = $_REQUEST['collection_date'];
		$collection_day=date('l',strtotime($collection_date));
		$month = date('F',strtotime($collection_date));
		$year = date('Y',strtotime($collection_date));
		$todays_date = date('Y-m-d');
		$time = date('H:i');

		if($collection_date==$todays_date){
		  $stmt_time= $obj->con1->prepare("select * from collection_time where status='enable'and start_time>=?");
		  $stmt_time->bind_param("s",$time);
		  $stmt_time->execute();
		  $res_coll_time = $stmt_time->get_result();
		  $stmt_time->close();
		} else if($collection_date>$todays_date){
		  $stmt_time= $obj->con1->prepare("select * from collection_time where status='enable'");
		  $stmt_time->execute();
		  $res_coll_time = $stmt_time->get_result();
		  $stmt_time->close();
		}

	    $html='<option value="">Select Collection Time</option>';
    	while($coll_time=mysqli_fetch_array($res_coll_time))
      	{
      		$html.='<option value="'.$coll_time["id"].'">'.$coll_time["start_time"].' - '.$coll_time["end_time"].'</option>';
      	}

		if($collection_day=="Monday" || $collection_day=="Tuesday" || $collection_day=="Wednesday" || $collection_day=="Thursday" || $collection_day=="Sunday"){
			$d_date = new DateTime($collection_date);
			$d_date->modify('+1 day');
			$delivery_date = $d_date->format('Y-m-d');
		}	
		else if($collection_day=="Friday"){
			$dod_date = new DateTime($collection_date);
			$dod_date->modify('+1 day');
			$d_date = $dod_date->format('Y-m-d');
			if(date('Y-m-d', strtotime("Second Saturday Of ".$month." {$year}"))==$d_date || date('Y-m-d', strtotime("Fourth Saturday Of ".$month." {$year}"))==$d_date)
			{
				$d_date = new DateTime($collection_date);
				$d_date->modify('+3 day');
				$delivery_date = $d_date->format('Y-m-d');				
			}
			else{
				$delivery_date = $d_date;		
			}
		} 
		else if($collection_day=="Saturday"){
			$d_date = new DateTime($collection_date);
			$d_date->modify('+2 day');
			$delivery_date = $d_date->format('Y-m-d');
		}

	  	echo $html."@@@@@".$delivery_date;
	}

}


?>
