<?php  if(count($popupList) > 0) { ?>
<div class="popup_wrap" id="agreePopup">
	<div class="popup_content">

		<?php for($i=0; $i < count($popupList); $i++) {?>
		<div id="agreePopup<?=$i+1 ?>" class="popup_content_box">
			<img src="<?=config(App::class)->IMAGE_SERVER.'/'.config(App::class)->imagePath.'/'.$popupList[$i]['thumbnail']?>">
			<div class="popup_button">
				<a href="javascript:fnSetHidePopup('agreePopup<?=$i+1 ?>', 1);">오늘하루 보지않기</a>
				<a href="javascript:fnHidePopup('agreePopup<?=$i+1 ?>');" class="popup_close">닫기</a>
			</div>
		</div>
		<?php }?>

	</div>
</div>
<?php } ?>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script>
	

	$(function () {
		const $popupList = <?= json_encode($popupList)?>;		// popup
		// let popupLength = $(document).find(".popup_content_box").length;	// 팝업갯수
		for( let i=0; i<=$popupList.length; i++){
			fnShowPopup(`agreePopup${i+1}`);
		}
	});


	// 팝업 열기
	const fnShowPopup = function(id) {
		if( $.cookie(`#${id}`) != 'hidden') {
			$(document).find(`#${id}`).removeClass('hide');
			$(document).find(`#${id}`).addClass("show");
		} else {
			$(document).find(`#${id}`).removeClass('show');
			$(document).find(`#${id}`).addClass("hide");
		}
		popupLengthChk();
	};


	// 팝업 보지 않기
	const fnSetHidePopup = function(id, expireDt) {
		if(!expireDt)
			expireDt = 1;

		$.cookie('#' + id, 'hidden', {expires : expireDt});

		$(document).find(`#${id}`).removeClass('show');
		$(document).find(`#${id}`).addClass('hide');
		popupLengthChk();
	};


	// 팝업 닫기
	const fnHidePopup = function(id) {
		$(document).find(`#${id}`).removeClass('show');
		$(document).find(`#${id}`).addClass('hide');
		popupLengthChk();
	};



	const popupLengthChk = function(){
		let popupLength = 0;
		let popup = $(document).find(".popup_content_box");
		popup.map((index, item)=>{
			if( $(item).hasClass("show") ){
				popupLength++;
			}		
		});

		if( popupLength < 1 ){
			$(document).find(".popup_wrap").hide();
		}
	}
</script>