$(function(){

	// gnb open
	$(document).on("click", ".open-gnb", function(e){
		e.preventDefault();
		e.stopImmediatePropagation();
		$(document).find("header").addClass("opened-gnb");
	});
	$(document).on("click", ".open-mypage", function(e){
		e.preventDefault();
		e.stopImmediatePropagation();
		$(document).find("header").addClass("opened-mypage");
	});

	//gnb close
	$(document).on("click", ".close-gnb", function(e){
		e.preventDefault();
		e.stopImmediatePropagation();
		$(document).find("header").removeClass("opened-gnb");
	});
	$(document).on("click", ".close-mypage", function(e){
		e.preventDefault();
		e.stopImmediatePropagation();
		$(document).find("header").removeClass("opened-mypage");
	});



	// loading link
	// $(document).on("click", ".loading__link", function(e){
	// 	e.preventDefault();
    //     console.log('click');
	// 	let link = $(this).attr("href");
	// 	// // loadingMove(link);	// loading link
	// 	// console.log(link);
	// 	// $('#loadingCircle').show();
    // 	// // location.href = link;
	// 	window.location.href = link;
	// });


	// scroll top
	$(document).on("scroll", function(){
		let scroll = $(window).scrollTop();
		if( scroll >= 50 ){
			$(".go-top").addClass("show");
		} else {
			$(".go-top").removeClass("show");
		}
	});
	$(document).on("click", ".go-top", function(e){
		e.preventDefault();
		$("html,body").stop().animate({"scrollTop":0},500);
	});



    // F5 check
    // document.onkeydown = fkey;
    // document.onkeyup = fkey;
    // let wasPressed = false;
    // function fkey(e){
    //     e = e || window.event;
    //     if(wasPressed) return;

    //     if(e.keyCode == 116){
    //         $('#loadingCircle').show();
    //     }
    // }

    // Refresh detection
    window.addEventListener('beforeunload', (event) => {
        // Depending on the specification, preventDefault must be invoked, preventing default behavior.
        // event.preventDefault();
        console.log('beforeunload');
        $('#loadingCircle').show();
    });


    $(document).on("click", ".odds_3_folder_bonus", function(e){
        alert("3폴더이상 선택시 보너스폴더는 자동선택됩니다.");
    });
    $(document).on("click", ".odds_4_folder_bonus", function(e){
        alert("4폴더이상 선택시 보너스폴더는 자동선택됩니다.");
    });
    $(document).on("click", ".odds_6_folder_bonus", function(e){
        alert("6폴더이상 선택시 보너스폴더는 자동선택됩니다.");
    });


});




// resize event
$(window).on("resize", function(){
	mobileNav();
});

function mobileNav(){
	let width = $(window).outerWidth();
	if( width <= 1320 ){
		$(document).find("body").addClass("mobile")
		$(document).find("header").addClass("mobile");
	} else {
		$(document).find("body").removeClass("mobile")
		$(document).find("header").removeClass("mobile");
	}
}
mobileNav();






