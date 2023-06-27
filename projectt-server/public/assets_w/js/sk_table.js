var totalTicker = 0,
  tickerTimer = {};
(function ($) {
  $.fn.tableTicker = function (options) {

    var settings = $.extend({
      row: 1,
      speed: 1000,
      interval: 3000,
      buffer: 0,
      complete: null
    }, options);

    function rollTicker(element) {
      if (!$(element).is(':animated')) {
        var table = element;
        var rows = table.rows;
        var w = $(element).outerWidth();
        var h = $(element).outerHeight();
        var mTop = (h / rows.length * settings.row) + settings.buffer;

        $(element).animate({
            'marginTop': '-' + mTop + 'px'
          },
          settings.speed, "linear",
          function () {
            $(rows).slice(0, settings.row).each(function (index, value) {
              table.tBodies[0].appendChild(value);
            });
            $(element).css("marginTop", "-" + settings.buffer + "px");
            if ($.isFunction(settings.complete)) {
              settings.complete.call(element);
            }
            this.rolled += 1;
            if (!this.paused) {
              tickerTimer[($(element).parent().prop('id'))] = setTimeout(function () {
                rollTicker(element);
              }, settings.interval);
            }
          });
      }
    }

    function startTicker(element) {
      if (element.paused) {
        element.paused = false;
        if (!$(element).is(':animated')) {
          tickerTimer[($(element).parent().prop('id'))] = setTimeout(function () {
            rollTicker(element);
          }, settings.interval);
        }
      }
    }

    function stopTicker(element) {
      clearTimeout(tickerTimer[($(element).parent().prop('id'))]);
      tickerTimer[($(element).parent().prop('id'))] = null;
      element.paused = true;
    }

    return this.each(function (index, value) {
      if (value && value.nodeName.toLowerCase() == "table") {
        if (value.parentNode && value.parentNode.id.indexOf('tableTicker_wrap') < 0) {
          totalTicker++;
          var table = value;
          var rows = table.rows;
          var w = $(table).outerWidth();
          var h = $(table).outerHeight();

          var wrap = $("<div />");
          wrap.prop('id', "tableTicker_wrap" + totalTicker);
          wrap.css({
            "width": w + 'px',
            "height": "200px",
            "position": "relative",
            "overflow": "hidden"
          });
          $(table).hover(
            function () {
              stopTicker(table);
            },
            function () {
              startTicker(table);
            }
          );
          $(table).css({
            "position": "absolute",
            "top": 0
          })
          $(table).wrap(wrap);
          table.paused = false;
          table.rolled = 0;
          tickerTimer[(wrap.prop('id'))] = setTimeout(function () {
            rollTicker(table);
          }, settings.interval);
        }
      }
    });
  };
}(jQuery));


// 위 까지가 플러그인 소스
// 아래부터 이용법
$(function () {

  $(".myTable").tableTicker({
    row: 1, //몇줄 롤링?
    speed: 1000, //롤링 속도 1000 = 1초에 애니메이션 종료
    interval: 2000, //정지 기간 (1000 = 1초, 0 = 논스탑)
    buffer: 0, // 이동폭 및 높이 보정용 추가 픽셀 (마이너스 값은 입력 불가)
    complete: null // 매 롤링 후 호출되는 callback function
  });

  $("#myTable3").tableTicker({
    row: 2,
    speed: 2000,
    interval: 2000,
    buffer: 0,
    complete: function () {
      $('#cnt').html(this.rolled);
    }
  });

})