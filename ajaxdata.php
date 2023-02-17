<?php
session_start();
date_default_timezone_set("Asia/Kolkata");
error_reporting(E_ALL);
include("db_connect.php");
$obj=new DB_Connect();
if(isset($_REQUEST['action']))
{
	// get notification
	if($_REQUEST['action']=="get_notification")
	{
		$html="";
		
		$stmt_clist = $obj->con1->prepare("select * from notification where status=1");
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
	  			$html.= '<li class="list-group-item list-group-item-action dropdown-notifications-item"><div class="d-flex flex-column"><a class="dropdown-item" href="javascript:removeNotification('.$noti["id"].',\''.$noti["noti_type"].'\')"><span class="align-middle">New user registered <br/><small class="text-success fw-semibold">'.$notification_cust["name"].'- '.$notification_cust["contact"].'</small></span></a></div>	</li>';
	  		}
	  		else
	  		{
	  			$html.= '<li class="list-group-item list-group-item-action dropdown-notifications-item"><div class="d-flex flex-column"><a class="dropdown-item" href="javascript:removeNotification('.$noti["id"].',\''.$noti["noti_type"].'\')"><span class="align-middle">You have got new Post Job<br/><small class="text-success fw-semibold">'.$notification_cust["name"].' - '.$notification_cust["contact"].'</small></span></a></div>	</li>';
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
		$html='<option value="">Select Collection Address</option>';
	      while($address=mysqli_fetch_array($res_address))
	      {
	      	$html.='<option value="'.$address["ca_id"].'">'.$address["address_label"].'-'.$address["house_no"].','.$address["street"].','.$address["area_name"].','.$address["city_name"].'</option>';
	      }
		echo $html;	              
	}
	if($_REQUEST["action"]=="get_amount")
	{
		$html="";
		$basic_charges="";	
		$weight=$_REQUEST["weight"];
		$mail_type=$_REQUEST["mail_type"];
		
		$stmt_amt= $obj->con1->prepare("SELECT * FROM mail_type m1,mail_type_tariff m2 WHERE m2.mail_type=m1.id and m1.id=?");
		$stmt_amt->bind_param("i",$mail_type);
		$stmt_amt->execute();
		$res_amt = $stmt_amt->get_result();
		$stmt_amt->close();
		while($mail_amount=mysqli_fetch_array($res_amt))
		{
			
			if($weight>=$mail_amount["gm_from"] && $weight<=$mail_amount["gm_to"])
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
		$total_amt=$_REQUEST["total_amt"];


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

                    $amount_discount1 = $total_amt * $discount;
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
}


?>
