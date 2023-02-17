<?php
  include("header.php");

$stmt_city_list = $obj->con1->prepare("select * from city where status='enable' and LOWER(city_name)='surat' order by city_id desc");
$stmt_city_list->execute();
$city_result = $stmt_city_list->get_result();
$stmt_city_list->close();

$cid = $_COOKIE['cid'];

$stmt_cust = $obj->con1->prepare("select name from customer_reg where id=?");
$stmt_cust->bind_param("i",$cid);
$stmt_cust->execute();
$cust_result = $stmt_cust->get_result();
$stmt_cust->close();
$cust = mysqli_fetch_array($cust_result);

// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $addr_label = $_REQUEST['addr_label'];
  $house = $_REQUEST['house'];
  $street = $_REQUEST['street'];
  $city_id = $_REQUEST['city_id'];
  $area_id = $_REQUEST['area_id'];
  $pincode = $_REQUEST['pincode'];

  try
  {
	$stmt = $obj->con1->prepare("INSERT INTO `customer_address`(`cust_id`,`address_label`,`house_no`,`street`,`area_id`,`city_id`,`pincode`) VALUES (?,?,?,?,?,?,?)");
	$stmt->bind_param("isssiis",$cid,$addr_label,$house,$street,$area_id,$city_id,$pincode);
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
      header("location:customer_address.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:customer_address.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  $addr_label = $_REQUEST['addr_label'];
  $house = $_REQUEST['house'];
  $street = $_REQUEST['street'];
  $city_id = $_REQUEST['city_id'];
  $area_id = $_REQUEST['area_id'];
  $pincode = $_REQUEST['pincode'];
  $id=$_REQUEST['ttId'];
  $action='updated';
  try
  {
    $stmt = $obj->con1->prepare("update customer_address set address_label=?, house_no=?, street=?, area_id=?, city_id=?, pincode=?, action=? where ca_id=?");
  	$stmt->bind_param("sssiissi", $addr_label,$house,$street,$area_id,$city_id,$pincode,$action,$id);
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
      header("location:customer_address.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:customer_address.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from customer_address where ca_id='".$_REQUEST["n_id"]."'");
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
    header("location:customer_address.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:customer_address.php");
  }
}

?>

<script type="text/javascript">

  function areaList(city){
    $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=areaList",
          data: "city_id="+city,
          cache: false,
          success: function(result){
            $('#area_id').html('');
            $('#area_id').append(result);
          }
        });
  }

</script>

<h4 class="fw-bold py-3 mb-4"><?php echo $cust['name'] ?>'s Address</h4>

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
                      <h5 class="mb-0">Add Address</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                       
                        <input type="hidden" name="ttId" id="ttId">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Address Label</label>
                          <input type="text" class="form-control" name="addr_label" id="addr_label" required />
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">House No. and Society Name</label>
                          <input type="text" class="form-control" name="house" id="house" required />
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Street</label>
                          <input type="text" class="form-control" name="street" id="street" required/>
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">City</label>
                          <select name="city_id" id="city_id" onchange="areaList(this.value)" class="form-control" required>
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
                          <label class="form-label" for="basic-default-company">Area</label>
                          <select name="area_id" id="area_id" class="form-control" required>
                            <option value="">Select Area</option>
                          </select>
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Pincode</label>
                          <input type="text" class="form-control" name="pincode" id="pincode" required/>
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
                <h5 class="card-header">Customer Address Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">

                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Adress Label</th>
                        <th>House No. & Soc.</th>
                        <th>Street</th>
                        <th>City</th>
                        <th>Area</th>
                        <th>Pincode</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select ca1.*,c1.city_name,a1.area_name from customer_address ca1, city c1, area a1 where ca1.city_id=c1.city_id and ca1.area_id=a1.aid and ca1.cust_id=? order by ca_id desc");
                        $stmt_list->bind_param("i",$cid);
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($addr=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $addr["address_label"]?></td>
                        <td><?php echo $addr["house_no"]?></td>
                        <td><?php echo $addr["street"]?></td>
                        <td><?php echo $addr["city_name"]?></td>
                        <td><?php echo $addr["area_name"]?></td>
                        <td><?php echo $addr["pincode"]?></td>
                        <td>
                        	<a href="javascript:editdata('<?php echo $addr["ca_id"]?>','<?php echo base64_encode($addr["address_label"])?>','<?php echo base64_encode($addr["house_no"])?>','<?php echo base64_encode($addr["street"])?>','<?php echo $addr["city_id"]?>','<?php echo $addr["area_id"]?>','<?php echo base64_encode($addr["pincode"])?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                          <a href="javascript:deletedata('<?php echo $addr["ca_id"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        	<a href="javascript:viewdata('<?php echo $addr["ca_id"]?>','<?php echo base64_encode($addr["address_label"])?>','<?php echo base64_encode($addr["house_no"])?>','<?php echo base64_encode($addr["street"])?>','<?php echo $addr["city_id"]?>','<?php echo $addr["area_id"]?>','<?php echo base64_encode($addr["pincode"])?>');">View</a>
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
          var loc = "customer_address.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(id,label,house,street,city,area,pincode) {

     	$('#ttId').val(id);
			$('#addr_label').val(atob(label));
      $('#house').val(atob(house));
      $('#street').val(atob(street));
      $('#city_id').val(city);
      areaList(city);
      setTimeout(function() {
          $('#area_id').val(area);
      }, 100);
      $('#pincode').val(atob(pincode));
			
			$('#btnsubmit').attr('hidden',true);
      $('#btnupdate').removeAttr('hidden');
			$('#btnsubmit').attr('disabled',true);
  }
  function viewdata(id,label,house,street,city,area,pincode) {
           
		  $('#ttId').val(id);
      $('#addr_label').val(atob(label));
      $('#house').val(atob(house));
      $('#street').val(atob(street));
      $('#city_id').val(city);
      areaList(city);
      setTimeout(function() {
          $('#area_id').val(area);
      }, 100);
      $('#pincode').val(atob(pincode));
			
			$('#btnsubmit').attr('hidden',true);
      $('#btnupdate').attr('hidden',true);
			$('#btnsubmit').attr('disabled',true);
      $('#btnupdate').attr('disabled',true);

  }
</script>
<?php 
  include("footer.php");
?>