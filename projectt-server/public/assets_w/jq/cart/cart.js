
	var $cartWrap;
	var $cartWrap2;
	$(document).ready(function(e){
		
		
		$cartWrap=$(".cart_wrap");
		$cartWrap2=$(".cart_wrap2");
		$(".cart_up").bind("click",onMoving5);
		$(".cart_down").bind("click",onMoving6);
		$(".cart_up").bind("click",onMoving7);
		$(".cart_down").bind("click",onMoving8);
	
		
	})
		


		

		// 하단에서 나오는 메뉴
		function onMoving5(){
			$cartWrap.stop();
			$cartWrap.animate({bottom:0},500);
			$(".cart_down").show();
			$(".cart_up").hide();
		}


		function onMoving6(){
			$cartWrap.stop();
			$cartWrap.animate({bottom:-295},500);
			$(".cart_down").hide();
			$(".cart_up").show();
		}

