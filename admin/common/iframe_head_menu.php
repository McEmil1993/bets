<?php
if($_SESSION['u_business'] == 0){
    include_once(_BASEPATH . '/common/head_menu_admin.php');
}else{
    include_once(_BASEPATH . '/common/head_menu_distributor.php');
}
?>