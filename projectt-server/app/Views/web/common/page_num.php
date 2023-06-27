<div class="page_wrap">
	<ul>
	<?php
	if ($total_page == 0) {
	    ?>   
	    <!-- <a href="#"><div class="page_on">1</div></a> -->
	    <li><a href="#"><span class="pageon">1</span></a></li>
	    <?php
	} else {
	
	    if ($page > 1) {
	        $prv_page = $page - 1;
	        $prv_link = $default_link . "&page=" . $prv_page;
	    } else {
	        $prv_link = "javascript:;";
	    }
	
	    //이전 페이지 10쪽
	    if ($block > 1) {
	        $class_str_none_pre = "page";
	
	        $prv_b_page = $first_page - 1;
	        $prv_b_link = $default_link . "&page=" . $prv_b_page;
	    } else {
	        $class_str_none_pre = "page";
	        $prv_b_link = "javascript:;";
	    }
	    ?>
	    <?php
		// prev page function
		$current_page = $_GET['page'];

			$prev_page_string = $default_link . "&page=" . ($current_page - 1);

			if ($current_page > 1): ?>
	    		<li><a href="javascript: fnLoadingMove(`<?= $prev_page_string ?>`)"><span class='<?= $class_str_none_pre ?>'>이전</span></a></li>
			<?php endif; ?>
	    <?php
	    for ($i = $first_page; $i <= $last_page; $i++) {
	        if ($i == $page) {
	            echo "<li><a href='javascript:;'><span class='pageon'>$i</span></a></li> ";
	        } else {
	            $next_num_link = $default_link . "&page=" . $i;
	            //echo "<a href='javascript:fnLoadingMove(" . $next_num_link . ")'><div class='page'>" . $i . "</div></a>";
	            echo "<li><a href='javascript:fnLoadingMove(` $next_num_link `)'><span class='page'>" . $i . "</span></a></li> ";
	        }
	    }
	
	    //다음 페이지 10쪽
	    if ($block < $total_block) {
	        $class_str_none_next = "page";
	
	        $next_b_page = $last_page + 1;
	        $next_b_link = $default_link . "&page=" . $next_b_page;
	    } else {
	        $class_str_none_next = "page";
	        $next_b_link = "javascript:;";
	    }
	    ?>
		<?php
			//next page function
			if (!$current_page) {
				$current_page = 1;
			}

			$next_page_string = $default_link . "&page=" .  ($current_page + 1);
		
			if($total_page > $current_page): ?>	
	    		<li><a href="javascript: fnLoadingMove(`<?php echo $next_page_string ?>`)"><span class='<?= $class_str_none_next ?>'>다음</span></a></li>
			<?php endif; ?>
	    <?php
	}
	?>                
	</ul>
</div>