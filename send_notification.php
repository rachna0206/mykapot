<?php
include("header.php");
error_reporting(E_ALL);


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  
  $option=$_REQUEST["option"];
  $message=$_REQUEST["message"];
  $user = isset($_REQUEST["user"])?$_REQUEST["user"]:"";
  $userstring=isset($_REQUEST["user"])?implode(",",$user):"";
  $image=false;
  $res = 0;
  $res1 = 0;

  if ($_FILES["file1"]["name"] != "") {
   
      $image=true;
      if (file_exists("notification_img/" . $_FILES["file1"]["name"])) {
          $i = 0;
          $MainFileName = $_FILES["file1"]["name"];
          $Arr = explode('.', $MainFileName);
          $MainFileName = $Arr[0] . $i . "." . $Arr[1];
          while (file_exists("notification_img/" . $MainFileName)) {
              $i++;
               $MainFileName = $Arr[0] . $i . "." . $Arr[1];
          }

      } else {
           $MainFileName = $_FILES["file1"]["name"];
         
      }
      
      
      move_uploaded_file($_FILES["file1"]["tmp_name"], "notification_img/" . $MainFileName);
                          
  }

  if($option == 'ios')
  {
    $iosuser_qry="SELECT * FROM `customer_devices` c1,customer_reg c2 where c1.cust_id=c2.id and c1.device_type='ios' and c1.device_token!=''";
    $iosuser_resp=$obj->select($iosuser_qry);
    if(mysqli_num_rows($iosuser_resp)>0)
    {
        while($iosuser_row = mysqli_fetch_array($iosuser_resp))
        {
            $reg_ids_ios[] = $iosuser_row['device_token'];

        }
        $data= new stdClass();
        $data->message = $message;
        $data->body = $message;
        if($image)
        {
            $data->image="notification_img/".$MainFileName;
        }
        
        $data->title = "MyKapot";

        $title = "MyKapot";
        $data->is_dispatch="false";
        $body = $message;
        //notification::send_notification($msg_payload,$ids);
        //send_notification_ios($data, $reg_ids_ios, $title, $body);

        $res = 0;

    }
    else
    {
        $res = 1;

    }

  }
    else if($option == 'android')
    {
        $src='android';
         $user_query = "SELECT * FROM `customer_devices` c1,customer_reg c2 where c1.cust_id=c2.id and c1.device_type='android' and c1.device_token!=''";
        $user_result = $obj->select($user_query);
        $user_num = mysqli_num_rows($user_result);
        if($user_num > 0)
        {

            while($user_row = mysqli_fetch_array($user_result))
            {
                $reg_ids_android[] = $user_row['device_token'];


            }
            // print_r($reg_ids_android);
            $data= new stdClass();
            $data->message = $message;
            $data->body = $message;

            $data->title = "MyKapot";

            $title = "MyKapot";
            $data->is_dispatch="false";
            if($image)
            {
                $data->image="notification_img/".$MainFileName;
            }
            $body = $message;
            
            //send_notification_android($data, $reg_ids_android, $title, $body);
            $res = 0;

        }else
        {
            $res = 1;
        }

    }
    else if($option=="specific_user")
    {
        $user_qry="SELECT * FROM `customer_devices` c1,customer_reg c2 where c1.cust_id=c2.id and c1.device_type='android' and c1.device_token!=''  and find_in_set(c2.id,'".$userstring."')";
        $user_resp=$obj->select($user_qry);
        if(mysqli_num_rows($user_resp)>0)
        {
            while($user_row = mysqli_fetch_array($user_resp))
            {

                if($user_row['type']=="ios")
                {
                    $reg_ids_ios[] = $user_row['device_token'];
                }
                else
                {
                    $reg_ids_android[] = $user_row['device_token'];
                }


            }
            $data= new stdClass();
            $data->message = $message;
            $data->body = $message;

            $data->title = "MyKapot";

            $title = "MyKapot";
            $data->is_dispatch="false";
            if($image)
            {
                $data->image="notification_img/".$MainFileName;
            }
            $body = $message;
            
           // send_notification_ios($data, $reg_ids_ios, $title, $body);
          //  send_notification_android($data, $reg_ids_android, $title, $body);
           

        }
        else{
            $res =1;
        }

    }

    else
    {
        $user_query = "SELECT * FROM `customer_devices` c1,customer_reg c2 where c1.cust_id=c2.id  and c1.device_token!=''";
        $user_result = $obj->select($user_query);
        $user_num = mysqli_num_rows($user_result);
        if($user_num > 0)
        {

            while($user_row = mysqli_fetch_array($user_result))
            {
                if($user_row['device_type']=="ios")
                {
                    $reg_ids_ios[] = $user_row['device_token'];
                }
                else
                {
                    $reg_ids_android[] = $user_row['device_token'];
                }


            }
            //print_r($reg_ids_android);
            $data= new stdClass();
            $data->message = $message;
            $data->body = $message;
            $data->title = "MyKapot";
            $title = "MyKapot";
            $data->is_dispatch="false";
            if($image)
            {
                $data->image="notification_img/".$MainFileName;
            }
            $body = $message;

            send_notification_ios($data, $reg_ids_ios, $title, $body);
            send_notification_android($data, $reg_ids_android, $title, $body);
            $res = 0;

        }
        else
        {
            $res = 1;

        }
    }


    $add_noti="INSERT INTO `notification_center`( `notification_type`, `msg`, `user_ids`,`image`) VALUES ('".$option."','".$message."','".$userstring."','".$MainFileName."')";
    $res_noti=$obj->select($add_noti);
    
    if($res1 == 0)
    {
        setcookie("msg", "data",time()+3600,"/");
        header("location:send_notification.php");

    }else{
        setcookie("msg", "fail",time()+3600,"/");
        header("location:send_notification.php");

    }

}