// input keyup event
$(function(){

    $(document).find("input[type=text], input[type=tel], input[type=number], input[type=password], input[onlyNumber], input[onlyCerty], input[onlyMoney], input[onlyTel], input[onlyNumber-password], input[onlyRate], input[onlyKor], input[onlyText], input[noGap], input[onlyRecom]").attr("autocomplete", "off");

    $(document).find("input[onlyNumber], input[onlyMoney], input[onlyTel], input[onlyNumber-password], input[type=number], input[type=tel]").attr("pattern", "[0-9]*");


    // 숫자만 입력받기
    $(document).on("propertychange change keyup keypress keydown paste input", "input[onlyNumber]", function(e){
        if( $(this).prop("readonly") ){ return false; }
        if( this.value.length > $(this).attr("maxlength") ){
            this.value = this.value.slice(0, this.maxLength);
        }

        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    });


    
    // 숫자만 입력받기 => 금액
    $(document).on("focus", "input[onlyMoney]", function(e){
        this.value = format_remove(this.value);
    });
    $(document).on("blur", "input[onlyMoney]", function(e){
        this.value = format_money(this.value);
    });
    $(document).on("propertychange change keyup keypress keydown paste input", "input[onlyMoney]", function(e){
        if( $(this).prop("readonly") ){ return false; }
        
        if( this.value.length > $(this).attr("maxlength") ){
            this.value = this.value.slice(0, this.maxLength);
        }
        this.value = this.value.replace(/[^0-9]/g, '');
    });
	
    
    // 숫자만 입력받기 => 전화번호
    $(document).on("propertychange change keyup keypress keydown paste input", "input[onlyTel]", function(e){
        if( $(this).prop("readonly") ){ return false; }
        this.value = this.value.replace(/[^0-9]/g, '');
        this.value = this.value.replace(/^(\d{2,3})(\d{3,4})(\d{3,4})$/, `$1-$2-$3`);
    });
	
    // type=number 비밀번호 숫자만 입력받기
    $(document).on("propertychange change keyup keypress keydown paste input", "input[onlyNumber-password]", function(e){

        // e.target.value = e.target.value.substr(0, e.target.maxLength);

        if( regExpEmpty.test(this.value)            // 공백
            ||  regExpEng.test(this.value)          // 영어
            ||  regExpKor.test(this.value)          // 한글
            ||  regExpSpecial.test(this.value) ){   // 특문
            
            e.preventDefault();
            this.value = '';
        }

    })
	
    // 숫자만 입력받기 => 수수료 00.00
    $(document).on("propertychange change keyup keypress keydown paste input", "input[onlyRate]", function(e){
        if( $(this).prop("readonly") ){ return false; }
        this.value = this.value.replace(/[^0-9]/g, '');
        this.value = this.value.replace(/^(\d{1,2})(\d{2})$/, `$1.$2`);
    });


    // input[type="number"] 키보드 방향키 막기
    $(document).on("keydown keypress keyup", "input[type='number']", function(e){
        if(!((e.keyCode > 95 && e.keyCode < 106)
            || (e.keyCode > 47 && e.keyCode < 58)
            || e.keyCode == 8
            || e.keyCode == 9
            || e.keyCode == 0)
            || /[a-z|ㄱ-ㅎ|ㅏ-ㅣ|가-힣]/g.test(this.value) ) {   // 숫자만 입력, 한글 막기
            return false;
        }
    });

	

    // 한글
    $(document).on("propertychange change keyup keypress keydown paste input", "input[onlyKor]", function(e){
        // this.value = this.value.replace(/[a-z0-9]|[ \[\]{}()<>?|`~!@#$%^&*-_+=,.;:\"'\\]/g, '');
    });
    $(document).on("blur", "input[onlyKor]", function(e){
        let pattern = /[a-z0-9]|[ \[\]{}()<>?|`~!@#$%^&*-_+=,.;:\"'\\]/g;
        let value = this.value;

        if ( pattern.test(value) ){
            alert("한글만 입력해주세요");
            this.value = "";
            e.target.select();
        }
    });
    
    // 한글영문
    $(document).on("propertychange change keyup keypress keydown paste input", "input[onlyText]", function(e){
        if( regExpEmpty.test(this.value)            // 공백
            ||  regExpNumber.test(this.value)       // 숫자
            ||  regExpSpecial.test(this.value) ){   // 특문
            e.preventDefault();
            this.value = '';
        }
    });

    // 공백제거
    $(document).on("propertychange change keyup keypress keydown paste input", "input[noGap]", function(e){
        this.value = this.value.replace(/\s/g,'');
    });



















});







// money comma(,)
function format_money(item){
    if( item == null ){ return "-"; }
    item = String(item);
    return item = Number(item).toLocaleString('ko-KR');
}

// phone number 000-****-0000
function format_phone(phone){
    if( phone == null ){ return "-"; }
    phone = String(phone);
    return phone = phone.replace(/(^02.{0}|^01.{1}|[0-9]{3})([0-9]+)([0-9]{4})/,"$1-****-$3");

}

// phone number 000-0000-0000
function format_phone_all(phone){
    if( phone == null ){ return "-"; }
    phone = String(phone);
    return phone = phone.replace(/(^02.{0}|^01.{1}|[0-9]{3})([0-9]+)([0-9]{4})/,"$1-$2-$3");
}


// Remove special characters
function format_remove(item){
    if( item == null ){ return "-"; }
    item = String(item);
    return item = item.replace(/[\{\}\[\]\/?.,;:|\)*~`!^\-_+<>@\#$%&\\\=\(\'\"]/g, "");
}

// Remove Spaces
function format_noSpace(item){
    if( item == null ){ return "-"; }
    item = String(item);
    return item = item.replace(/\s/g,'');
}

// HTML
function format_html(item){
    if( item == null ){ return item; }
    item = String(item);
    return item = item.replace(/\n/gi,'<br>');
}







/*************************************************************************************** */





	
// loading image
function fnLoadingMove(link){
	$('#loadingCircle').show();
	location.href = link;
};


















// $(function(){
// 	var balloon = $('<div class="tooltip"></div>').appendTo('body');
// 	function updateBalloonPosition(x,y){
// 		balloon.css({left:x-10,top:y+20});
// 	};
// 	$('.bet_max').each(function(){
// 		var element = $(this);
// 		var text = "최대베팅금액";
// 		element.hover(
// 			function(event){
// 				balloon.text(text);
// 				updateBalloonPosition(event.pageX,event.pageY);
// 				balloon.show();
// 			},
// 			function(){
// 				balloon.hide();
// 			}
// 		);
// 		element.mousemove(function(event){
// 			updateBalloonPosition(event.pageX,event.pageY);
// 		});	
// 	});
// });



















// aside open
function injectAsidebar(jQuery) {
	jQuery.fn.asidebar = function asidebar(status) {
	  switch (status) {
		case "open":
		  var that = this;
		  // fade in backdrop
		  if ($(".aside-backdrop").length === 0) {
			$("body").append("<div class='aside-backdrop'></div>");
		  }
		  $(".aside-backdrop").addClass("in");
  
  
		  function close() {
			$(that).asidebar.apply(that, ["close"]);
		  }
  
		  // slide in asidebar
		  $(this).addClass("in");
		  $(this).find("[data-dismiss=aside], [data-dismiss=asidebar]").on('click', close);
		  $(".aside-backdrop").on('click', close);
		  break;
		case "close":
		  // fade in backdrop
		  if ($(".aside-backdrop.in").length > 0) {
			$(".aside-backdrop").removeClass("in");
		  }
  
		  // slide in asidebar
		  $(this).removeClass("in");
		  break;
		case "toggle":
		  if($(this).attr("class").split(' ').indexOf('in') > -1) {
			$(this).asidebar("close");
		  } else {
			$(this).asidebar("open");
		  }
		  break;
	  }
	}
  }
  
// support browser and node
if (typeof jQuery !== "undefined") {
	injectAsidebar(jQuery);
} else if (typeof module !== "undefined" && module.exports) {
	module.exports = injectAsidebar;
}
  