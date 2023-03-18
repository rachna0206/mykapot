<?php
  include("header.php");


$stmt_city_list = $obj->con1->prepare("select * from city order by city_id desc");
$stmt_city_list->execute();
$city_result = $stmt_city_list->get_result();
$stmt_city_list->close();

$stmt_zone_list = $obj->con1->prepare("select * from zone order by zid desc");
$stmt_zone_list->execute();
$zone_result = $stmt_zone_list->get_result();
$stmt_zone_list->close();

// insert data
if(isset($_REQUEST['btnsubmit']))
{
  
  $area_name = $_REQUEST['area_name'];
  $pincode = $_REQUEST['pincode'];
  $city = $_REQUEST['city_id'];
  $zone = $_REQUEST['zone_id'];
  $status = $_REQUEST['status'];

  try
  {
	$stmt = $obj->con1->prepare("INSERT INTO `area`(`area_name`,`pincode`,`city`,`zone`,`status`) VALUES (?,?,?,?,?)");
	$stmt->bind_param("ssiis",$area_name,$pincode,$city,$zone,$status);
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
      header("location:area.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:area.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  
  $area_name = $_REQUEST['area_name'];
  $pincode = $_REQUEST['pincode'];
  $city = $_REQUEST['city_id'];
  $zone = $_REQUEST['zone_id'];
  $status = $_REQUEST['status'];
  $id=$_REQUEST['ttId'];
  $action='updated';
  try
  {
    $stmt = $obj->con1->prepare("update area set area_name=?, pincode=?, city=?, zone=?, status=?, action=? where aid=?");
  	$stmt->bind_param("ssiissi", $area_name,$pincode,$city,$zone,$status,$action,$id);
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
      header("location:area.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:area.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from area where aid='".$_REQUEST["n_id"]."'");
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
    header("location:area.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:area.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">Area Master</h4>

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
                      <h5 class="mb-0">Add Area</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                       
                        <input type="hidden" name="ttId" id="ttId">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Area Name</label>
                          <input type="text" class="form-control" name="area_name" id="area_name" required />
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Pincode</label>
                          <input type="text" class="form-control" name="pincode" id="pincode" required />
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
                          <label class="form-label" for="basic-default-fullname">Zone Name</label>
                          <select name="zone_id" id="zone_id" class="form-control" required>
                            <option value="">Select Zone</option>
                    <?php    
                        while($zone_list=mysqli_fetch_array($zone_result)){
                    ?>
                        <option value="<?php echo $zone_list["zid"] ?>"><?php echo $zone_list["zone_name"] ?></option>
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
                <h5 class="card-header">City Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">

                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Area Name</th>
                        <th>Pincode</th>
                        <th>City</th>
                        <th>Zone</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select a1.*,c1.city_name,z1.zone_name from area a1, city c1, zone z1 where a1.city=c1.city_id and a1.zone=z1.zid order by aid desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($area=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $area["area_name"]?></td>
                        <td><?php echo $area["pincode"]?></td>
                        <td><?php echo $area["city_name"]?></td>
                        <td><?php echo $area["zone_name"]?></td>
                    <?php if($area["status"]=='enable'){	?>
                        <td style="color:green"><?php echo $area["status"]?></td>
                    <?php } else if($area["status"]=='disable'){	?>
                        <td style="color:red"><?php echo $area["status"]?></td>
                    <?php } ?>
                        <td>
                        	<a href="javascript:editdata('<?php echo $area["aid"]?>','<?php echo base64_encode($area["area_name"])?>','<?php echo $area["pincode"]?>','<?php echo $area["city"]?>','<?php echo $area["zone"]?>','<?php echo $area["status"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                          <a href="javascript:deletedata('<?php echo $area["aid"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        	<a href="javascript:viewdata('<?php echo $area["aid"]?>','<?php echo base64_encode($area["area_name"])?>','<?php echo $area["pincode"]?>','<?php echo $area["city"]?>','<?php echo $area["zone"]?>','<?php echo $area["status"]?>');">View</a>
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
          var loc = "area.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(id,aname,pincode,city,zone,status) {
         
     	$('#ttId').val(id);
			$('#area_name').val(atob(aname));
      $('#pincode').val(pincode);
      $('#city_id').val(city);
      $('#zone_id').val(zone);
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
  function viewdata(id,aname,pincode,city,zone,status) {
           
		  $('#ttId').val(id);
      $('#area_name').val(atob(aname));
      $('#pincode').val(pincode);
      $('#city_id').val(city);
      $('#zone_id').val(zone);
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