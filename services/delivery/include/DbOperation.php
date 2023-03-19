<?php
date_default_timezone_set("Asia/Kolkata");
class DbOperation
{
    private $con;

    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();
    }


// register delivery boy
public function delivery_reg($name, $email, $password, $contact, $address,  $city, $pincode, $id_proof_type, $MainFileName, $zone_id, $status, $action,$ProofFileName)
{
    if (!$this->isemailIDExists($email))
    {

        if (!$this->isContactExists($contact))
        {
   
            $stmt = $this->con->prepare("INSERT INTO delivery_boy( `name`, `email`, `password`, `contact`, `address`, `city`, `pincode`, `id_proof_type`, `id_proof`, `zone_id`, `status`, `action`,`profile_pic`) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssisssisss",$name, $email, $password, $contact, $address,  $city, $pincode, $id_proof_type, $MainFileName, $zone_id, $status, $action,$ProofFileName);
            $result = $stmt->execute();   
            $lastId = mysqli_insert_id($this->con);             
            $stmt->close();
            if ($result) {
                return $lastId;
            } else {
                return 0;
            }
        }
        else
        {
            return -2;
        }
    }
    else
    {
        return -3;
    }
}

//delivery boy login

public function Delivery_boyLogin($email, $pass)
    {
       
        
        $stmt = $this->con->prepare("SELECT * FROM delivery_boy WHERE email=? and BINARY password =?");
        $stmt->bind_param("ss", $email, $pass);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
        
    }



//get delivery boy reg data
     public function getDelivery_boy($email)
    {
        $stmt = $this->con->prepare("SELECT * FROM delivery_boy WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $deliveryboy = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $deliveryboy;
    }

//delete delivery boy devices
    public function delete_delivery_boy_device($dbid)
    {

        $stmt = $this->con->prepare("DELETE FROM `delivery_boy_device` WHERE `db_id` = ?");
        $stmt->bind_param("i", $dbid);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return 0;
        } else {
            return 1;
        }
    }

// insert delivery boy device

  public function insert_delivery_boy_device($dbid,$token,$type)
    {
 
        $datetime=date('Y-m-D h:i A');
         
        $stmt = $this->con->prepare("INSERT INTO `delivery_boy_device`(`db_id`, `token`, `type`) VALUES (?,?,?)");
        $stmt->bind_param("iss", $dbid, $token, $type);
       
        $result = $stmt->execute();
        $stmt->close();
        
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }


// view profile
    public function view_profile($userid)
    {
        $stmt = $this->con->prepare("select db_id,name,email,contact,id_proof,profile_pic from delivery_boy where db_id=?");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }


    // get job list 
    public function get_job_list($deliveryboy_id)
    {

        $today=date("Y-m-d");
        
        
        $stmt = $this->con->prepare("SELECT j1.id as job_id,p1.id as post_id,p1.receiver_name,c1.name as sender_name,c1.contact as sender_phone,m1.mail_type,p1.acknowledgement,p1.collection_address,p1.priority,p1.dispatch_date,c3.start_time as collection_start_time,c3.end_time as collection_end_time,j1.job_status,c2.address_label,c2.house_no,c2.street,a1.area_name,c2.pincode,p1.delivery_charge,p1.basic_charges,p1.ack_charges,p1.total_charges,p1.discount,p1.total_payment,p1.post_status,p1.payment_status FROM job_assign j1,delivery_boy db1,post p1,customer_reg c1,customer_address c2,area a1,collection_time c3,mail_type m1 where j1.post_id=p1.id and j1.delivery_boy_id=db1.db_id and p1.sender_id=c1.id and p1.collection_address=c2.ca_id and c2.area_id=a1.aid and p1.collection_time=c3.id and p1.mail_type=m1.id and db1.db_id=? and job_status='pending' order by j1.id desc ");
        $stmt->bind_param("i", $deliveryboy_id);
        $stmt->execute();
        $joblist = $stmt->get_result();
        $stmt->close();
        return $joblist;

    }

     // get active job list
    public function get_active_job_list($deliveryboy_id)
    {
        $today=date("Y-m-d");
        $yesterday=date("Y-m-d",strtotime("-1 days"));

        $stmt = $this->con->prepare("SELECT j1.id as job_id,p1.id as post_id,p1.receiver_name,c1.name as sender_name,c1.contact as sender_phone,m1.mail_type,p1.acknowledgement,p1.collection_address,p1.priority,p1.dispatch_date,c3.start_time as collection_start_time,c3.end_time as collection_end_time,j1.job_status,c2.address_label,c2.house_no,c2.street,a1.area_name,c2.pincode,p1.delivery_charge,p1.basic_charges,p1.ack_charges,p1.total_charges,p1.discount,p1.total_payment,p1.post_status,p1.payment_status FROM job_assign j1,delivery_boy db1,post p1,customer_reg c1,customer_address c2,area a1,collection_time c3,mail_type m1 where j1.post_id=p1.id and j1.delivery_boy_id=db1.db_id and p1.sender_id=c1.id and p1.collection_address=c2.ca_id and c2.area_id=a1.aid and p1.collection_time=c3.id and p1.mail_type=m1.id  and db1.db_id=?  and (j1.job_status='accept' or j1.job_status='dispatch')  order by j1.id ");
        // and post status='pending' or 'collected'
        $stmt->bind_param("i", $deliveryboy_id);
        $stmt->execute();
        $joblist = $stmt->get_result();
        $stmt->close();
        return $joblist;


    }

    // get job history
    public function get_job_history($deliveryboy_id)
    {

        $today=date("Y-m-d");
        $stmt = $this->con->prepare("SELECT j1.id as job_id,p1.id as post_id,p1.receiver_name,c1.name as sender_name,c1.contact as sender_phone,m1.mail_type,p1.acknowledgement,p1.collection_address,p1.priority,p1.dispatch_date,c3.start_time as collection_start_time,c3.end_time as collection_end_time,j1.job_status,c2.address_label,c2.house_no,c2.street,a1.area_name,c2.pincode FROM job_assign j1,delivery_boy db1,post p1,customer_reg c1,customer_address c2,area a1,collection_time c3,mail_type m1 where j1.post_id=p1.id and j1.delivery_boy_id=db1.db_id and p1.sender_id=c1.id and p1.collection_address=c2.ca_id and c2.area_id=a1.aid and p1.collection_time=c3.id and p1.mail_type=m1.id and j1.delivery_boy_id=? and j1.job_status='deliver' order by j1.id desc");
        $stmt->bind_param("i", $deliveryboy_id);
        $stmt->execute();
        $joblist = $stmt->get_result();
        $stmt->close();
        return $joblist;


    }

    //get city list
    public function get_city_list()
    {
        $stmt = $this->con->prepare("select * from city where status='enable'");
        
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }

// check if email already exists

