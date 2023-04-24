<?php
  include("header.php");

  $post_id = $_COOKIE['post_id'];

//post qry
$stmt_post = $obj->con1->prepare("select p1.*,a1.area_name,c1.id as cust_name,c1.name as cust_name,c3.id as coll_time_id,c3.start_time,c3.end_time, m1.mail_type as mail_type_name from post p1,area a1,mail_type m1,customer_address ca,customer_reg c1,collection_time c3 where ca.area_id=a1.aid and p1.mail_type=m1.id and p1.collection_address=ca.ca_id and p1.sender_id=c1.id and p1.collection_time=c3.id and p1.id=?");
$stmt_post->bind_param("i",$post_id);
$stmt_post->execute();
$result_post = $stmt_post->get_result();
$stmt_post->close();
$post_data=mysqli_fetch_array($result_post);


// Customer(Sender) Address
$stmt_address= $obj->con1->prepare("select ca.*,c1.city_name,a1.area_name from customer_address ca, area a1,city c1 where ca.area_id=a1.aid and ca.city_id=c1.city_id and ca_id=?");
$stmt_address->bind_param("i",$post_data['collection_address']);
$stmt_address->execute();
$res_address  = $stmt_address->get_result();
$stmt_address->close();
$sender_addr = mysqli_fetch_array($res_address);


// Coupon Code 
$stmt_coupon= $obj->con1->prepare("select couponcode from coupon where c_id=?");
$stmt_coupon->bind_param("i",$post_data['coupon_id']);
$stmt_coupon->execute();
$res_coupon  = $stmt_coupon->get_result();
$stmt_coupon->close();
$coupon = mysqli_fetch_array($res_coupon);


// Delivery Boy
$stmt_deliboy = $obj->con1->prepare("select *,count(*) as count from job_assign where (job_status='accept' or job_status='dispatched' or job_status='transit') and post_id=?");
$stmt_deliboy->bind_param("i",$post_id);
$stmt_deliboy->execute();
$result_deliboy = $stmt_deliboy->get_result();
$stmt_deliboy->close();
$deliboy_data=mysqli_fetch_array($result_deliboy);

// Delivery Boy Info
$stmt_deliboy_info = $obj->con1->prepare("select * from delivery_boy where db_id=?");
$stmt_deliboy_info->bind_param("i",$deliboy_data['delivery_boy_id']);

//echo "douby id=".$deliboy_data['delivery_boy_id'];


$stmt_deliboy_info->execute();
$result_deliboy_info = $stmt_deliboy_info->get_result();
$stmt_deliboy_info->close();
$deliboy_data_info=mysqli_fetch_array($result_deliboy_info);

// Delivery Images changed by jay : 16-04-2023 added or condition in the query
$stmt_deli_imgs1 = $obj->con1->prepare("select d1.image as transit_image, d2.image as barcode from delivery_images as d1, delivery_images as d2 where d1.job_id=? and d2.job_id=? and d1.status='transit' and d2.status='barcode'");
$stmt_deli_imgs1->bind_param("ii",$deliboy_data['id'],$deliboy_data['id']);
$stmt_deli_imgs1->execute();
$result_deli_imgs1 = $stmt_deli_imgs1->get_result();
$stmt_deli_imgs1->close();
$deli_imgs_data1=mysqli_fetch_array($result_deli_imgs1);

