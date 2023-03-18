<?php
include("header.php");
error_reporting(E_ALL);


$stmt_zone = $obj->con1->prepare("select * from zone");
$stmt_zone->execute();
$res_zone = $stmt_zone->get_result();
$stmt_zone->close();

// insert data
if(isset($_REQUEST['btnsubmit']))
{
  
  $name=isset($_REQUEST['name'])?$_REQUEST['name']:"";
  $contact=isset($_REQUEST['contact'])?$_REQUEST['contact']:"";
  $email=isset($_REQUEST['email'])?$_REQUEST['email']:"";
  $zone=isset($_REQUEST['zone'])?$_REQUEST['zone']:"";
    
  $name_str=($name!="")?"and db.name like '%".$name."%'":"";
  $contact_str=($contact!="")?"and db.contact like '%".$contact."%'":"";
  $email_str=($email!="")?"and db.email like '%".$email."%'":"";
  $zone_str=($zone!="")?"and db.zone_id='".$zone."'":"";
  
  $stmt_list = $obj->con1->prepare("select  db.*,c1.city_name,z1.zone_name from delivery_boy db,city c1,zone z1 where db.city=c1.city_id and db.zone_id=z1.zid ".$name_str.$contact_str.$email_str.$zone_str);
  $stmt_list->execute();
  $result = $stmt_list->get_result();
  $stmt_list->close();

}

?>

<h4 class="fw-bold py-3 mb-4">Delivery Boy Report</h4>

<!-- Basic Layout -->
<div class="row">
  <div class="col-xl">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
          
      </div>
      <div class="card-body">
        <form method="post" enctype="multipart/form-data">
          <div class="row">
            
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Name</label>
              <input type="text" class="form-control" name="name" id="name" value="<?php echo isset($_REQUEST['name'])?$_REQUEST['name']:""?>"  />
            </div>
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Contact</label>
              <input type="text" class="form-control" name="contact" id="contact" value="<?php echo isset($_REQUEST['contact'])?$_REQUEST['contact']:""?>"/>
            </div>
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Email</label>
              <input type="text" class="form-control" name="email" id="email" value="<?php echo isset($_REQUEST['email'])?$_REQUEST['email']:""?>"/>
            </div>
            <div class="mb-3 col-md-3">
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

          <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Submit</button>
        
          <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location='delivery_boy_report.php'">Cancel</button>

        </form>
      </div>
    </div>
  </div>
</div>

<!-- Basic Bootstrap Table -->
              <div class="card">
                <h5 class="card-header">Delivery Boy Records</h5>
               
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">
                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Name</th>
                        <th>Contact No.</th>
                        <th>Email</th>
                        <th>City</th>
                        <th>Zone</th>
                        <th>Action</th>
                        
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0" id="grid">
                      <?php 
                     if(isset($_REQUEST['btnsubmit']) )
                      {                     
                        $i=1;
                        while($row=mysqli_fetch_array($result))
                        {                          
                       ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $row["name"]?></td>
                        <td><?php echo $row["contact"]?></td>
                        <td><?php echo $row["email"]?></td>
                        <td><?php echo $row["city_name"]?></td>
                        <td><?php echo $row["zone_name"]?></td>
                        <td ><a href="#">View</a></td>                          
                    <?php 
                    $i++;
                  } ?>
                      </tr>
                      <?php
                          
                        }
                        
                      ?>
                      
                    </tbody>
                  </table>
                </div>
              </div>

              <!--/ Basic Bootstrap Table -->


<?php 
	include("footer.php");
?>