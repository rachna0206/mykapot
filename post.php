<?php
  include("header.php");

  //customer list
  $stmt_slist = $obj->con1->prepare("select * from customer_reg");
  $stmt_slist->execute();
  $res = $stmt_slist->get_result();
  $stmt_slist->close();

  // city list

  $stmt_city = $obj->con1->prepare("select * from city where status='enable'");
  $stmt_city->execute();
  $res_city = $stmt_city->get_result();
  $stmt_city->close();

  // mail type

  $stmt_mail= $obj->con1->prepare("select * from mail_type where status='enable'");
  $stmt_mail->execute();
  $res_mail  = $stmt_mail->get_result();
  $stmt_mail->close();


  // collection adddress

  $stmt_address= $obj->con1->prepare("select ca.*,c1.city_name,a1.area_name from customer_address ca, area a1,city c1 where ca.area_id=a1.aid and ca.city_id=c1.city_id");
  $stmt_address->execute();
  $res_address  = $stmt_address->get_result();
  $stmt_address->close();

  // collection Time

  $stmt_time= $obj->con1->prepare("select * from collection_time where status='enable'");
  $stmt_time->execute();
  $res_coll_time = $stmt_time->get_result();
  $stmt_time->close();

  // coupon list

  $stmt_coupon = $obj->con1->prepare("select * from coupon where status='enable'");
  $stmt_coupon->execute();
  $res_coupon = $stmt_coupon->get_result();
  $stmt_coupon->close();

  // insert data
  if(isset($_REQUEST['btnsubmit']))
  {
    $receiver_name = $_REQUEST['receiver_name'];
    $sender = $_REQUEST['sender'];
    $house=$_REQUEST['house'];
    $street=$_REQUEST['street'];
    $area=$_REQUEST['area'];
    $city=$_REQUEST['city'];
    $pincode=$_REQUEST['pincode'];
    $mail_type=$_REQUEST['mail_type'];
    $weight=$_REQUEST['weight'];
    $ack=$_REQUEST['ack'];
    $priority=$_REQUEST['priority'];
    $coll_address=$_REQUEST['coll_address'];
    $coll_time=$_REQUEST['coll_time'];
    $dispatch_date=$_REQUEST['dispatch_dt'];
    $basic_charge = $_REQUEST['basic_charge'];
    $delivery_charge=$_REQUEST['delivery_charge'];
    $ack_charges=$_REQUEST['ack_charge'];
    
    $coupon_id=$_REQUEST['coupon'];
    $discount=$_REQUEST['discount'];
    $total_charges=($basic_charge+$ack_charges+$delivery_charge)-$discount;
    $total_payment=$_REQUEST["total_amt"];
    $noti_status=1;
    $playstatus=1;
    $noti_type="post";
    try
    {
      
      
  	$stmt = $obj->con1->prepare("INSERT INTO `post`( `receiver_name`, `sender_id`, `house_no`, `street_1`, `area`, `city`, `pincode`, `mail_type`, `weight`, `acknowledgement`, `priority`, `collection_address`, `collection_time`, `dispatch_date`, `basic_charges`, `delivery_charge`, `ack_charges`, `total_charges`, `coupon_id`, `discount`, `total_payment`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
  	$stmt->bind_param("sisssisissssssssssiss",$receiver_name,$sender,$house,$street,$area,$city,$pincode,$mail_type,$weight,$ack,$priority,$coll_address,$coll_time,$dispatch_date,$basic_charge,$delivery_charge,$ack_charges,$total_charges,$coupon_id,$discount,$total_payment);
  	$Resp=$stmt->execute();
     $stmt->close();
     $insert_id = mysqli_insert_id($obj->con1);
     //insert into notification
     $stmt_noti = $obj->con1->prepare("INSERT INTO `notification`( `noti_type`, `noti_type_id`, `status`, `playstatus`) VALUES (?,?,?,?)");
    $stmt_noti->bind_param("siii",$noti_type,$insert_id,$noti_status,$playstatus);
    $Resp_noti=$stmt_noti->execute();
    $stmt_noti->close();
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
  	  setcookie("msg", "data",time()+3600,"/");
      header("location:post.php");
    }
    else
    {
  	  setcookie("msg", "fail",time()+3600,"/");
        header("location:post.php");
    }
  }

  if(isset($_REQUEST['btnupdate']))
  {
    $state_id = $_REQUEST['state'];
    $post_name = $_REQUEST['post_name'];
    $status = $_REQUEST['status'];
    $id=$_REQUEST['ttId'];
    $action='updated';
    try
    {
      $stmt = $obj->con1->prepare("update post set post_name=?, state=?, status=?,action=? where post_id=?");
  	$stmt->bind_param("sissi", $post_name,$state_id,$status,$action,$id);
  	$Resp=$stmt->execute();
      if(!$Resp)
      {
        throw new Exception("Problem in updating! ". strtok($obj->con1-> error,  '('));
      }
      $stmt->close();
    } 
    catch(\Exception  $e) {
      setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
    }


    if($Resp)
    {
  	  setcookie("msg", "update",time()+3600,"/");
        header("location:post.php");
    }
    else
    {
  	  setcookie("msg", "fail",time()+3600,"/");
        header("location:post.php");
    }
  }

  // delete data
  if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
  {
    try
    {
      $stmt_del = $obj->con1->prepare("delete from post where id='".$_REQUEST["n_id"]."'");
    	$Resp=$stmt_del->execute();
      if(!$Resp)
      {
        throw new Exception("Problem in deleting! ". strtok($obj->con1-> error,  '('));
      }
      $stmt_del->close();
    } 
    catch(\Exception  $e) {
      setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
    }


    if($Resp)
    {
  	setcookie("msg", "data_del",time()+3600,"/");
      header("location:post.php");
    }
    else
    {
  	setcookie("msg", "fail",time()+3600,"/");
      header("location:post.php");
    }
  }

  ?>

  <h4 class="fw-bold py-3 mb-4">Post Master</h4>

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


                <!-- Basic Layout -->
                <div class="row">
                  <div class="col-xl">
                    <div class="card mb-4">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Add post</h5>
                        
                      </div>
                      <div class="card-body">
                        <form method="post" >
                          <input type="hidden" name="ttId" id="ttId">
                          <div class="row g-2">
                            <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Recipient Name</label>
                            <input type="text" class="form-control" name="receiver_name" id="receiver_name" required />
                          </div>

                          <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Sender</label>
                            <select name="sender" id="sender" class="form-control" required onchange="get_address(this.value)">
                              <option value="">Select Sender</option>
                                    <?php    
                                        while($sender=mysqli_fetch_array($res)){
                                    ?>
                                        <option value="<?php echo $sender["id"] ?>"><?php echo $sender["name"] ?></option>
                                    <?php
                            }
                          ?>
                            </select>
                                          
                          </div>
                          
                        </div>
                        <div class="row g-2">
                          
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">House No./Apt No.</label>
                            <input type="text" class="form-control" name="house" id="house" required />                        
                          </div>
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Street 1</label>
                            <input type="text" class="form-control" name="street" id="street" required />                        
                          </div>
                        </div>

                        <div class="row g-2">
                          <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Area</label>
                            <input type="text" name="area" id="area" required  class="form-control">
                                          
                          </div>
                          <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">City</label>
                            <select name="city" id="city" class="form-control" required>
                              <option value="">Select City</option>
                                    <?php    
                                        while($city=mysqli_fetch_array($res_city)){
                                    ?>
                                        <option value="<?php echo $city["city_id"] ?>"><?php echo $city["city_name"] ?></option>
                                    <?php
                            }
                          ?>
                                </select>
                                          
                          </div>
                          
                        </div>
                        <div class="row g-2">
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Pincode</label>
                            <input type="text" pattern="[0-9]{6}" class="form-control" name="pincode" id="pincode" required />                        
                          </div>
                          <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Mail Type</label>
                            <select name="mail_type" id="mail_type" class="form-control" required>
                              <option value="">Select Mail Type</option>
                                    <?php    
                                        while($mail_type=mysqli_fetch_array($res_mail)){
                                    ?>
                                        <option value="<?php echo $mail_type["id"] ?>"><?php echo $mail_type["mail_type"] ?></option>
                                    <?php
                            }
                          ?>
                                </select>
                                          
                          </div>
                        </div>
                        <div class="row g-2">
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Approx Weight</label>
                            <input type="text" class="form-control" name="weight" id="weight" required onblur="get_amount(this.value)"/>
                          </div>
                          
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Need Acknowledgement?</label>
                            <div class="form-check form-check-inline mt-3">
                              <input class="form-check-input" type="radio" name="ack" id="ack_yes" value="yes" required checked onclick="set_ack_charges()">
                              <label class="form-check-label" for="inlineRadio1">Yes</label>
                            </div>
                            <div class="form-check form-check-inline mt-3">
                              <input class="form-check-input" type="radio" name="ack" id="ack_no" value="no" required onclick="set_ack_charges()">
                              <label class="form-check-label" for="inlineRadio1">No</label>
                            </div>
                          </div>
                        </div>
                        <div class="row g-2">
                        <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Priority</label>
                            <div class="form-check form-check-inline mt-3">
                              <input class="form-check-input" type="radio" name="priority" id="most_urgent" value="most urgent" required checked>
                              <label class="form-check-label" for="inlineRadio1">Most Urgent</label>
                            </div>
                            <div class="form-check form-check-inline mt-3">
                              <input class="form-check-input" type="radio" name="priority" id="urgent" value="urgent" required>
                              <label class="form-check-label" for="inlineRadio1">Urgent</label>
                            </div>
                            <div class="form-check form-check-inline mt-3">
                              <input class="form-check-input" type="radio" name="priority" id="normal" value="normal" required>
                              <label class="form-check-label" for="inlineRadio1">Normal</label>
                            </div>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Collect From</label>
                            <select name="coll_address" id="coll_address" class="form-control" required>
                              <option value="">Select Collection Address</option>
                                    <?php    
                                        while($address=mysqli_fetch_array($res_address)){
                                    ?>
                                        <option value="<?php echo $address["ca_id"] ?>"><?php echo $address["address_label"]."-".$address["house_no"].",".$address["street"].",".$address["area_name"].",".$address["city_name"] ?></option>
                                    <?php
                            }
                          ?>
                                </select>
                                          
                          </div>
                        </div>  
                        <div class="row g-2">
                          
                          
                          <div class="col mb-3">
                            <label class="form-label" for="basic-default-fullname">Collection Time</label>
                            <select name="coll_time" id="coll_time" class="form-control" required>
                              <option value="">Select Collection Time</option>
                                    <?php    
                                        while($coll_time=mysqli_fetch_array($res_coll_time)){
                                    ?>
                                        <option value="<?php echo $coll_time["id"] ?>"><?php echo $coll_time["start_time"]."-".$coll_time["end_time"]?></option>
                                    <?php
                            }
                          ?>
                            </select>                                          
                          </div>
                          <div class="col mb-3">

                             <label class="form-label d-block" for="basic-default-fullname">Dispatch Date</label>
                            <input type="date" class="form-control" name="dispatch_dt" id="dispatch_dt" required />
                          </div>
                        </div>
                        <div class="row g-2">
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Basic Charges(Rs.)</label>
                            <input type="text" class="form-control" name="basic_charge" id="basic_charge" readonly />
                          </div>
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Acknowledgement Charges(Rs.)</label>
                            <input type="text" class="form-control" name="ack_charge" id="ack_charge" readonly value="2.00" />
                          </div>
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Delivery Charges(Rs.)</label>
                            <input type="text" class="form-control" name="delivery_charge" id="delivery_charge" readonly value="20.00" />
                          </div>
                        </div>
                        <div class="row g-2">
                          
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Apply Coupon</label>
                            <select name="coupon" id="coupon" class="form-control"  onchange="get_coupon_disc(this.value)">
                              <option value="">Select Coupon</option>
                                    <?php    
                                        while($coupon=mysqli_fetch_array($res_coupon)){
                                    ?>
                                        <option value="<?php echo $coupon["c_id"] ?>"><?php echo $coupon["couponcode"]?></option>
                                    <?php
                            }
                          ?>
                            </select>  
                            <div id="coupon_alert" class="text-danger"></div>
                          </div>
                          
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Discount(Rs.)</label>
                            <input type="text" class="form-control" name="discount" id="discount" readonly  />
                          </div>
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Total Payable(Rs.)</label>
                            <input type="text" class="form-control" name="total_amt" id="total_amt" readonly  />
                          </div>
                        </div>
                      
                          <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Save</button>
                      
  						            <button type="submit" name="btnupdate" id="btnupdate" class="btn btn-primary " hidden>Update</button>
                      
                          <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location.reload()">Cancel</button>

                        </form>
                      </div>
                    </div>
                  </div>
                  
                </div>
             

             <!-- grid -->

             <!-- Basic Bootstrap Table -->
                <div class="card">
                  <h5 class="card-header">Post Records</h5>
                  <div class="table-responsive text-nowrap">
                    <table class="table" id="table_id">

                      <thead>
                        <tr>
                          <th>Srno</th>
                          <th>Receiver Name</th>
                          <th>Sender</th>
                          <th>Area</th> 
                          <th>City</th>
                          <th>Collection Address</th>
                          <th>Collection Time</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody class="table-border-bottom-0">
                        <?php 
                          $stmt_list = $obj->con1->prepare("select p1.*,a1.area_name,c1.city_name,c2.id as cust_name,c2.name as cust_name,ca.*,c4.id as coll_time_id,c4.start_time,c4.end_time  from post p1,area a1,city c1,mail_type m1,customer_address ca,customer_reg c2,coupon c3,collection_time c4 where ca.area_id=a1.aid and p1.city=c1.city_id  and p1.mail_type=m1.id and p1.collection_address=ca.ca_id and p1.sender_id=c2.id and p1.collection_time=c4.id and p1.coupon_id=c3.c_id order by p1.id desc");
                          $stmt_list->execute();
                          $result = $stmt_list->get_result();
                          
                          $stmt_list->close();
                          $i=1;
                          while($post=mysqli_fetch_array($result))
                          {
                            ?>

                        <tr>
                          <td><?php echo $i?></td>
                          <td><?php echo $post["receiver_name"]?></td>
                          <td><?php echo $post["cust_name"]?></td>
                          <td><?php echo $post["area"]?></td>
                          <td><?php echo $post["city_name"]?></td>    
                          <td><?php echo $post["address_label"]."-".$post["house_no"].",".$post["street"].",".$post["area_name"].",".$post["city_name"] ?></td>
                          <td><?php echo $post["start_time"]."-".$post["end_time"]?></td>
                                       
                          <td>
                          
                          	<!-- <a href="javascript:editdata('<?php echo $post["id"]?>');"><i class="bx bx-edit-alt me-1"></i> </a> -->
                          
  							           <a  href="javascript:deletedata('<?php echo $post["id"]?>');"><i class="bx bx-trash me-1"></i> </a>
                          
                          	<!-- <a href="javascript:viewdata('<?php echo $post["id"]?>');">View</a> -->
                          
                          </td>
                    
                          
                        </tr>
                        <?php
                            $i++;
                          }
                        ?>
                        
                      </tbody>
                    </table>
                  </div>
                </div>
                <!--/ Basic Bootstrap Table -->


             <!-- / grid -->

              <!-- / Content -->
  <script type="text/javascript">
    function get_address(sender){
      $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=get_address",
          data: "uid="+sender,
          cache: false,
          success: function(result){
            $('#coll_address').html('');
            $('#coll_address').append(result);
          }
        });

    }
    function get_amount(val){
      var mail_type=$('#mail_type').val();
      var ack_charge=$('#ack_charge').val();
      var del_charge=$('#delivery_charge').val();
      var coupon=$('#coupon').val();
      $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=get_amount",
          data: "weight="+val+"&mail_type="+mail_type,
          cache: false,
          success: function(result){
            var total_amt=parseInt(result)+parseInt(ack_charge)+parseInt(del_charge);
            $('#basic_charge').html('');
            $('#basic_charge').val(result);
            $('#total_amt').val(total_amt);
          }
        });
      get_coupon_disc(coupon);
    }
    function set_ack_charges()
    {
      var basic_charge=$('#basic_charge').val();
      var del_charge=$('#delivery_charge').val();
      var coupon=$('#coupon').val();
      if( $('#ack_yes').is(':checked') ){
          $('#ack_charge').val("2.00");
          
          var total_amt=parseInt(basic_charge)+parseInt(2)+parseInt(del_charge);
          
      }
      else{
          $('#ack_charge').val('');
          var total_amt=parseInt(basic_charge)+parseInt(del_charge);
          
      }
      $('#total_amt').val(total_amt);
      get_coupon_disc(coupon);
    }
    function get_coupon_disc(val)
    {
      if(val!="")
      {

      var total_amt=$('#total_amt').val();
      $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=get_coupon_disc",
          data: "coupon_id="+val+"&total_amt="+total_amt,
          cache: false,
          success: function(result){
           // var total_amt=parseInt(result)+parseInt(ack_charge)+parseInt(del_charge);
            console.log(result);
            if (result == 1) {
                $("#coupon").val('');
                $('#coupon_alert').html('Invalid Coupon Code');
                

            } else if (result == 2) {
                $("#coupon").val('');
                $('#coupon_alert').html('Amount is too small');
                
            } else {
                var data = result.split("@@@@@");
                var tamount = parseFloat(total_amt) - parseFloat(data[1]);
                console.log("tamount=" + parseFloat((tamount * 100) / 100).toFixed(2));
                $('#coupon_alert').html('');
                
                $('#discount').val(parseFloat(data[1]).toFixed(2));
                
                $('#total_amt').val(parseFloat(tamount).toFixed(2));
               
                

            }
            
          }
        });
      }
      else
      {
        var ack_charge=$('#ack_charge').val();
        var del_charge=$('#delivery_charge').val();
        var basic_charge=$('#basic_charge').val();
        var total_amt=parseInt(basic_charge)+parseInt(ack_charge)+parseInt(del_charge);
        $('#total_amt').val(parseFloat(total_amt).toFixed(2));
      }
    }
    function deletedata(id) {

        if(confirm("Are you sure to DELETE data?")) {
            var loc = "post.php?flg=del&n_id=" + id;
            window.location = loc;
        }
    }
    function editdata(id,stateid,cname,status) {
             
  		   	$('#ttId').val(id);
              $('#state').val(stateid);
  			$('#post_name').val(atob(cname));
  			if(status=="enable")
  		   	{
  				$('#enable').attr("checked","checked");	
  		   	}
  		   	else if(status=="disable")
  		   	{
  				$('#disable').attr("checked","checked");	
  		   	}
  			
  			$('#btnsubmit').attr('hidden',true);
              $('#btnupdate').removeAttr('hidden');
  			$('#btnsubmit').attr('disabled',true);

          }
    function viewdata(id,stateid,cname,status) {
             
  		   	$('#ttId').val(id);
              $('#state').val(stateid);
  			$('#post_name').val(atob(cname));
  			if(status=="enable")
  		   	{
  				$('#enable').attr("checked","checked");	
  		   	}
  		   	else if(status=="disable")
  		   	{
  				$('#disable').attr("checked","checked");	
  		   	}
  			
  			$('#btnsubmit').attr('hidden',true);
              $('#btnupdate').attr('hidden',true);
  			$('#btnsubmit').attr('disabled',true);

          }
  </script>
  <?php 
  include("footer.php");
  ?>