$stmt_deli_imgs2 = $obj->con1->prepare("select d1.image as dispatch_image from delivery_images as d1 where d1.job_id=? and d1.status='dispatched'");
$stmt_deli_imgs2->bind_param("i",$deliboy_data['id']);
$stmt_deli_imgs2->execute();
$result_deli_imgs2 = $stmt_deli_imgs2->get_result();
$stmt_deli_imgs2->close();
$deli_imgs_data2=mysqli_fetch_array($result_deli_imgs2);



  // insert data
  if(isset($_REQUEST['btnsubmit']))
  { 
    $post_status_transit="transit";
    $post_status_dispatch="dispatched";
    
    $img_status_dispatch="dispatched";
    $img_status_transit="transit";
    $barcode_status="barcode";

    $payment_status="paid";


    try
    {
      
      if($post_data["post_status"]=="accept"){
        
        $weight=$_REQUEST['weight_by_db'];
        $basic_charge = $_REQUEST['basic_charge_db'];
        $delivery_charge=$_REQUEST['delivery_charge_db'];
        $ack_charges=$_REQUEST['ack_charge_db'];
        $discount=isset($_REQUEST['discount_db'])?$_REQUEST['discount_db']:"0";
        $total_charges=($basic_charge+$ack_charges+$delivery_charge)-$discount;
        $total_payment=$_REQUEST["total_amt_db"];
        
        $barcode=$_REQUEST["barcode"];
        $envelope_img=$_FILES['envelope_img']['name'];
        $envelope_src=$_FILES['envelope_img']['tmp_name'];

        //rename file for envelope image
        if ($_FILES["envelope_img"]["name"] != "")
        {
          if(file_exists("post_images/" . $envelope_img)) {
              $i = 0;
              $EnveFileName = $_FILES["envelope_img"]["name"];
              $Arr1 = explode('.', $EnveFileName);

              $EnveFileName = $Arr1[0] . $i . "." . $Arr1[1];
              while (file_exists("post_images/" . $EnveFileName)) {
                  $i++;
                  $EnveFileName = $Arr1[0] . $i . "." . $Arr1[1];
              }
         } 
         else {
              $EnveFileName = $_FILES["envelope_img"]["name"];
          }
        }


        // update post  
        $stmt = $obj->con1->prepare("update post set weight=?, basic_charges=?, delivery_charge=?, ack_charges=?, total_charges=?, discount=?, total_payment=?, post_status=?, payment_status=? where id=?");
        $stmt->bind_param("sssssssssi",$weight,$basic_charge,$delivery_charge,$ack_charges,$total_charges,$discount,$total_payment,$post_status_transit,$payment_status,$post_id);
        $Resp=$stmt->execute();
        $stmt->close();

        //insert into delivery images
        $stmt_deli_img = $obj->con1->prepare("insert into delivery_images(job_id,image,status) values(?,?,?),(?,?,?)");
        $stmt_deli_img->bind_param("ississ",$deliboy_data['id'],$EnveFileName,$img_status_transit,$deliboy_data['id'],$barcode,$barcode_status);
        $Resp=$stmt_deli_img->execute();
        $stmt_deli_img->close();

        //update job assign status
        $stmt_job_status = $obj->con1->prepare("update job_assign set job_status=? where id=?");
        $stmt_job_status->bind_param("si",$post_status_transit,$deliboy_data['id']);
        $Resp=$stmt_job_status->execute();
        $stmt_job_status->close();      
      }
      else if($post_data["post_status"]=="transit"){

        $receipt_img=$_FILES['receipt_img']['name'];
        $receipt_src=$_FILES['receipt_img']['tmp_name'];

        //rename file for receipt image
        if ($_FILES["receipt_img"]["name"] != "")
        {
          if(file_exists("post_images/" . $receipt_img)) {
              $i = 0;
              $ReceFileName = $_FILES["receipt_img"]["name"];
              $Arr1 = explode('.', $ReceFileName);

              $ReceFileName = $Arr1[0] . $i . "." . $Arr1[1];
              while (file_exists("post_images/" . $ReceFileName)) {
                  $i++;
                  $ReceFileName = $Arr1[0] . $i . "." . $Arr1[1];
              }
         } 
         else {
              $ReceFileName = $_FILES["receipt_img"]["name"];
          }
        }

        // update post  
        $stmt = $obj->con1->prepare("update post set post_status=? where id=?");
        $stmt->bind_param("si",$post_status_dispatch,$post_id);
        $Resp=$stmt->execute();
        $stmt->close();

        //insert into delivery images
        $stmt_deli_img = $obj->con1->prepare("insert into delivery_images(job_id,image,status) values(?,?,?)");
        $stmt_deli_img->bind_param("iss",$deliboy_data['id'],$ReceFileName,$img_status_dispatch);
        $Resp=$stmt_deli_img->execute();
        $stmt->close();

        //update job assign status
        $stmt_job_status = $obj->con1->prepare("update job_assign set job_status=? where id=?");
        $stmt_job_status->bind_param("si",$post_status_dispatch,$deliboy_data['id']);
        $Resp=$stmt_job_status->execute();
        $stmt_job_status->close();      
      }
      
      if(!$Resp)
      {
        throw new Exception("Problem in adding! ". strtok($obj->con1-> error,  '('));
      }
     
    } 
    catch(\Exception  $e) {
      setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
    }


    if($Resp)
    {
      if($post_data["post_status"]=="accept"){
        move_uploaded_file($envelope_src,"post_images/".$EnveFileName);
      } else if($post_data["post_status"]=="transit"){
        move_uploaded_file($receipt_src,"post_images/".$ReceFileName);  
      }
      
      setcookie("msg", "data",time()+3600,"/");
      header("location:customer_report_detail.php");
    }
    else
    {
      setcookie("msg", "fail",time()+3600,"/");
      header("location:customer_report_detail.php");
    } 
  }

