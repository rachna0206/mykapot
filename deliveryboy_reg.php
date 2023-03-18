<?php
include("header.php");
error_reporting(E_ALL);

$stmt_city = $obj->con1->prepare("select * from city where status='enable' and LOWER(city_name)='surat' order by city_id desc");
$stmt_city->execute();
$res_city = $stmt_city->get_result();
$stmt_city->close();

$stmt_zone = $obj->con1->prepare("select * from zone");
$stmt_zone->execute();
$res_zone = $stmt_zone->get_result();
$stmt_zone->close();

// insert data
if(isset($_REQUEST['btnsubmit']))
{
  echo "<br/>".$name = $_REQUEST['name'];
  echo "<br/>".$email = $_REQUEST['email'];
  echo "<br/>".$pass = $_REQUEST['password'];
  echo "<br/>".$contact = $_REQUEST['contact'];
  echo "<br/>".$address = $_REQUEST['address'];
  echo "<br/>".$pincode = $_REQUEST['pincode'];
  echo "<br/>".$city = $_REQUEST['city'];
  echo "<br/>".$zone_id = $_REQUEST['zone'];
  echo "<br/>".$id_type=$_REQUEST['id_type'];
  echo "<br/>".$id_proof = $_FILES['id_proof']['name'];
  echo "<br/>".$id_proof_path = $_FILES['id_proof']['tmp_name'];
  echo "<br/>".$status = $_REQUEST['status'];
  echo "<br/>".$action='added';

  //rename file for id proof
  if ($_FILES["id_proof"]["name"] != "")
    {
      if(file_exists("deliveryboy_id/" . $id_proof)) {
          $i = 0;
          $PicFileName = $_FILES["id_proof"]["name"];
          $Arr1 = explode('.', $PicFileName);

          $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
          while (file_exists("deliveryboy_id/" . $PicFileName)) {
              $i++;
              $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
          }
     } 
     else {
          $PicFileName = $_FILES["id_proof"]["name"];
      }
    }

  try
  {
    
	$stmt = $obj->con1->prepare("INSERT INTO `delivery_boy`( `name`, `email`, `password`, `contact`, `address`, `city`, `pincode`, `id_proof_type`, `id_proof`,`zone_id`, `status`, `action`) values (?,?,?,?,?,?,?,?,?,?,?,?)");
	$stmt->bind_param("sssssisssiss",$name,$email,$pass,$contact,$address,$city,$pincode,$id_type,$PicFileName,$zone_id,$status,$action);
	$Resp=$stmt->execute();
    if(!$Resp)
    {
      throw new Exception("Problem in adding! ". strtok($obj->con1-> error,  '('));
    }
    $stmt->close();
  } 
  catch(\Exception  $e) {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }


  if($Resp)
  {
    move_uploaded_file($id_proof_path,"deliveryboy_id/".$PicFileName);

	  setcookie("msg", "data",time()+3600,"/");
    header("location:deliveryboy_reg.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
    header("location:deliveryboy_reg.php");
  } 
}

if(isset($_REQUEST['btnupdate']))
{
  $name = $_REQUEST['name'];
  $email = $_REQUEST['email'];
  $pass = $_REQUEST['password'];
  $contact = $_REQUEST['contact'];
  $address = $_REQUEST['address'];
  $pincode = $_REQUEST['pincode'];
  $city = $_REQUEST['city'];
  $zone_id = $_REQUEST['zone'];
  $id_type=$_REQUEST['id_type'];
  $id_proof = $_FILES['id_proof']['name'];
  $id_proof_path = $_FILES['id_proof']['tmp_name'];
  $rpp= $_REQUEST['hid_proof'];
  $status = $_REQUEST['status'];
  $id=$_REQUEST['ttId'];
  $action='updated';

 
    
    //rename file for id proof
    if ($_FILES["id_proof"]["name"] != "")
    {
      if(file_exists("deliveryboy_id/" . $id_proof)) {
        $i = 0;
        $PicFileName = $_FILES["id_proof"]["name"];
        $Arr1 = explode('.', $PicFileName);

        $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
        while (file_exists("deliveryboy_id/" . $PicFileName)) {
          $i++;
          $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
        }
      } 
      else {
        $PicFileName = $_FILES["id_proof"]["name"];
      }
      unlink("deliveryboy_id/".$rpp);  
    }
    else
    {
      $PicFileName=$rpp;
    }
    move_uploaded_file($id_proof_path,"deliveryboy_id/".$PicFileName);
 

  try
  {  
    
    $stmt = $obj->con1->prepare("update delivery_boy set  name=?,`email`=?,`contact`=?,`address`=?,`city`=?,`pincode`=?,`id_proof_type`=?,`id_proof`=?,`zone_id`=?,`status`=?,`action`=? where db_id=?");
	$stmt->bind_param("ssssisssissi", $name,$email,$contact,$address,$city,$pincode,$id_type,$PicFileName,$zone_id,$status,$action,$id);
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
    header("location:deliveryboy_reg.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
    header("location:deliveryboy_reg.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  $id_proof = $_REQUEST["id_proof"];

  try
  {
    $stmt_del = $obj->con1->prepare("delete from delivery_boy  where db_id='".$_REQUEST["n_id"]."'");
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
    if(file_exists("deliveryboy_id/".$id_proof)){
      unlink("deliveryboy_id/".$id_proof);
    }
  	setcookie("msg", "data_del",time()+3600,"/");
    header("location:deliveryboy_reg.php");
  }
  else
  {
	 setcookie("msg", "fail",time()+3600,"/");
    header("location:deliveryboy_reg.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">Delivery Boy Master</h4>

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
                      <h5 class="mb-0">Add Delivery Boy</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="ttId" id="ttId" />
                        <div class="row">
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Name</label>
                            <input type="text" id="name" name="name" class="form-control"  />
                          </div>
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Contact</label>
                            <input type="text" id="contact" name="contact" class="form-control" onkeyup ="check_deliboy_contact(this.value)" />
                            <div id="contact_alert_div" class="text-danger"></div>
                          </div>
                          
                        </div>
                        <div class="row">
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">E-mail</label>
                            <input type="email" id="email" name="email" class="form-control" onkeyup ="check_deliboy_email(this.value)" />
                            <div id="email_alert_div" class="text-danger"></div>
                          </div>
                          
                          <div class="col mb-3" id="pass_div">
                            <label for="nameWithTitle" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control"  />
                          </div>
                        </div>
                        <div class="row">
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Address</label>
                            <textarea  id="address" name="address" class="form-control"  ></textarea>
                          </div>
                          
                        </div>
                        <div class="row">
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">City</label>
                            <select class="form-control" name="city" id="city">
                              <option value="">Select City</option>
                              <?php 
                              while($city=mysqli_fetch_array($res_city))
                              {
                                ?>
                                <option value="<?php echo $city["city_id"]?>"><?php echo $city["city_name"]?></option>
                              <?php
                              }
                              ?>
                            </select>
                          </div>
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Zone</label>
                            <select class="form-control" name="zone" id="zone">
                              <option value="">Select Zone</option>
                              <?php 
                              while($zone=mysqli_fetch_array($res_zone))
                              {
                                ?>
                                <option value="<?php echo $zone["zid"]?>"><?php echo $zone["zone_name"]?></option>
                              <?php
                              }
                              ?>
                            </select>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Pincode</label>
                            <input type="text" id="pincode" name="pincode" class="form-control"  />
                          </div>
                          <div class="col mb-3">
                          </div>
                        </div>
                        <div class="row">
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Id Proof Type</label>
                            
                            <select class="form-control" name="id_type" id="id_type">
                              <option value="">Select Type</option>
                               <option value="pan_card">PAN card</option>
                              <option value="driving_license">Driving License</option>
                              <option value="aadhar_card">Aadhar Card</option>
                              
                            </select>
                          </div>
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Id Proof</label>
                            <input type="file" class="form-control" onchange="readURL(this)" name="id_proof" id="id_proof" required />
                            <img src="" name="PreviewImage" id="PreviewImage" width="100" height="100" style="display:none;">
                          <div id="imgdiv" style="color:red"></div>
                          <input type="hidden" name="hid_proof" id="hid_proof" />
                          </div>
                        </div>
                        <div class="row">
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Status</label>
                          
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="status" id="enable" value="enable" required checked>
                            <label class="form-check-label" for="inlineRadio1">Enable</label>
                          </div>
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="status" id="disable" value="disable" required>
                            <label class="form-check-label" for="inlineRadio1">Disable</label>
                          </div>
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
                <h5 class="card-header">Delivery Boy Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">

                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Name</th>
                        <th>E-mail</th>
                        <th>Contact</th>
                        <th>City</th>
                        <th>Pincode</th>
                       
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select  db.*,c1.city_name from delivery_boy db,city c1 where db.city=c1.city_id order by db_id desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($Delivery=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $Delivery["name"]?></td>
                        <td><?php echo $Delivery["email"]?></td>
                        <td><?php echo $Delivery["contact"]?></td>
                        <td><?php echo $Delivery["city_name"]?></td>
                        <td><?php echo $Delivery["pincode"]?></td>
                    <?php if($Delivery["status"]=='enable'){	?>
                        <td style="color:green"><?php echo $Delivery["status"]?></td>
                    <?php } else if($Delivery["status"]=='disable'){	?>
                        <td style="color:red"><?php echo $Delivery["status"]?></td>
                    <?php } ?>
                        <td>
                        
                        	<a href="javascript:editdata('<?php echo $Delivery["db_id"]?>','<?php echo base64_encode($Delivery["name"])?>','<?php echo base64_encode($Delivery["email"])?>','<?php echo base64_encode($Delivery["contact"])?>','<?php echo base64_encode($Delivery["address"])?>','<?php echo $Delivery["city"]?>','<?php echo $Delivery["pincode"]?>','<?php echo $Delivery["id_proof_type"]?>','<?php echo base64_encode($Delivery["id_proof"])?>','<?php echo $Delivery["zone_id"]?>','<?php echo $Delivery["status"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
							            <a  href="javascript:deletedata('<?php echo $Delivery["db_id"]?>','<?php echo base64_encode($Delivery["id_proof"])?>');"><i class="bx bx-trash me-1"></i> </a>
                        	<a href="javascript:viewdata('<?php echo $Delivery["db_id"]?>','<?php echo base64_encode($Delivery["name"])?>','<?php echo base64_encode($Delivery["email"])?>','<?php echo base64_encode($Delivery["contact"])?>','<?php echo base64_encode($Delivery["address"])?>','<?php echo $Delivery["city"]?>','<?php echo $Delivery["pincode"]?>','<?php echo $Delivery["id_proof_type"]?>','<?php echo base64_encode($Delivery["id_proof"])?>','<?php echo $Delivery["zone_id"]?>','<?php echo $Delivery["status"]?>');">View</i> </a>
                          
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

  function check_deliboy_contact(contact_no)
  {
    var id=$('#ttId').val();
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=check_deliboy_contact",
      data: "contact_no="+contact_no+"&id="+id,
      cache: false,
      success: function(result){
        if(result>0){
          $('#contact_alert_div').html('Contact Number already taken');
          document.getElementById('btnsubmit').disabled = true;
          document.getElementById('btnupdate').disabled = true;
          
        }
        else{
          $('#contact_alert_div').html('');
          document.getElementById('btnsubmit').disabled = false;
          document.getElementById('btnupdate').disabled = false;
          
        }
      }
    });
  }

  function check_deliboy_email(email_id)
  {
    var id=$('#ttId').val();
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=check_deliboy_email",
      data: "email_id="+email_id+"&id="+id,
      cache: false,
      success: function(result){
        if(result>0){
          $('#email_alert_div').html('Email ID already taken');
          document.getElementById('btnsubmit').disabled = true;
          document.getElementById('btnupdate').disabled = true;
        }
        else{
          $('#email_alert_div').html('');
          document.getElementById('btnsubmit').disabled = false;
          document.getElementById('btnupdate').disabled = false;
        }
      }
    });
  }


  function readURL(input) {
      if (input.files && input.files[0]) {
          var filename=input.files.item(0).name;

          var reader = new FileReader();
          var extn=filename.split(".");

           if(extn[1].toLowerCase()=="jpg" || extn[1].toLowerCase()=="jpeg" || extn[1].toLowerCase()=="png" || extn[1].toLowerCase()=="bmp") {
            reader.onload = function (e) {
                $('#PreviewImage').attr('src', e.target.result);
                  document.getElementById("PreviewImage").style.display = "block";
            };

            reader.readAsDataURL(input.files[0]);
            $('#imgdiv').html("");
            document.getElementById('btnsubmit').disabled = false;
      }
        else
        {
            $('#imgdiv').html("Please Select Image Only");
            document.getElementById('btnsubmit').disabled = true;
        }
    }
  }

  function deletedata(id,id_proof) {

      if(confirm("Are you sure to DELETE data?")) {
          var loc = "deliveryboy_reg.php?flg=del&n_id="+id+"&id_proof="+atob(id_proof);
          window.location = loc;
      }
  }
  function editdata(id,name,email,contact,address,city,pincode,id_type,id_proof,zone,status) {           
	   	$('#ttId').val(id);
      $('#name').val(atob(name));
      $('#email').val(atob(email));
      $('#contact').val(atob(contact));
      $('#address').val(atob(address));
      $('#city').val(city);
      $('#pincode').val(pincode);
			$('#id_type').val(id_type);
      //$('#id_proof').val(atob(id_proof));
      $('#hid_proof').val(atob(id_proof));
      $('#PreviewImage').show();
      $('#PreviewImage').attr('src','deliveryboy_id/'+atob(id_proof));   
      $('#id_proof').removeAttr('required');
      $('#zone').val(zone);
      $('#pass_div').hide();
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
  function viewdata(id,name,email,contact,address,city,pincode,id_type,id_proof,zone,status) {     
	   	$('#ttId').val(id);
      $('#name').val(atob(name));
      $('#email').val(atob(email));
      $('#contact').val(atob(contact));
      $('#address').val(atob(address));
      $('#city').val(city);
      $('#pincode').val(pincode);
      $('#id_type').val(id_type);
      //$('#id_proof').val(atob(id_proof));
      $('#hid_proof').val(atob(id_proof));
      $('#PreviewImage').show();
      $('#PreviewImage').attr('src','deliveryboy_id/'+atob(id_proof));   
      $('#id_proof').removeAttr('required');
      $('#zone').val(zone);
      $('#pass_div').hide();
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
      $('#btnupdate').attr('disabled',true);
    }
</script>
<?php 
include("footer.php");
?>