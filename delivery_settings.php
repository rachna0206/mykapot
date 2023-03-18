<?php
  include("header.php");

$stmt_city_list = $obj->con1->prepare("select * from city where status='enable' order by city_id desc");
$stmt_city_list->execute();
$city_result = $stmt_city_list->get_result();
$stmt_city_list->close();

// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $min_km = $_REQUEST['min_km'];
  $min_charge = $_REQUEST['min_charge'];
  $km_charge = $_REQUEST['km_charge'];
  $max_radius = $_REQUEST['max_radius'];
  $deli_percen = $_REQUEST['deli_percen'];
  $city_id = $_REQUEST['city_id'];
  $status = $_REQUEST['status'];

  try
  {
	$stmt = $obj->con1->prepare("INSERT INTO `delivery_settings`(`minimum_delivery_kilometer`,`minimum_delivery_charge`,`per_kilometer_charges`,`max_radius`,`delivery_percentage`,`city`,`status`) VALUES (?,?,?,?,?,?,?)");
	$stmt->bind_param("sssssis",$min_km,$min_charge,$km_charge,$max_radius,$deli_percen,$city_id,$status);
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
      header("location:delivery_settings.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:delivery_settings.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  $min_km = $_REQUEST['min_km'];
  $min_charge = $_REQUEST['min_charge'];
  $km_charge = $_REQUEST['km_charge'];
  $max_radius = $_REQUEST['max_radius'];
  $deli_percen = $_REQUEST['deli_percen'];
  $city_id = $_REQUEST['city_id'];
  $status = $_REQUEST['status'];
  $id=$_REQUEST['ttId'];
  $action='updated';
  try
  {
    $stmt = $obj->con1->prepare("update delivery_settings set minimum_delivery_kilometer=?, minimum_delivery_charge=?, per_kilometer_charges=?, max_radius=?, delivery_percentage=?, city=?, status=?, action=? where id=?");
  	$stmt->bind_param("sssssissi",$min_km,$min_charge,$km_charge,$max_radius,$deli_percen,$city_id,$status,$action,$id);
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
      header("location:delivery_settings.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:delivery_settings.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from delivery_settings where id='".$_REQUEST["n_id"]."'");
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
    header("location:delivery_settings.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:delivery_settings.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">Delivery Settings Master</h4>

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
                      <h5 class="mb-0">Add Delivery Settings</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                       
                        <input type="hidden" name="ttId" id="ttId">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Minimum Delivery Kilometer</label>
                          <input type="text" class="form-control" name="min_km" id="min_km" required />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Minimum Delivery Charge</label>
                          <input type="text" class="form-control" name="min_charge" id="min_charge" required />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Per Kilometer Delivery Charge</label>
                          <input type="text" class="form-control" name="km_charge" id="km_charge" required />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Maximum Radius</label>
                          <input type="text" class="form-control" name="max_radius" id="max_radius" required />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Delivery Percentage - Admin Commission</label>
                          <input type="text" class="form-control" name="deli_percen" id="deli_percen" required />
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">City Name</label>
                          <select name="city_id" id="city_id" class="form-control" required>
                            <option value="">Select City</option>
                    <?php    
                        while($city_list=mysqli_fetch_array($city_result)){
                    ?>
                        <option value="<?php echo $city_list["city_id"] ?>"><?php echo $city_list["city_name"] ?></option>
                    <?php
                      }
                    ?>
                          </select>
                        </div>
                        
                        <div class="mb-3">
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
                <h5 class="card-header">Delivery Settings Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">

                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Min Delivery KM</th>
                        <th>Min Delivery Charge</th>
                        <th>Per KM Delivery Charges</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select ds1.*, c1.city_name from delivery_settings ds1, city c1 where ds1.city=c1.city_id order by id desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($delivery=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $delivery["minimum_delivery_kilometer"]?></td>
                        <td><?php echo $delivery["minimum_delivery_charge"]?></td>
                        <td><?php echo $delivery["per_kilometer_charges"]?></td>
                    <?php if($delivery["status"]=='enable'){	?>
                        <td style="color:green"><?php echo $delivery["status"]?></td>
                    <?php } else if($delivery["status"]=='disable'){	?>
                        <td style="color:red"><?php echo $delivery["status"]?></td>
                    <?php } ?>
                        <td>
                          <a href="javascript:editdata('<?php echo $delivery["id"]?>','<?php echo $delivery["minimum_delivery_kilometer"]?>','<?php echo $delivery["minimum_delivery_charge"]?>','<?php echo $delivery["per_kilometer_charges"]?>','<?php echo $delivery["max_radius"]?>','<?php echo $delivery["delivery_percentage"]?>','<?php echo $delivery["city"]?>','<?php echo $delivery["status"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                          <a href="javascript:deletedata('<?php echo $delivery["id"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        	<a href="javascript:viewdata('<?php echo $delivery["id"]?>','<?php echo $delivery["minimum_delivery_kilometer"]?>','<?php echo $delivery["minimum_delivery_charge"]?>','<?php echo $delivery["per_kilometer_charges"]?>','<?php echo $delivery["max_radius"]?>','<?php echo $delivery["delivery_percentage"]?>','<?php echo $delivery["city"]?>','<?php echo $delivery["status"]?>');">View</a>
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
          var loc = "delivery_settings.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(id,min_km,min_charge,km_charge,radius,deli_per,city,status) {
     	$('#ttId').val(id);
			$('#min_km').val(min_km);
      $('#min_charge').val(min_charge);
      $('#km_charge').val(km_charge);
      $('#max_radius').val(radius);
      $('#deli_percen').val(deli_per);
      $('#city_id').val(city);
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
  function viewdata(id,min_km,min_charge,km_charge,radius,deli_per,city,status) {
           
		  $('#ttId').val(id);
      $('#min_km').val(min_km);
      $('#min_charge').val(min_charge);
      $('#km_charge').val(km_charge);
      $('#max_radius').val(radius);
      $('#deli_percen').val(deli_per);
      $('#city_id').val(city);
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