?>


<?php 
  if(isset($_COOKIE["msg"]) )
  {

    if($_COOKIE['msg']=="data")
    {

    ?>
    <div class="alert alert-primary alert-dismissible" role="alert">
      Data added succesfully
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
      </button>
    </div>
    <script type="text/javascript">eraseCookie("msg")</script>
    <?php
    }
    if($_COOKIE['msg']=="update")
    {

    ?>
    <div class="alert alert-primary alert-dismissible" role="alert">
      Data updated succesfully
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
      </button>
    </div>
    <script type="text/javascript">eraseCookie("msg")</script>
    <?php
    }
    if($_COOKIE['msg']=="data_del")
    {

    ?>
    <div class="alert alert-primary alert-dismissible" role="alert">
      Data deleted succesfully
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
      </button>
    </div>
    <script type="text/javascript">eraseCookie("msg")</script>
    <?php
    }
    if($_COOKIE['msg']=="fail")
    {
    ?>

    <div class="alert alert-danger alert-dismissible" role="alert">
      An error occured! Try again.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
      </button>
    </div>
    <script type="text/javascript">eraseCookie("msg")</script>
    <?php
    }
  }
    if(isset($_COOKIE["sql_error"]))
    {
      ?>
      <div class="alert alert-danger alert-dismissible" role="alert">
        <?php echo urldecode($_COOKIE['sql_error'])?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        </button>
      </div>

      <script type="text/javascript">eraseCookie("sql_error")</script>
      <?php
    }
  ?>