// fcm notification code
function send_notification_ios($data,$reg_ids,$title,$body)
{
    //$reg_ids[0]="esR5GsVCeEBljF0hszij-k:APA91bEq7A2QCl6Rrt8-__t7OlUemcQOIy_KRe0Zm6h50b8ffZcciHDdnT8f9poGAiW6gcqywi438TWt_aOLN0yk7YKgbOakkvrmTlvVUEtr98aiz69BsgoACxfHXztRmFx-0HprNxLy";
    $url='https://fcm.googleapis.com/fcm/send';
    $api_key='AAAA5lYuOAA:APA91bEImlO4QpQYgwUluphC4Di-qhr9q_E6b9ZvtSvwoSxA9N5CN0LbnOiexpnY8hih5XBUW84wiEeHj_MGBcJR8o9BfkEK9FXVizGwIfMir-NEvZ3_IgjL1Eu8ylZdkoVAKCYt174K';

    $msg = array(
        'title' =>$title,
        'body' => $body,
        'icon' => 'myicon',
        'sound' => 'mySound',
        'data' => $data
    );
    $fields = array(
        'registration_ids'  => $reg_ids,

        'notification' => $msg
    );
//print_r($fields);
    $headers = array(
        'Content-Type:application/json',
        'Authorization:key='.$api_key
    );

    // echo json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);

    //  echo $result;
    return $result;
}

// fcm notificaton for android
function send_notification_android($data,$reg_ids_android,$title,$body)
{
    
    $url='https://fcm.googleapis.com/fcm/send';
    $api_key='AAAA5lYuOAA:APA91bEImlO4QpQYgwUluphC4Di-qhr9q_E6b9ZvtSvwoSxA9N5CN0LbnOiexpnY8hih5XBUW84wiEeHj_MGBcJR8o9BfkEK9FXVizGwIfMir-NEvZ3_IgjL1Eu8ylZdkoVAKCYt174K';
    $msg = array(
        'title' =>$title,
        'body' => $body,
        'icon' => 'myicon',
        'sound' => 'mySound',
        'data' => $data
    );

    $fields = array(
        'registration_ids'  => $reg_ids_android,
        'data' => $data,

    );

    $headers = array(
        'Content-Type:application/json',
        'Authorization:key='.$api_key
    );

    json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);

    //  echo $result;
    return $result;
}

