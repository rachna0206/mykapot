<?php
  include("header.php");

  $customer_id = $_COOKIE['cust_id'];

//customer qry
$stmt_cust = $obj->con1->prepare("select * from customer_reg where id=?");
$stmt_cust->bind_param("i",$customer_id);
$stmt_cust->execute();
$result_cust = $stmt_cust->get_result();
$stmt_cust->close();

$cust_data=mysqli_fetch_array($result_cust);


$stmt_address= $obj->con1->prepare("select ca.*,c1.city_name,a1.area_name from customer_address ca, area a1,city c1 where ca.area_id=a1.aid and ca.city_id=c1.city_id and cust_id=?");
$stmt_address->bind_param("i",$customer_id);
$stmt_address->execute();
$res_address  = $stmt_address->get_result();
$stmt_address->close();

?>

<h4 class="fw-bold py-3 mb-4">Customer Report</h4>

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
                          
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Name</label>
                          <input type="text" class="form-control" name="name" id="name" value="<?php echo $cust_data['name'] ?>" readonly required />
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Email</label>
                          <input type="text" class="form-control" name="email" id="email" value="<?php echo $cust_data['email'] ?>" readonly required />
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Contact No.</label>
                          <input type="tel" pattern="[0-9]{10}" class="form-control phone-mask" id="contact" name="contact" value="<?php echo $cust_data['contact'] ?>" readonly required/>
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label d-block" for="basic-default-fullname">Status</label>
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="status" id="enable" value="enable" disabled <?php if($cust_data['status']=="enable"){ ?> checked <?php } ?> >
                            <label class="form-check-label" for="inlineRadio1">Enable</label>
                          </div>
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="status" id="disable" value="disable" disabled <?php if($cust_data['status']=="disable"){ ?> checked <?php } ?> >
                            <label class="form-check-label" for="inlineRadio1">Disable</label>
                          </div>
                        </div>

                        <?php 
                          while($cust_addr = mysqli_fetch_array($res_address)){
                        ?>
                            <div class="mb-3">
                              <label class="form-label" for="basic-default-fullname">Address</label>
                              <input type="text" class="form-control" name="addr" id="addr" value="<?php echo $cust_addr["address_label"]."-".$cust_addr["house_no"].",".$cust_addr["street"].",".$cust_addr["area_name"].",".$cust_addr["city_name"] ?>" readonly required />
                            </div>
                        <?php
                          }
                        ?>

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