<h4 class="fw-bold py-3 mb-4">Post Job Report</h4>

                <!-- Basic Layout -->
                <div class="row">
                  <div class="col-xl">
                    <div class="card mb-4">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Post Details (Order ID: <?php echo $post_id ?>)</h5>
                      </div>  
                      
                      <div class="card-body">
                        <form method="post" >
                          <input type="hidden" name="ttId" id="ttId">
                          <div class="row g-2">
                            <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Recipient Name</label>
                            <input type="text" class="form-control" name="receiver_name" id="receiver_name" value="<?php echo $post_data['receiver_name'] ?>" readonly required />
                          </div>

                          <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Sender</label>
                            <input type="text" class="form-control" name="sender" id="sender" value="<?php echo $post_data['cust_name'] ?>" readonly required />
                          </div>
                          
                        </div>
                        <div class="row g-2">
                          
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">House No./Apt No.</label>
                            <input type="text" class="form-control" name="house" id="house" value="<?php echo $post_data['house_no'] ?>" readonly required />                        
                          </div>
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Street 1</label>
                            <input type="text" class="form-control" name="street" id="street" value="<?php echo $post_data['street_1'] ?>" readonly required />                        
                          </div>
                        </div>

                        <div class="row g-2">
                          <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Area</label>
                            <input type="text" name="area" id="area" value="<?php echo $post_data['area'] ?>" readonly required  class="form-control">
                          </div>
                          <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">City</label>
                            <input type="text" name="city" id="city" value="<?php echo $post_data['city'] ?>" readonly required  class="form-control">              
                          </div>
                          
                        </div>
                        <div class="row g-2">
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Pincode</label>
                            <input type="text" pattern="[0-9]{6}" class="form-control" name="pincode" id="pincode" value="<?php echo $post_data['pincode'] ?>" readonly required />                  
                          </div>
                          <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Mail Type</label>
                            <input type="text" class="form-control" name="mail_type" id="mail_type" value="<?php echo $post_data['mail_type_name'] ?>" readonly required />
                          </div>
                        </div>
                        <div class="row g-2">
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Approx Weight(in grams)</label>
                            <input type="number" min="1" class="form-control" name="weight" id="weight" value="<?php echo $post_data['weight'] ?>" readonly required/>
                          </div>
                          
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Need Acknowledgement?</label>
                            <div class="form-check form-check-inline mt-3">
                              <input class="form-check-input" type="radio" name="ack" id="ack_yes" value="yes" disabled <?php if($post_data['acknowledgement']=='yes'){ ?> checked <?php } ?>>
                              <label class="form-check-label" for="inlineRadio1">Yes</label>
                            </div>
                            <div class="form-check form-check-inline mt-3">
                              <input class="form-check-input" type="radio" name="ack" id="ack_no" value="no" disabled <?php if($post_data['acknowledgement']=='no'){ ?> checked <?php } ?>>
                              <label class="form-check-label" for="inlineRadio1">No</label>
                            </div>
                          </div>
                        </div>
                        <div class="row g-2">
                        <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Priority</label>
                            <div class="form-check form-check-inline mt-3">
                              <input class="form-check-input" type="radio" name="priority" id="most_urgent" value="most urgent" disabled <?php if($post_data['priority']=='most urgent'){ ?> checked <?php } ?>>
                              <label class="form-check-label" for="inlineRadio1">Most Urgent</label>
                            </div>
                            <div class="form-check form-check-inline mt-3">
                              <input class="form-check-input" type="radio" name="priority" id="urgent" value="urgent" disabled <?php if($post_data['priority']=='urgent'){ ?> checked <?php } ?>>
                              <label class="form-check-label" for="inlineRadio1">Urgent</label>
                            </div>
                            <div class="form-check form-check-inline mt-3">
                              <input class="form-check-input" type="radio" name="priority" id="normal" value="normal" disabled <?php if($post_data['priority']=='normal'){ ?> checked <?php } ?>>
                              <label class="form-check-label" for="inlineRadio1">Normal</label>
                            </div>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Collect From</label>
                            <input type="text" class="form-control" name="coll_address" id="coll_address" value="<?php echo $sender_addr["address_label"]."-".$sender_addr["house_no"].",".$sender_addr["street"].",".$sender_addr["area_name"].",".$sender_addr["city_name"] ?>" readonly required />
                          </div>
                        </div>  
                        <div class="row g-2">
                          <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Collection Time</label>
                            <input type="text" class="form-control" name="coll_time" id="coll_time" value="<?php echo $post_data["start_time"]." - ".$post_data["end_time"]?>" readonly required />          
                          </div> 
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Collection Date</label>
                            <input type="date" class="form-control" name="collection_dt" id="collection_dt" value="<?php echo date('Y-m-d', strtotime($post_data["collection_date"])) ?>" required />
                          </div>
                        </div>
                        <div class="row g-2">
                          <div class="col mb-3">
                             <label class="form-label d-block" for="basic-default-fullname">Dispatch Date</label>
                            <input type="date" class="form-control" name="dispatch_dt" id="dispatch_dt" required value="<?php echo date('Y-m-d', strtotime($post_data["dispatch_date"])) ?>"/>
                          </div>
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Basic Charges(Rs.)</label>
                            <input type="text" class="form-control" name="basic_charge" id="basic_charge" readonly value="<?php echo $post_data["basic_charges"] ?>" />
                          </div>
                        </div>
                        <div class="row g-2">
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Acknowledgement Charges(Rs.)</label>
                            <input type="text" class="form-control" name="ack_charge" id="ack_charge" readonly value="<?php echo $post_data["ack_charges"] ?>" />
                          </div>
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Delivery Charges(Rs.)</label>
                            <input type="text" class="form-control" name="delivery_charge" id="delivery_charge" readonly value="<?php echo $post_data["delivery_charge"] ?>" />
                          </div>
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Total Charges(Rs.)</label>
                            <input type="text" class="form-control" name="total_charge" id="total_charge" readonly value="<?php echo $post_data["total_charges"] ?>" />
                          </div>
                        </div>
                        <div class="row g-2">
                          
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Apply Coupon</label>
                            <input type="text" class="form-control" name="coupon" id="coupon" readonly <?php if($post_data['coupon_id']==0){ ?> value="No Coupon Code Applied" <?php } else{ ?> value="<?php echo $coupon["couponcode"] ?>" <?php } ?> />
                          </div>
                          
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Discount(Rs.)</label>
                            <input type="text" class="form-control" name="discount" id="discount" readonly value="<?php if($post_data['coupon_id']==0){ echo '0'; } else{ echo $post_data["discount"]; } ?>" />
                          </div>
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Total Payable(Rs.)</label>
                            <input type="text" class="form-control" name="total_amt" id="total_amt" readonly value="<?php echo $post_data["total_payment"] ?>" />
                          </div>
                        </div>
                      
                        </form>
                      </div>
                    </div>


            <?php
              if($deliboy_data["count"]==1){
            ?>
                    <div class="card mb-4">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Delivery Details (Order Status: <?php echo $post_data["post_status"] ?>)</h5>
                      </div>  
                      
                      <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                          <input type="hidden" name="dbId" id="dbId">
                          
                          <div class="row g-3">
                            <div class="col mb-3">
                              <label class="form-label d-block" for="basic-default-fullname">Delivery Boy Name : <?php echo $deliboy_data_info['name'] ?></label>
                            </div>
                            <div class="col mb-3">
                              <label class="form-label d-block" for="basic-default-fullname">Phone Number : <?php echo $deliboy_data_info['contact'] ?></label>
                            </div>
                          </div>
                          <div class="row g-3">
                            <div class="col mb-3">
                              <label class="form-label" for="basic-default-fullname">Profile Pic of Delivery Boy : </label>
                              <img src="deliveryboy_id/<?php echo $deliboy_data_info["profile_pic"] ?>" name="dis_img" id="dis_img" width="50" height="50">
                            </div>
                            <div class="col mb-3">
                              <label class="form-label d-block" for="basic-default-fullname">Payment Status : <?php echo $post_data['payment_status'] ?></label>
                            </div>
                          </div>
                          <div class="row g-2">
                            <div class="col mb-3">
                            <input type="hidden" class="form-control" name="mail_type_db" id="mail_type_db"value="<?php echo $post_data["mail_type"] ?>" />
                            <label class="form-label" for="basic-default-fullname">Weight</label>
                            <input type="text" class="form-control" name="weight_by_db" id="weight_by_db" value="<?php echo $post_data['weight'] ?>" onchange="get_amount(this.value)" required <?php if($post_data["post_status"]=="transit" || $post_data["post_status"]=="dispatched"){ ?> readonly <?php } ?>/>
                            <div id="weight_alert" class="text-danger"></div>
                          </div>
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Barcode</label>
                            <input type="text" class="form-control" name="barcode" id="barcode" required <?php if($post_data["post_status"]=="transit" || $post_data["post_status"]=="dispatched"){ ?> readonly value="<?php echo $deli_imgs_data1["barcode"] ?>" <?php } ?> /> 
                          </div>
                        </div>

                        <div class="row g-2">
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Basic Charges(Rs.)</label>
                            <input type="text" class="form-control" name="basic_charge_db" id="basic_charge_db" readonly value="<?php echo $post_data["basic_charges"] ?>" />
                          </div>
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Acknowledgement Charges(Rs.)</label>
                            <input type="text" class="form-control" name="ack_charge_db" id="ack_charge_db" readonly value="<?php echo $post_data["ack_charges"] ?>" />
                          </div>
                        </div>
                        
                        <div class="row g-2">
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Delivery Charges(Rs.)</label>
                            <input type="text" class="form-control" name="delivery_charge_db" id="delivery_charge_db" readonly value="<?php echo $post_data["delivery_charge"] ?>" />
                          </div>
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Apply Coupon</label>
                            <input type="text" class="form-control" name="coupon_db" id="coupon_db" readonly <?php if($post_data['coupon_id']==0){ ?> value="No Coupon Code Applied" <?php } else{ ?> value="<?php echo $coupon["couponcode"] ?>" <?php } ?> />
                            <input type="hidden" class="form-control" name="coupon_id" id="coupon_id"value="<?php echo $post_data["coupon_id"] ?>" />
                            <div id="coupon_alert" class="text-danger"></div>
                          </div>
                        </div>

                        <div class="row g-2">
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Discount(Rs.)</label>
                            <input type="text" class="form-control" name="discount_db" id="discount_db" readonly value="<?php if($post_data['coupon_id']==0){ echo '0'; } else{ echo $post_data["discount"]; } ?>" />
                          </div>
                          <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Total Payable(Rs.)</label>
                            <input type="text" class="form-control" name="total_amt_db" id="total_amt_db" value="<?php echo $post_data["total_payment"] ?>" readonly required />
                          </div>
                        </div>
                      
                        <div class="row g-2">
                          <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Image of Envelope (Delivery)</label>
                          <?php if($post_data["post_status"]=="transit" || $post_data["post_status"]=="dispatched"){ ?>
                              <img src="post_images/<?php echo $deli_imgs_data1["transit_image"] ?>" name="del_img" id="del_img" width="100" height="100">
                          <?php } else{ ?>
                              <input type="file" class="form-control" onchange="readEnvelopeURL(this)" name="envelope_img" id="envelope_img" required />
                              <img src="" name="PreviewEnveImage" id="PreviewEnveImage" width="100" height="100" style="display:none;">
                              <div id="imgdivEnve" style="color:red"></div>
                          <?php } ?>
                          </div>
                          <div class="col mb-3">
                          <?php if($post_data["post_status"]=="transit"){ ?>
                            <label class="form-label" for="basic-default-fullname">Image of Receipt (Dispatch)</label>
                            <input type="file" class="form-control" onchange="readReceiptURL(this)" name="receipt_img" id="receipt_img" required />
                            <img src="" name="PreviewReceImage" id="PreviewReceImage" width="100" height="100" style="display:none;">
                            <div id="imgdivRece" style="color:red"></div>
                          <?php } else if($post_data["post_status"]=="dispatched"){ ?>
                            <label class="form-label" for="basic-default-fullname">Image of Receipt (Dispatch)</label>
                            <img src="post_images/<?php echo $deli_imgs_data2["dispatch_image"] ?>" name="dis_img" id="dis_img" width="100" height="100">
                          <?php } ?>
                          </div>
                        </div>


                      <?php if($post_data["post_status"]!="dispatched"){ ?>
                        <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Save</button>
                      
                        <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location.reload()">Cancel</button>
                      <?php } ?>
                        </form>
                      </div>
                    </div>

            <?php } ?>

                  </div>
                  
                </div>

            <!-- / Content -->
