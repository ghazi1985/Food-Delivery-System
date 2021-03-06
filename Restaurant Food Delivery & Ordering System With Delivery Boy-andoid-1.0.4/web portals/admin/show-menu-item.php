<?php require_once("config.php"); ?>
<?php if(isset($_SESSION['id'])) { ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php require_once("meta.php"); ?>

	<link rel="stylesheet" href="assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css">
	<link rel="stylesheet" href="assets/css/font-icons/entypo/css/entypo.css">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
	<link rel="stylesheet" href="assets/css/bootstrap.css">
	<link rel="stylesheet" href="assets/css/neon-core.css">
	<link rel="stylesheet" href="assets/css/neon-theme.css">
	<link rel="stylesheet" href="assets/css/neon-forms.css">
	<link rel="stylesheet" href="assets/css/custom.css">

	<script src="assets/js/jquery-1.11.0.min.js"></script>

	<!--[if lt IE 9]><script src="assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
	
	
</head>
<body class="page-body" data-url="http://neon.dev">

<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->	
	
	<?php require_once('left-sidebar.php'); ?>			
	<div class="main-content">
	<?php require_once('topbar.php'); ?>		
	
	<div class="row">
        <div class="col-md-12">
        
        <div class="pull-left"><h2 class="toast-title">View all Menu Items</h2></div>
        <div class="pull-right"></div>
        <div class="clearfix"></div>
        <br>
        <?php 
        if(isset($_GET['success'])) {
            ?>
            <div class='alert alert-success'>Successfully submitted..</div>
            <?php
        }
        if(isset($_GET['error'])) {
            ?>
            <div class='alert alert-danger'>Some problem occured, try again later..</div>
            <?php
        }

        //delete
        if( isset($_GET['del']) && !empty($_GET['item']) ) {
            if($_GET['del']=="ok"){

                $item = $_GET['item'];

                $headers = array(
                    "Accept: application/json",
                    "Content-Type: application/json",
                );

                $data = array(
                    "menu_item_id" => $item
                );

                $ch = curl_init( $baseurl.'/deleteMenuItem' );

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $return = curl_exec($ch);

                $json_data = json_decode($return, true);
                //var_dump($json_data);
                
                $curl_error = curl_error($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if($json_data['code'] !== 200){
                    //echo "<div class='alert alert-danger'>".$json_data['msg']."</div>";
                    @header("Location: show-menu-item.php?id=".$_GET['id']);
                    echo "<script>window.location='show-menu-item.php?id=".$_GET['id']."'</script>";

                } else {
                    //echo "<div class='alert alert-success'>".$json_data['msg']."</div>";
                    @header("Location: show-menu-item.php?id=".$_GET['id']."");
                    echo "<script>window.location='show-menu-item.php?id=".$_GET['id']."'</script>";
                    
                }

                curl_close($ch);

            }
        } //delete = end
        ?>
         <table class="table table-bordered datatable" id="table-1">
            <thead>
                <tr>
                    <th>Menu name</th>
                    <th>Menu description</th>
                    <th>Menu item name</th>
                    <th>Menu item description</th>
                    <th>Menu item price</th>
                    <th>Menu item created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if( isset($_GET['id']) ) {
                    $restaurant_menu_id = $_GET['id'];
                } else {
                    $restaurant_menu_id = "";
                }
                $headers = array(
                    "Accept: application/json",
                    "Content-Type: application/json"
                   );
                   $data = array(
                    "restaurant_menu_id" => $restaurant_menu_id
                    );

                   $ch = curl_init( $baseurl.'/showMenuItems' );

                   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                   $result = curl_exec($ch);

                   $curl_error = curl_error($ch);
                   $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                   $json_data = json_decode($result, true);
                    //var_dump($json_data);
                    $i=0;
                    foreach($json_data['msg'] as $str => $data) {
                        //var_dump($data);
                        if(!empty($data['RestaurantMenuItem']['id'])) {
                            echo "<tr>
                                <td>".$data['RestaurantMenu']['name']."</td>
                                <td>".$data['RestaurantMenu']['description']."</td>
                                <td>".$data['RestaurantMenuItem']['name']."</td>
                                <td>".$data['RestaurantMenuItem']['description']."</td>
                                <td>".$data['RestaurantMenuItem']['price']."</td>
                                <td>".$data['RestaurantMenuItem']['created']."</td>
                                <td>
                                    <a href='javascript:;' onClick='addmenu_extraitem(".$data['RestaurantMenuItem']['id'].")' class='btn btn-default btn-sm' style='margin-bottom:5px;'>Add menu extra items</a>
                                    <a href='javascript:;' onClick='showmenu_extraitem(".$data['RestaurantMenuItem']['id'].")' class='btn btn-default btn-sm'>Show menu extra items</a>
                                    <a href='show-menu-item.php?id=".$_GET['id']."&del=ok&item=".$data['RestaurantMenuItem']['id']."' class='btn btn-default btn-sm'>Delete menu item</a>
                                </td>
                            </tr>";
                        }
                        $i++;
                    }

                   curl_close($ch);

                   // do some checking to make sure it sent
                   //var_dump($return);
                   //die;

                ?>
            </tbody>
        </table>
        
        <script type="text/javascript">
        var responsiveHelper;
        var breakpointDefinition = {
            tablet: 1024,
            phone : 480
        };
        var tableContainer;
        
            jQuery(document).ready(function($)
            {
                tableContainer = $("#table-1");
                
                tableContainer.dataTable({
                    "sPaginationType": "bootstrap",
                    "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    "bStateSave": true,
                    
        
                    // Responsive Settings
                    bAutoWidth     : false,
                    fnPreDrawCallback: function () {
                        // Initialize the responsive datatables helper once.
                        if (!responsiveHelper) {
                            responsiveHelper = new ResponsiveDatatablesHelper(tableContainer, breakpointDefinition);
                        }
                    },
                    fnRowCallback  : function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                        responsiveHelper.createExpandIcon(nRow);
                    },
                    fnDrawCallback : function (oSettings) {
                        responsiveHelper.respond();
                    }
                });
                
                $(".dataTables_wrapper select").select2({
                    minimumResultsForSearch: -1
                });
            });
        </script>
         
            
        </div>
    </div>    

<script type="text/javascript">
function addmenu_extraitem(menu_item_id) {
    //console.log(menu_item_id);
    jQuery("input[id='restaurant_menu_item_id']").val(menu_item_id);
   jQuery('#modal-7').modal('show', {backdrop: 'static'});
}
function showmenu_extraitem(menu_item_id) {
    //console.log(menu_item_id);
   jQuery('#modal-8').modal('show', {backdrop: 'static'});

   $.ajax({
        type: "GET",
        url: "getextra_menuitems.php",
        data: {
            'menu_item_id' : menu_item_id
        },
        success: function(response) {
            jQuery('#modal-8 .modal-body').html(response);
        }
    });
}
</script>
<?php require_once('footer.php'); ?>
</div>
	
		
	</div>

<!-- Modal 7 (Ajax Modal)-->
<div class="modal fade custom-width in" id="modal-7">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add Menu Extra Item</h4>
            </div>
            
            <div class="modal-body">

                <?php 
                if(isset($_GET['insert'])){
                    if($_GET['insert']=="ok") {

                        $restaurant_menu_item_id = $_POST['restaurant_menu_item_id'];
                        $name = $_POST['name'];
                        $price = $_POST['price'];

                           $headers = array(
                            "Accept: application/json",
                            "Content-Type: application/json"
                           );
                           $data = array(
                            "restaurant_menu_item_id" => $restaurant_menu_item_id, 
                            "name" => $name, 
                            "price" => $price
                            );

                           $ch = curl_init( $baseurl.'/addMenuExtraItem' );

                           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                           curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                           curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                           curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                           $return = curl_exec($ch);

                           $curl_error = curl_error($ch);
                           $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                           curl_close($ch);

                           if($http_code !== 200){
                                echo "<script>window.location='show-menu-item.php?id=".$_GET['id']."&error=1';</script>";
                           }else{
                                echo "<script>window.location='show-menu-item.php?id=".$_GET['id']."&success=1';</script>";
                           }

                    }
                }
                ?>
                <form role="form" method="post" action="show-menu-item.php?id=<?php echo $_GET['id']; ?>&insert=ok" class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label for="field-1" class="col-sm-2 control-label">Restaurant ID</label>
                        
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="restaurant_menu_item_id" name="restaurant_menu_item_id" placeholder="Restaurant Menu ID" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-2 control-label">Name</label>
                        
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="name" placeholder="Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-2 control-label">Price</label>
                        
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="price" placeholder="Price">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-6">
                            <input type="submit" class="btn btn-primary" value="Add Menu Extra Item">
                        </div>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>



<!-- Modal 8 (Ajax Modal)-->
<div class="modal fade custom-width in" id="modal-8">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Show Menu Extra Items</h4>
            </div>
            
            <div class="modal-body">

                Content here..

            </div>

        </div>
    </div>
</div>


	<link rel="stylesheet" href="assets/js/datatables/responsive/css/datatables.responsive.css">
	<link rel="stylesheet" href="assets/js/select2/select2-bootstrap.css">
	<link rel="stylesheet" href="assets/js/select2/select2.css">

	<!-- Bottom Scripts -->
	<script src="assets/js/gsap/main-gsap.js"></script>
	<script src="assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js"></script>
	<script src="assets/js/bootstrap.js"></script>
	<script src="assets/js/joinable.js"></script>
	<script src="assets/js/resizeable.js"></script>
	<script src="assets/js/neon-api.js"></script>
	<script src="assets/js/jquery.dataTables.min.js"></script>
	<script src="assets/js/datatables/TableTools.min.js"></script>
	<script src="assets/js/dataTables.bootstrap.js"></script>
	<script src="assets/js/datatables/jquery.dataTables.columnFilter.js"></script>
	<script src="assets/js/datatables/lodash.min.js"></script>
	<script src="assets/js/datatables/responsive/js/datatables.responsive.js"></script>
	<script src="assets/js/select2/select2.min.js"></script>
	<script src="assets/js/neon-chat.js"></script>
	<script src="assets/js/neon-custom.js"></script>
	<script src="assets/js/neon-demo.js"></script>

</body>
</html>
<?php } else {
	@header('Location: login.php');
} ?>