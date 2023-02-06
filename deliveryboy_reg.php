<?php
include("header.php");
error_reporting(E_ALL);
$stmt_city = $obj->con1->prepare("select * from city");
$stmt_city->execute();
$res_city = $stmt_city->get_result();
$stmt_city->close();

// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $name = $_REQUEST['name'];
  $email = $_REQUEST['email'];
  $pass = $_REQUEST['password'];
  $contact = $_REQUEST['contact'];
  $address = $_REQUEST['address'];
  $pincode = $_REQUEST['pincode'];


  $city = $_REQUEST['city'];
  $id_type=$_REQUEST['id_type'];
  $id_proof="";
  $status = $_REQUEST['status'];
  $action='added';
  $zone_id="1";
  try
  {
    
	$stmt = $obj->con1->prepare("INSERT INTO `delivery_boy`( `name`, `email`, `password`, `contact`, `addess`, `city`, `pincode`, `id_proof_type`, `id_proof`,`zone_id`, `status`, `action`) values (?,?,?,?,?,?,?,?,?,?,?,?)");
	$stmt->bind_param("sssssisssiss",$name,$email,$pass,$contact,$address,$city,$pincode,$id_type,$id_proof,$zone_id,$status,$action);
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
  $id_type=$_REQUEST['id_type'];
  $id_proof="";
  $status = $_REQUEST['status'];
  
  $zone_id="1";
  $id=$_REQUEST['ttId'];
  $action='updated';
  try
  {
    
    $stmt = $obj->con1->prepare("update delivery_boy set  name=?,`email`=?,`contact`=?,`address`=?,`city`=?,`pincode`=?,`id_proof_type`=?,`id_proof`=?,`zone_id`=?,`status`=?,`action`=? where db_id=?");
	$stmt->bind_param("ssssisssissi", $name,$email,$contact,$address,$city,$pincode,$id_type,$id_proof,$zone_id,$status,$action,$id);
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
  try
  {
    $stmt_del = $obj->con1->prepare("delete from  delivery_boy  where db_id='".$_REQUEST["n_id"]."'");
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
                      <form method="post" >
                        <input type="hidden" name="ttId" id="ttId" />
                        <div class="row">
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Name</label>
                            <input type="text" id="name" name="name" class="form-control"  />
                          </div>
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Contact</label>
                            <input type="text" id="contact" name="contact" class="form-control"  />
                          </div>
                          
                        </div>
                        <div class="row">
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">E-mail</label>
                            <input type="email" id="email" name="email" class="form-control"  />
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
                            <label for="nameWithTitle" class="form-label">Pincode</label>
                            <input type="text" id="pincode" name="pincode" class="form-control"  />
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
                            <input type="file" id="id_proof" name="id_proof" class="form-control"  />
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
                        <td><?php echo $Delivery["status"]?></td>
                        
                    
                        <td>
                        
                        	<a href="javascript:editdata('<?php echo $Delivery["db_id"]?>','<?php echo $Delivery["name"]?>','<?php echo $Delivery["email"]?>','<?php echo $Delivery["contact"]?>','<?php echo base64_encode($Delivery["address"])?>','<?php echo $Delivery["city"]?>','<?php echo $Delivery["pincode"]?>','<?php echo $Delivery["status"]?>','<?php echo $Delivery["id_proof_type"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                        
							<a  href="javascript:deletedata('<?php echo $Delivery["db_id"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        
                        	<a href="javascript:viewdata('<?php echo $Delivery["db_id"]?>','<?php echo $Delivery["name"]?>','<?php echo $Delivery["email"]?>','<?php echo $Delivery["contact"]?>','<?php echo base64_encode($Delivery["address"])?>','<?php echo $Delivery["city"]?>','<?php echo $Delivery["pincode"]?>','<?php echo $Delivery["status"]?>','<?php echo $Delivery["id_proof_type"]?>');">View</a>
                        
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
  function deletedata(id) {

      if(confirm("Are you sure to DELETE data?")) {
          var loc = "deliveryboy_reg.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(id,name,email,contact,address,city,pincode,status,id_type) {
           
	   	$('#ttId').val(id);
      $('#name').val(name);
      $('#email').val(email);
      $('#contact').val(contact);
      $('#pincode').val(pincode);
      $('#city').val(city);
			$('#address').val(atob(address));
      $('#id_type').val(id_type);
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
  function viewdata(id,name,email,contact,address,city,pincode,status,id_type) {
           
	   	$('#ttId').val(id);
      $('#name').val(name);
      $('#email').val(email);
      $('#contact').val(contact);
      $('#pincode').val(pincode);
      $('#city').val(city);
      $('#address').val(atob(address));
      $('#id_type').val(id_type);
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

        }
</script>
<?php 
include("footer.php");
?>