<script type="text/javascript">

  function get_amount(val){
    var mail_type=$('#mail_type_db').val();
    var ack_charge=$('#ack_charge_db').val();
    var del_charge=$('#delivery_charge_db').val();
    var coupon=$('#coupon_id').val();
    $.ajax({
        async: true,
        type: "POST",
        url: "ajaxdata.php?action=get_amount",
        data: "weight="+val+"&mail_type="+mail_type,
        cache: false,
        success: function(result){
          var total_amt=parseFloat(result)+parseFloat(ack_charge)+parseFloat(del_charge);
          $('#basic_charge_db').html('');
          $('#basic_charge_db').val(result);
          $('#total_amt_db').val(total_amt);
          $('#weight_alert').html('');
          if(result==0){
            $('#weight_alert').html('Weight Not Available');  
          }
        }
      });
    get_coupon_disc(coupon);
  }

  function get_coupon_disc(val)
  {
    var sender=$('#sender').val();
    var basic_charge=$('#basic_charge_db').val();
    var ack_charge=$('#ack_charge_db').val();
    var del_charge=$('#delivery_charge_db').val();
    var total_del_charge=parseFloat(ack_charge)+parseFloat(del_charge);
    var main_total=parseFloat(basic_charge)+parseFloat(ack_charge)+parseFloat(del_charge);
    if(val!=0)
    {
      var total_amt=$('#total_amt_db').val();
      $.ajax({
        async: true,
        type: "POST",
        url: "ajaxdata.php?action=get_coupon_disc",
        data: "coupon_id="+val+"&total_amt="+total_amt+"&sender="+sender+"&total_del_charge="+total_del_charge,
        cache: false,
        success: function(result){
         // var total_amt=parseInt(result)+parseInt(ack_charge)+parseInt(del_charge);
          console.log(result);
          
          if (result == 1) {
                $("#coupon_db").val('');
                $('#coupon_alert').html('Invalid Coupon Code');
                $('#total_amt_db').val(main_total);
                $('#discount_db').val('0');
            } else if (result == 2) {
                $("#coupon_db").val('');
                $('#coupon_alert').html('Amount is too small');
                $('#total_amt_db').val(main_total);
                $('#discount_db').val('0');
            } else {
                var data = result.split("@@@@@");
                var tamount = parseFloat(total_amt) - parseFloat(data[1]);
                console.log("tamount=" + parseFloat((tamount * 100) / 100).toFixed(2));
                $('#coupon_alert').html('');
                
                $('#discount_db').val(parseFloat(data[1]).toFixed(2));
                
                $('#total_amt_db').val(parseFloat(tamount).toFixed(2));
            }
          }
         
      });

    }
    else
    {
      $('#discount_db').val('');
      var ack_charge=$('#ack_charge_db').val();
      var del_charge=$('#delivery_charge_db').val();
      var basic_charge=$('#basic_charge_db').val();
      var total_amt=parseFloat(basic_charge)+parseFloat(ack_charge)+parseFloat(del_charge);
      $('#total_amt_db').val(parseFloat(total_amt).toFixed(2));
    }
  }

  function readEnvelopeURL(input) {
    if (input.files && input.files[0]) {
      var filename=input.files.item(0).name;

      var reader = new FileReader();
      var extn=filename.split(".");

      if(extn[1].toLowerCase()=="jpg" || extn[1].toLowerCase()=="jpeg" || extn[1].toLowerCase()=="png" || extn[1].toLowerCase()=="bmp") {
        reader.onload = function (e) {
          $('#PreviewEnveImage').attr('src', e.target.result);
          document.getElementById("PreviewEnveImage").style.display = "block";
        };

        reader.readAsDataURL(input.files[0]);
        $('#imgdivEnve').html("");
        document.getElementById('btnsubmit').disabled = false;     
      }
      else
      {
        $('#imgdivEnve').html("Please Select Image Only");
        document.getElementById('btnsubmit').disabled = true;
      }
    }
  }

  function readReceiptURL(input) {
    if (input.files && input.files[0]) {
      var filename=input.files.item(0).name;

      var reader = new FileReader();
      var extn=filename.split(".");

      if(extn[1].toLowerCase()=="jpg" || extn[1].toLowerCase()=="jpeg" || extn[1].toLowerCase()=="png" || extn[1].toLowerCase()=="bmp") {
        reader.onload = function (e) {
          $('#PreviewReceImage').attr('src', e.target.result);
          document.getElementById("PreviewReceImage").style.display = "block";
        };

        reader.readAsDataURL(input.files[0]);
        $('#imgdivRece').html("");
        document.getElementById('btnsubmit').disabled = false;     
      }
      else
      {
        $('#imgdivRece').html("Please Select Image Only");
        document.getElementById('btnsubmit').disabled = true;
      }
    }
  }

</script>
<?php 
  include("footer.php");
?>