?>


<h4 class="fw-bold py-3 mb-4">Notification Center</h4>

<?php 
if(isset($_COOKIE["msg"]) )
{

  if($_COOKIE['msg']=="data")
  {

  ?>
  <div class="alert alert-primary alert-dismissible" role="alert">
    Notification sent succesfully
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
      <h5 class="mb-0">Send Notification</h5>
      
    </div>
    <div class="card-body">
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="ttId" id="ttId">
        <div class="mb-3">
          <label class="form-label" for="basic-default-fullname">To</label>
            
          
          <div class="form-check form-check-inline mt-3">
            <input type="radio" value="all" name="option" id="all" checked onclick="show_users()">
            <label class="form-check-label" for="inlineRadio1">All</label>
          </div>
          <div class="form-check form-check-inline mt-3">
            <input type="radio" value="ios" name="option" id="ios" onclick="show_users()">
            <label class="form-check-label" for="inlineRadio1">iOS</label>
          </div>
          <div class="form-check form-check-inline mt-3">
            <input type="radio" value="android" name="option" id="android" onclick="show_users()">
            <label class="form-check-label" for="inlineRadio1">Android</label>
          </div>
          <div class="form-check form-check-inline mt-3">
            <input type="radio" value="specific_user" name="option" id="setuser"  onclick="show_users()">
            <label class="form-check-label" for="inlineRadio1">Specific users</label>
          </div>

        </div>
        
        <div class="mb-3" id="users_div" style="display: none">
          <label class="form-label" for="basic-default-fullname">Users</label>
          <select id="user" name="user[]" class="form-control select2-multiple" multiple>

              <?php
              $user_qry="select * from customer_reg";
              $res_qry=$obj->select($user_qry);
              while ($users=mysqli_fetch_array($res_qry))
              {
                  ?>
                  <option value="<?php echo $users["id"]?>"><?php echo $users["name"]?></option>
                  <?php
              }
              ?>
          </select>
        </div>
        
        <div class="mb-3">
          <label class="form-label" for="basic-default-fullname">Image</label>
          <input id="file1" name="file1" type="file" onchange="readURL(this);"/>
                <img id='PreviewImage' name="PreviewImage" src="" height="100" width="120" hidden="true">
        </div>
        
        <div class="mb-3">
          <label class="form-label" for="basic-default-fullname">Message</label>
          <textarea class=" form-control" name="message" id="message" rows="4" required ></textarea>
        </div>
        
    
      
        
    
        <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Save</button>
    
        <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location.reload()">Cancel</button>

      </form>
    </div>
  </div>
</div>

</div>
           



           <!-- grid -->

           <!-- Basic Bootstrap Table -->
              <div class="card">
                <h5 class="card-header">Exercise Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">

                    <thead>
                      <tr>
                        <th>
                            Serial No.
                        </th>
                        <th>Notification Type</th>
                        <th>Message</th>
                        <th>Image</th>
                        <th>Date Time</th>

                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select * from notification_center order by id desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($notification=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td> <?php echo $notification["notification_type"]?></td>
                                <td> <?php echo $notification["msg"]?></td>
                                <?php
                                if($notification["image"]!="")
                                {
                                    $image="notification_img/".$notification["image"];
                                }
                                else
                                {
                                    $image="-";
                                }
                                ?>
                                <td><img src="<?php echo $image?>" width="80" height="80"></td>
                                <td><?php echo $notification["date_time"]?></td>
                        
                   
                        
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
  
  function show_users() {
        if($('#setuser').prop("checked"))
        {
            $('#users_div').show();
        }
        else
        {
            $('#users_div').hide();
        }

    }
    function resendnotification(msg) {

        $('#message').val(msg);
    }
    function readURL(input) {

            var filePath = input.value;
            var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
            if(!allowedExtensions.exec(filePath)){
                alert('Please upload Image only.');
                fileInput.value = '';
                return false;
            }
            else
            {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('#PreviewImage').attr('src', e.target.result);
                        document.getElementById("PreviewImage").hidden = false;

                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }

        }
</script>
<?php 
include("footer.php");
?>