private function isemailIDExists($email)
{

    $stmt = $this->con->prepare("SELECT db_id from delivery_boy WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $num_rows = $stmt->num_rows;
    $stmt->close();
    return $num_rows > 0;
}
// check if contact already exists

private function isContactExists($contact)
{
    $stmt = $this->con->prepare("SELECT db_id from delivery_boy WHERE contact = ?");
    $stmt->bind_param("s", $contact);
    $stmt->execute();
    $stmt->store_result();
    $num_rows = $stmt->num_rows;
    $stmt->close();
    return $num_rows > 0;
}



//  order info
public function order_info($job_id)
{

    
    $stmt = $this->con->prepare("select j1.id as job_id,j1.post_id,p1.sender_id,d1.name as delivery_boy_name,p1.post_status from job_assign j1,post p1,delivery_boy d1 where j1.post_id=p1.id and j1.delivery_boy_id=d1.db_id and j1.id=?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $order_list = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $order_list;
   

}
//update order status
public function update_order_status($pid,$status,$payment_status)
{

    //  $date_time =  date("Y-m-D h:i A");
    
    if($payment_status!="")
    {

        $stmt = $this->con->prepare("UPDATE `post` SET post_status = ?,payment_status=? where id = ?");
        $stmt->bind_param("ssi", $status,$payment_status, $pid);
    }
    else
    {
        $stmt = $this->con->prepare("UPDATE `post` SET post_status = ? where id = ?");
        $stmt->bind_param("si", $status, $pid);
    }
   
    
    $stmt->execute();
    $affected=$stmt->affected_rows;
    $stmt->close();
    return $affected;
}
    //---------------------------------------//

    // insert delivery boy availability

  public function delivery_boy_availability($dbid,$status)
    {
 
        $datetime=date('Y-m-D h:i A');
        


        $stmt = $this->con->prepare("INSERT INTO `delivery_boy_avalibility`(`delivery_boy_id`, `status`) VALUES (?,?)");
        $stmt->bind_param("is", $dbid,$status);
       
        $result = $stmt->execute();
        $stmt->close();
        
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }

//check delivery boy status
     public function check_delivery_boy_status($id)
    {

        
        $stmt = $this->con->prepare("SELECT * FROM delivery_boy WHERE id =? and `status`='Enable'");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;

    }


    // check delivery boy location
    public function check_delivery_location($deliveryboy_id)
    {

        $stmt = $this->con->prepare("select * from delivery_boy_location where db_id=?");
        $stmt->bind_param("i", $deliveryboy_id);
        $stmt->execute();
        $location = $stmt->get_result();
        $stmt->close();
        return $location;


    }

    // add lcoation
     public function add_location($dbid,$lat,$long)
    {
 
        
         
        $stmt = $this->con->prepare("INSERT INTO `delivery_boy_location`(`db_id`, `lat`,`longi`) VALUES (?,?,?)");
        $stmt->bind_param("idd", $dbid, $lat,$long);
       
        $result = $stmt->execute();
        $stmt->close();
        
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }
     // add lcoation
     public function update_location($loc_id,$lat,$long)
    {
 
        
        
        $stmt = $this->con->prepare("UPDATE `delivery_boy_location` SET `lat`=?,`longi`=? where id=?");
        $stmt->bind_param("ddi",  $lat,$long,$loc_id);
       
        $result = $stmt->execute();
        $affected=$stmt->affected_rows;
        $stmt->close();
        
        return $affected;
    }

    // get_terms by jay
    public function get_terms()
    {
        $stmt = $this->con->prepare("select * from termsandcondition where `type`='delivery'");
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }

    //get_privacy by jay
    public function get_privacy()
    {
        $stmt = $this->con->prepare("select * from privacy_policy where `type`='delivery'");
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }



    //Method to get user details by username
    public function getUser($userid)
    {
        $stmt = $this->con->prepare("SELECT * FROM delivery_boy WHERE userid=?");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result;
    }
    
// get vendor for delivery boy
     public function get_myvendor($userid)
    {
        $stmt = $this->con->prepare("select db1.id,v1.id as v_id,v1.business_name from delivery_boy db1,vendor_reg v1 where   db1.owner_id=v1.id and db1.owned_by='vendor' and db1.id=?");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result;
    }


   

//accept /reject job

    public function job_action($job_id,$status)
    {

        if($status=="accept" || $status=="reject")
        {
            $stmt = $this->con->prepare("update job_assign set job_status=? where id=? and job_status='pending'");
        }
        else
        {
            $stmt = $this->con->prepare("update job_assign set job_status=? where id=?");
        }
       // echo "update job_assign set job_status=$status where id=$job_id";
        $stmt->bind_param("si",$status,$job_id);
        $stmt->execute();
         $affected=$stmt->affected_rows;
        /*$stmt->store_result();
        $num_rows = $stmt->num_rows;*/
        $stmt->close();
        return $affected > 0;

    }

// update multiple job status
    public function job_update($p_id,$status,$job_id)
    {

        
        $stmt = $this->con->prepare("update job_assign set job_status=? where post_id=? and id!=?");
        
        
        $stmt->bind_param("sii",$status,$p_id,$job_id);
        $stmt->execute();
         $affected=$stmt->affected_rows;
        /*$stmt->store_result();
        $num_rows = $stmt->num_rows;*/
        $stmt->close();
        return $affected > 0;

    }

    // order list
    public function order_list($job_id)
    {

        
        $stmt = $this->con->prepare("select j1.id as job_id,o1.id as order_id,o1.detail as order_detail,o2.detail as order_list from job_assign j1,ordr o1,order_detail o2 where j1.order_id=o1.id and o2.o_id=o1.id
 and j1.id=?");
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $order_list = $stmt->get_result();
        $stmt->close();
        return $order_list;
       

    }


// logout

     public function logout($id,$tokenid)
    {

         $stmt = $this->con->prepare("delete from delivery_boy_device where db_id=? and token=?");
        
        $stmt->bind_param("is", $id,$tokenid);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }

    public function fetch_user_android($o_id)
    {

        $stmt = $this->con->prepare("SELECT cd.* FROM `customer_devices` cd,post p1 where cd.cust_id=p1.sender_id and cd.device_type='android' and p1.id=? ");
        $stmt->bind_param("i", $o_id);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }
     public function fetch_user_ios($o_id)
    {

        $stmt = $this->con->prepare("SELECT cd.* FROM `customer_devices` cd,post p1 where cd.cust_id=p1.sender_id and  cd.device_type='ios' and p1.id=? ");
        $stmt->bind_param("i", $o_id);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }
// fetch vendor devices
    public function fetch_vendor_devices_android($o_id)
    {

        $stmt = $this->con->prepare("SELECT vd.* FROM `vendor_device` vd,ordr o1 where vd.vid=o1.v_id and vd.type='android' and o1.id=? ");
        $stmt->bind_param("i", $o_id);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }
    public function fetch_vendor_devices_ios($o_id)
    {

        $stmt = $this->con->prepare("SELECT vd.* FROM `vendor_device` vd,ordr o1 where vd.vid=o1.v_id and vd.type='ios' and o1.id=? ");
        $stmt->bind_param("i", $o_id);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }

    // fecth admin ios device
     public function fetch_admin_devices_ios()
    {

        $stmt = $this->con->prepare("SELECT * FROM `admin_device` where type='ios'  group by token");        
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }

    // fecth admin android device
     public function fetch_admin_devices_android()
    {

        $stmt = $this->con->prepare("SELECT * FROM `admin_device` where type='android'  group by token");        
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }

    public function delivery_boy_info($o_id)
    {

        $stmt = $this->con->prepare("select db.id,db.contact,db.name from job_assign j1,delivery_boy db where j1.delivery_boy_id=db.id and j1.order_id=? ");
        $stmt->bind_param("i", $o_id);
        $stmt->execute();
        $del = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $del;
    }

    // check phone number
    public function fetch_contact($phone_number)
    {


        $stmt = $this->con->prepare("SELECT contact FROM `delivery_boy` WHERE contact=?");
        $stmt->bind_param("s", $phone_number);
        $result = $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;

    }
    // get vendor id
    public function get_deliveryboy($contact)
    {
        $stmt = $this->con->prepare("SELECT * FROM delivery_boy WHERE REPLACE(contact,' ', '')=?");
        $stmt->bind_param("s", $contact);
        $result = $stmt->execute();
        $faculty = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($result) {
            return $faculty;
        } else {
            return 0;
        }
    }

//check delivery boy availability
     public function check_delivery_boy_availability($id)
    {

        
        $stmt = $this->con->prepare("SELECT * FROM delivery_boy_avalibility WHERE delivery_boy_id =? and `status`='on' order by id desc limit 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;

    }

// update delivery boy availability
 public function update_delivery_boy_availability($dbid,$reason,$status)
    {
        //$status='off';
         
       
        $stmt = $this->con->prepare("INSERT INTO `delivery_boy_avalibility`(`delivery_boy_id`, `status`,`reason`) VALUES (?,?,?)");
        $stmt->bind_param("iss", $dbid,$status,$reason);
       
        $result = $stmt->execute();
        $stmt->close();
        
        if ($result) {
            return 1;
        } else {
            return 0;
        }

    }

    //insert delivery image
    public function add_delivery_image($job_id,$MainFileName,$status)
    {
        $datetime=date("Y-m-D h:i A");
        
        $stmt = $this->con->prepare("INSERT INTO `delivery_images`(`job_id`, `image`,`status`,`datetime`) VALUES (?,?,?,?)");
        $stmt->bind_param("isss", $job_id,$MainFileName,$status,$datetime);
       
        $result = $stmt->execute();
        $stmt->close();
        
        if ($result) {
            return 1;
        } else {
            return 0;
        }

    }
// check delivery boy
     public function check_delivery_boy($id)
    {

        
        $stmt = $this->con->prepare("SELECT * FROM delivery_boy_avalibility WHERE delivery_boy_id =? order by id desc limit 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resp=$stmt->get_result();
        
        $stmt->close();
        return $resp;

    }

    //update password
    public function update_Password($cpass, $npass, $id)
    {

        $date_time = date("Y-m-D h:i A");
        $operation = 'Updated';
        $type = 'Normal';

        $stmt1 = $this->con->prepare("select password from delivery_boy where id=? and password=? ");
        $stmt1->bind_param("is", $id, $cpass);
        $result1 = $stmt1->execute();
        $stmt1->store_result();
        $password = $stmt1->num_rows;

        if ($password > 0) {
            $stmt = $this->con->prepare("update delivery_boy set password=? where id=? ");
            $stmt->bind_param("si", $npass, $id);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return 2;
        }

    }

    //insert into domail

public function insertinto_domail($cid,$vid,$oid,$order_status,$status)
    {
        $datetime=date("Y-m-D h:i A");
        $stmt = $this->con->prepare("insert into domail (`customerid`, `vendorid`, `orderid`, `type`, `status`, `datetime`) values(?,?,?,?,?,?)");
        $stmt->bind_param("iiisss",$cid,$vid,$oid,$order_status,$status,$datetime);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return 0;
        } else {
            return 1;
        }
    }


    //insert notification
    public function new_notification($noti_type,$pid,$msg,$status,$playstatus)
    {

       
        $stmt = $this->con->prepare("INSERT INTO `notification`(`noti_type`, `noti_type_id`,`msg`,`status`,`playstatus`) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sisii",$noti_type,$pid,$msg,$status,$playstatus);
        $result = $stmt->execute();
       
        $stmt->close();

        if ($result) {
            return 1;
        } else {
            return 0;
        }

    }
    // get order ready status
     public function get_order_ready_status($o_id)
    {

        
        $stmt = $this->con->prepare("SELECT * FROM order_ready WHERE order_id =?  order by id desc limit 1");
        $stmt->bind_param("s", $o_id);
        $stmt->execute();
        $result=$stmt->get_result()->fetch_assoc();
        
        $stmt->close();
        return $result;

    }

    // get account data
    public function get_account($db_id,$from_date,$to_date)
    {


        $stmt = $this->con->prepare("SELECT count(*) as total_job,sum(o1.delivery_boy_amount) as delivery_boy_amount,sum(o1.myct_delivery_amount) as myct_delivery_amount,sum(o1.ex_charge) as total_delivery_charge FROM `job_assign` j1,ordr o1,delivery_boy d1 WHERE j1.order_id=o1.id and j1.delivery_boy_id=d1.id and j1.job_status='deliver'  and (o1.stats='Confirmed' or o1.stats='Dispatched' or o1.stats='Delivered') and j1.delivery_boy_id=?  and ( str_to_date(o1.collect_date,'%Y-%m-%d') >=str_to_date('".$from_date."','%Y-%m-%d') and   str_to_date(o1.collect_date,'%Y-%m-%d') <= str_to_date('".$to_date."','%Y-%m-%d')) order by j1.id");
        $stmt->bind_param("i", $db_id);
        $stmt->execute();
        $account_data = $stmt->get_result();
        $stmt->close();
        return $account_data;


    }

  
}
    
