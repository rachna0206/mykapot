<?php
  include("header.php");

  $deli_boy_id = $_COOKIE['deli_boy_id'];

//customer qry
$stmt = $obj->con1->prepare("select  db.*,c1.city_name,z1.zone_name from delivery_boy db, city c1, zone z1 where db.city=c1.city_id and db.zone_id=z1.zid and db.db_id=?");
$stmt->bind_param("i",$deli_boy_id);
$stmt->execute();
$result_deli_boy = $stmt->get_result();
$stmt->close();

$deli_boy_data=mysqli_fetch_array($result_deli_boy);

/*
$stmt_address= $obj->con1->prepare("select ca.*,c1.city_name,a1.area_name from customer_address ca, area a1,city c1 where ca.area_id=a1.aid and ca.city_id=c1.city_id and cust_id=?");
$stmt_address->bind_param("i",$customer_id);
$stmt_address->execute();
$res_address  = $stmt_address->get_result();
$stmt_address->close();
*/
?>

<h4 class="fw-bold py-3 mb-4">Delivery Boy Report</h4>

                <!-- Basic Layout -->
                <div class="row">
                  <div class="col-xl">
                    <div class="card mb-4">
                    <!--  <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Add post</h5>
                        
                      </div>  -->
                      <div class="card-body">
                        <form method="post" >
                          <input type="hidden" name="ttId" id="ttId">
                          
                        <div class="row">
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo $deli_boy_data['name'] ?>" readonly />
                          </div>
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Contact</label>
                            <input type="text" id="contact" name="contact" class="form-control" value="<?php echo $deli_boy_data['contact'] ?>" readonly />
                          </div>
                          
                        </div>
                        <div class="row">
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">E-mail</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo $deli_boy_data['email'] ?>" readonly />
                          </div>
                          <div class="col mb-3"></div>
                        </div>
                        <div class="row">
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Address</label>
                            <textarea  id="address" name="address" class="form-control" readonly ><?php echo $deli_boy_data['address'] ?></textarea>
                          </div>
                          
                        </div>
                        <div class="row">
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">City</label>
                            <input type="text" id="city" name="city" class="form-control" value="<?php echo $deli_boy_data['city_name'] ?>" readonly />
                          </div>
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Zone</label>
                            <input type="text" id="zone" name="zone" class="form-control" value="<?php echo $deli_boy_data['zone_name'] ?>" readonly />
                          </div>
                        </div>
                        <div class="row">
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Pincode</label>
                            <input type="text" id="pincode" name="pincode" class="form-control" value="<?php echo $deli_boy_data['pincode'] ?>" readonly />
                          </div>
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Profile Pic/Selfie</label><div>
                             <img src="deliveryboy_id/<?php echo $deli_boy_data['profile_pic'] ?>" name="pro_pic" id="pro_pic" width="100" height="100">
                          </div>
                        </div>
                        <div class="row">
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Id Proof Type</label>
                            <input type="text" id="id_type" name="id_type" class="form-control" value="<?php echo $deli_boy_data['id_proof_type'] ?>" readonly />
                          </div>
                          <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Id Proof</label><div>
                            <img src="deliveryboy_id/<?php echo $deli_boy_data['id_proof'] ?>" name="id_proof" id="id_proof" width="100" height="100">
                          </div>
                        </div>
                        <div class="row">
                          <div class="col mb-3">
                            <label class="form-label d-block" for="basic-default-fullname">Status</label>
                          
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="status" id="enable" value="enable" required disabled <?php if($deli_boy_data['status']=="enable"){ ?> checked <?php } ?> >
                            <label class="form-check-label" for="inlineRadio1">Enable</label>
                          </div>
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="status" id="disable" value="disable" required disabled <?php if($deli_boy_data['status']=="disable"){ ?> checked <?php } ?> >
                            <label class="form-check-label" for="inlineRadio1">Disable</label>
                          </div>
                          </div>
                        </div>

                        </form>
                      </div>
                    </div>
                  </div>
                  
                </div>

            <!-- / Content -->
<script type="text/javascript">
</script>
<?php 
  include("footer.php");
?>