                <!-- page -->
                <div class="page">
<?php 
if ($total_page == 0) {
?>   
					<a href="" class="none"><i class="mte i_navigate_before vam"></i></a>
                    <a href="" class="on">1</a>
                    <a href="" class="none"><i class="mte i_navigate_next vam"></i></a> 
<?php
}
else {
    
    if($p_data['page'] >1) {
        $prv_page  = $p_data['page']-1;
        $prv_link  = $default_link."&page=".$prv_page;
    }
    else {
        $prv_link = "javascript:;";
    }
    
    //이전 페이지 10쪽
    if ($block > 1) {
        $class_str_none_pre = "";
        
        $prv_b_page = $first_page-1;
        $prv_b_link  = $default_link."&page=".$prv_b_page;
    }
    else {
        $class_str_none_pre = "none";
        $prv_b_link = "javascript:;";
    }
?>
				<a href="<?=$prv_b_link?>" class="<?=$class_str_none_pre?>"><i class="mte i_navigate_before vam"></i></a>
<?php    
    
    
    for($i=$first_page;$i<=$last_page; $i++) {
        if ($i == $p_data['page']) {
            echo "<a href='javascript:;' class='on'>$i</a>";
        }
        else {
            $next_num_link  = $default_link."&page=".$i;
            echo "<a href='".$next_num_link."'>".$i."</a>";
        }
    }
    
    //다음 페이지 10쪽
    if ($block < $total_block) {
        $class_str_none_next = "";
        
        $next_b_page  = $last_page+1;
        $next_b_link  = $default_link."&page=".$next_b_page;
    }
    else {
        $class_str_none_next = "none";
        $next_b_link = "javascript:;";
    }
?>
					<a href="<?=$next_b_link?>" class="<?=$class_str_none_next?>"><i class="mte i_navigate_next vam"></i></a>
<?php    
}
?>                
                    
                </div>
                <!-- END page -->