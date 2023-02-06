<?php
session_start();
date_default_timezone_set("Asia/Kolkata");
error_reporting(E_ALL);
include("db_connect.php");
$obj=new DB_Connect();
if(isset($_REQUEST['action']))
{
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
