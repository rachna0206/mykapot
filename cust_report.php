<?php
include("header.php");
error_reporting(E_ALL);


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  
  $name=isset($_REQUEST['name'])?$_REQUEST['name']:"";
  $contact=isset($_REQUEST['contact'])?$_REQUEST['contact']:"";
  $email=isset($_REQUEST['email'])?$_REQUEST['email']:"";
    
  $name_str=" name like '%".$name."%'";
  $contact_str=" and contact like '%".$contact."%'";
  $email_str=" and email like '%".$email."%'";
  
  $stmt_list = $obj->con1->prepare("SELECT * from customer_reg c1 where ".$name_str.$contact_str.$email_str);
  $stmt_list->execute();
  $result = $stmt_list->get_result();
  
  $stmt_list->close();

}

?>

<h4 class="fw-bold py-3 mb-4">Customer Report</h4>

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
           
          </div>

          <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Submit</button>
        
          <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location='cust_report.php'">Cancel</button>

        </form>
      </div>
    </div>
  </div>
</div>

<!-- Basic Bootstrap Table -->
              <div class="card">
                <h5 class="card-header">Customer Records</h5>
               
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">
                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Name</th>
                        <th>Contact No.</th>
                        <th>Email</th>
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