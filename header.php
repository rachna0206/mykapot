<?php 
//ob_start();
include ("db_connect.php");
$obj=new DB_connect();
date_default_timezone_set("Asia/Kolkata");
error_reporting(E_ALL);

session_start();


if(!isset($_SESSION["userlogin"]) )
{
    header("location:index.php");
}



$adminmenu=array("customer_reg.php","collection_time.php","delivery_settings.php","coupon.php","state.php","city.php","zone.php","area.php","post.php","mail_type.php","mail_type_tariff.php","deliveryboy_reg.php","coupon_counter.php");
$reportmenu=array("customer_report.php");
?>

<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Dashboard | MyKapot</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/kapot_favi.jpg" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- data tables -->
    <link rel="stylesheet" type="text/css" href="assets/vendor/DataTables/datatables.css">
     
    <!-- Row Group CSS -->
    <!-- <link rel="stylesheet" href="assets/vendor/datatables-rowgroup-bs5/rowgroup.bootstrap5.css"> -->
    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets/js/config.js"></script>
    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script type="text/javascript">
        function createCookie(name, value, days) {
          var expires;
          if (days) {
              var date = new Date();
              date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
              expires = "; expires=" + date.toGMTString();
          } else {
              expires = "";
          }
          document.cookie = (name) + "=" + String(value) + expires + ";path=/ ";

      }

      function readCookie(name) {
          var nameEQ = (name) + "=";
          var ca = document.cookie.split(';');
          for (var i = 0; i < ca.length; i++) {
              var c = ca[i];
              while (c.charAt(0) === ' ') c = c.substring(1, c.length);
              if (c.indexOf(nameEQ) === 0) return (c.substring(nameEQ.length, c.length));
          }
          return null;
      }

      function eraseCookie(name) {
          createCookie(name, "", -1);
      }
      function get_dashboard_data(date)
      {
        createCookie("dash_date", date, 1);
       
        document.getElementById("dashboard_frm").submit(); 
      }

      $(function() {
    // setInterval("get_notification()", 10000);

  });


 /* function get_notification() {

      $.ajax({
          async: true,
          url: 'ajaxdata.php?action=get_notification',
          type: 'POST',
          data: "",

          success: function (data) {
             // console.log(data);

              var resp=data.split("@@@@");
              $('#notification_list').html('');
              $('#notification_list').append(resp[0]);
              $('#notification_count').html('');
              $('#noti_count').html('');
              
              if(resp[1]>0) {
                  $('#notification_count').append(resp[1]);

                  $('#noti_count').append(resp[1]);
                  
                  playSound();
              }
              else
              {
                   $('#noti_count').append('');
                   $('#notification_list').hide();
              }
          }

      });
  }
  function removeNotification(id){

      $.ajax({
          async: true,
          type: "GET",
          url: "ajaxdata.php?action=removenotification",
          data:"id="+id,
          async: true,
          cache: false,
          timeout:50000,

          success: function(data){
              
              window.location = "lead_generation.php";
            

          }
      });
  }
  function playSound(){

      $.ajax({
          async: true,
          url: 'ajaxdata.php?action=get_Playnotification',
          type: 'POST',
          data: "",

          success: function (data) {
              // console.log(data);

              var resp=data.split("@@@@");

              if(resp[0]>0) {

                  var mp3Source = '<source src="notif_sound.wav" type="audio/mpeg">';
                  document.getElementById("sound").innerHTML='<audio autoplay="autoplay">' + mp3Source +  '</audio>';
                  removeplaysound(resp[1]);
              }
          }

      });

  }

  function removeplaysound(ids) {

      $.ajax({
          async: true,
          type: "GET",
          url: "ajaxdata.php?action=removeplaysound",
          data:"id="+ids,
          async: true,
          cache: false,
          timeout:50000,

      });

  }*/
    </script>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="#" class="app-brand-link">
              
              <span class="app-brand-text demo menu-text fw-bolder ms-2">MyKapot</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
              <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            <!-- Dashboard -->
            <li class="menu-item active">
              <a href="home.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
              </a>
            </li>


            <!-- Forms & Tables -->
            <!-- <li class="menu-header small text-uppercase"><span class="menu-header-text">Masters</span></li> -->
            <!-- Forms -->
            

            <li class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]),$adminmenu)?"active open":"" ?> ">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-detail"></i>
                <div data-i18n="Form Elements">Admin Controls</div>
              </a>
              <ul class="menu-sub">
              
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="customer_reg.php"?"active":"" ?>">
                <a href="customer_reg.php" class="menu-link">
                <div data-i18n="course">Customer Registration</div>
                </a>
              </li>

              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="collection_time.php"?"active":"" ?>">
                <a href="collection_time.php" class="menu-link">
                <div data-i18n="course">Collection Time</div>
                </a>
              </li>

              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="deliveryboy_reg.php"?"active":"" ?>">
                <a href="deliveryboy_reg.php" class="menu-link">
                <div data-i18n="course">Delivery Boy Registration</div>
                </a>
              </li>

              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="delivery_settings.php"?"active":"" ?>">
                <a href="delivery_settings.php" class="menu-link">
                <div data-i18n="course">Delivery Settings</div>
                </a>
              </li>

              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="coupon.php"?"active":"" ?>">
                <a href="coupon.php" class="menu-link">
                <div data-i18n="course">Coupon</div>
                </a>
              </li>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="coupon_counter.php"?"active":"" ?>">
                <a href="coupon_counter.php" class="menu-link">
                <div data-i18n="course">Coupon Counter</div>
                </a>
              </li>

              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="state.php"?"active":"" ?>">
                <a href="state.php" class="menu-link">
                <div data-i18n="course">State Master</div>
                </a>
              </li>
                     
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="city.php"?"active":"" ?>">
                <a href="city.php" class="menu-link">
                <div data-i18n="course">City Master</div>
                </a>
              </li>

              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="zone.php"?"active":"" ?>">
                <a href="zone.php" class="menu-link">
                <div data-i18n="course">Zone Master</div>
                </a>
              </li>

              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="area.php"?"active":"" ?>">
                <a href="area.php" class="menu-link">
                <div data-i18n="course">Area Master</div>
                </a>
              </li>
              
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="mail_type.php"?"active":"" ?>">
                <a href="mail_type.php" class="menu-link">
                <div data-i18n="course">Mail Type</div>
                </a>
              </li>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="mail_type_tariff.php"?"active":"" ?>">
                <a href="mail_type_tariff.php" class="menu-link">
                <div data-i18n="course">Mail Type Tariff</div>
                </a>
              </li>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="post.php"?"active":"" ?>">
                <a href="post.php" class="menu-link">
                <div data-i18n="course">Post Master</div>
                </a>
              </li>

              </ul>
            </li>

            <li class="menu-item  <?php echo in_array(basename($_SERVER["PHP_SELF"]),$reportmenu)?"active open":"" ?> ">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-detail"></i>
                <div data-i18n="Form Elements">Reports</div>
              </a>
              <ul class="menu-sub">
                
                
                <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="customer_report.php"?"active":"" ?>">
                  <a href="customer_report.php" class="menu-link">
                  <div data-i18n="course">Customer Report</div>
                  </a>
                </li>
                

              </ul>
            </li>
            
           
          </ul>
        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

          <nav
            class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
            id="layout-navbar"
          >
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
              <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                  <ul class="dropdown-menu ">
                    
                    <li>
                      <a class="dropdown-item" href="#">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle"><?php echo ucfirst($_SESSION["username"])?></span>
                      </a>
                    </li>
                   
                    
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="logout.php">
                        <i class="bx bx-power-off me-2"></i>
                        <span class="align-middle">Log Out</span>
                      </a>
                    </li>
                  </ul>
                  <!-- <a class="dropdown-item" href="#">
                        <span class="d-flex align-items-center align-middle">
                          <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                          
                          <span class="flex-shrink-0 badge badge-center rounded-pill bg-success w-px-20 h-px-20">4</span>
                        </span>
                      </a> -->
                </div>
              </div>

              <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- Place this tag where you want the button to render. -->
                

              
                <!-- <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar">
                     <i class="bx bx-bell"></i>
                     <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20" id="noti_count"></span>
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end" id="notification_list">
                  </ul>
                </li> -->
             
                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                      <img src="assets/img/kapot_favi.jpg" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    
                    <li>
                      <a class="dropdown-item" href="#">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle"><?php echo ucfirst($_SESSION["username"])?></span>
                      </a>
                    </li>
                   
                    
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="logout.php">
                        <i class="bx bx-power-off me-2"></i>
                        <span class="align-middle">Log Out</span>
                      </a>
                    </li>
                  </ul>
                </li>
              <!-- / User -->
              </ul>
            </div>
          </nav>
          <div id="sound"></div>
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">