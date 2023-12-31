var handleAddCommasToNumber = function (e) {
    return e.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")
},
    handlePageScrollContentAnimation = function () {
        $('[data-scrollview="true"]').each(function () {
            var e = $(this);
            scrollMonitor.create(e, 60).enterViewport(function () {
                $(e).find("[data-animation=true]").each(function () {
                    var e = $(this).attr("data-animation-type"),
                        a = $(this);
                    if (!$(a).hasClass("contentAnimated"))
                        if ("number" == e) {
                            var t = parseInt($(a).attr("data-final-number"));
                            $({
                                animateNumber: 0
                            }).animate({
                                animateNumber: t
                            }, {
                                duration: 4e3,
                                easing: "swing",
                                step: function () {
                                    var e = handleAddCommasToNumber(Math.ceil(this.animateNumber));
                                    $(a).text(e).addClass("contentAnimated")
                                }
                            })
                        } else $(this).addClass(e + " contentAnimated"), setTimeout(function () {
                            $(a).addClass("finishAnimated")
                        }, 1500)
                })
            })
        })
    },
    handleSlimScroll = function () {
        "use strict";
        $("[data-scrollbar=true]").each(function () {
            generateSlimScroll($(this))
        })
    },
    generateSlimScroll = function (e) {
        if (!$(e).attr("data-init")) {
            var a = $(e).attr("data-height"),
                t = {
                    height: a = a || $(e).height(),
                    alwaysVisible: !0
                };
            /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ? ($(e).css("height", a), $(e).css("overflow-x", "scroll")) : $(e).slimScroll(t), $(e).attr("data-init", !0)
        }
    },
    // handleSidebarMenu = function () {
    //     "use strict";
    //     $(".sidebar .nav > .has-sub > a").click(function () {
    //         var e = $(this).next(".sub-menu");
    //         0 === $(".page-sidebar-minified").length && ($(".sidebar .nav > li.has-sub > .sub-menu").not(e).slideUp(250, function () {
    //             $(this).closest("li").removeClass("expand")
    //         }), $(e).slideDown(250, function () {
    //             $(this).addClass('slide-open');
    //             var e = $(this).closest("li");
    //             $(e).hasClass("expand") ? $(e).removeClass("expand") : $(e).addClass("expand")
    //         }))
    //     }), $(".sidebar .nav > .has-sub .sub-menu li.has-sub > a").click(function () {
    //         if (0 === $(".page-sidebar-minified").length) {
    //             var e = $(this).next(".sub-menu");
    //             $(e).slideDown(250)
    //             $(this).addClass('slide-open');
    //         }
    //     })
    // },
    // handleMobileSidebarToggle = function () {
    //     var e = !1;
    //     $(".sidebar").bind("click touchstart", function (a) {
    //         0 !== $(a.target).closest(".sidebar").length ? e = !0 : (e = !1, a.stopPropagation())
    //     }), $(document).bind("click touchstart", function (a) {
    //         0 === $(a.target).closest(".sidebar").length && (e = !1), a.isPropagationStopped() || !0 === e || ($("#page-container").hasClass("page-sidebar-toggled") && (e = !0, $("#page-container").removeClass("page-sidebar-toggled")), $(window).width() <= 767 && $("#page-container").hasClass("page-right-sidebar-toggled") && (e = !0, $("#page-container").removeClass("page-right-sidebar-toggled")))
    //     }), $("[data-click=right-sidebar-toggled]").click(function (a) {
    //         a.stopPropagation();
    //         var t = "#page-container",
    //             i = "page-right-sidebar-collapsed";
    //         i = $(window).width() < 979 ? "page-right-sidebar-toggled" : i, $(t).hasClass(i) ? $(t).removeClass(i) : !0 !== e ? $(t).addClass(i) : e = !1, $(window).width() < 480 && $("#page-container").removeClass("page-sidebar-toggled"), $(window).trigger("resize")
    //     }), $("[data-click=sidebar-toggled]").click(function (a) {
    //         a.stopPropagation();
    //         var t = "page-sidebar-toggled",
    //             i = "#page-container";
    //         $(i).hasClass(t) ? $(i).removeClass(t) : !0 !== e ? $(i).addClass(t) : e = !1, $(window).width() < 480 && $("#page-container").removeClass("page-right-sidebar-toggled")
    //     })
    // },
    handleSidebarMinify = function () {
        $("[data-click=sidebar-minify]").click(function (e) {
            e.preventDefault();
            var a = "page-sidebar-minified",
                t = "#page-container";
            $('#sidebar [data-scrollbar="true"]').css("margin-top", "0"), $('#sidebar [data-scrollbar="true"]').removeAttr("data-init"), $("#sidebar [data-scrollbar=true]").stop(), $(t).hasClass(a) ? ($(t).removeClass(a), $(t).hasClass("page-sidebar-fixed") ? (0 !== $("#sidebar .slimScrollDiv").length && ($('#sidebar [data-scrollbar="true"]').slimScroll({
                destroy: !0
            }), $('#sidebar [data-scrollbar="true"]').removeAttr("style")), generateSlimScroll($('#sidebar [data-scrollbar="true"]')), $("#sidebar [data-scrollbar=true]").trigger("mouseover")) : /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) && (0 !== $("#sidebar .slimScrollDiv").length && ($('#sidebar [data-scrollbar="true"]').slimScroll({
                destroy: !0
            }), $('#sidebar [data-scrollbar="true"]').removeAttr("style")), generateSlimScroll($('#sidebar [data-scrollbar="true"]')))) : ($(t).addClass(a), /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ? ($('#sidebar [data-scrollbar="true"]').css("margin-top", "0"), $('#sidebar [data-scrollbar="true"]').css("overflow", "visible")) : ($(t).hasClass("page-sidebar-fixed") && ($('#sidebar [data-scrollbar="true"]').slimScroll({
                destroy: !0
            }), $('#sidebar [data-scrollbar="true"]').removeAttr("style")), $("#sidebar [data-scrollbar=true]").trigger("mouseover"))), $(window).trigger("resize")
        })
    },
    handlePageContentView = function () {
        "use strict";
        $.when($("#page-loader").addClass("hide")).done(function () {
            $("#page-container").addClass("in")
        })
    },
    panelActionRunning = !1,
    handlePanelAction = function () {
        "use strict";
        if (panelActionRunning) return !1;
        panelActionRunning = !0, $(document).on("hover", "[data-click=panel-remove]", function (e) {
            $(this).attr("data-init") || ($(this).tooltip({
                title: "Remove",
                placement: "bottom",
                trigger: "hover",
                container: "body"
            }), $(this).tooltip("show"), $(this).attr("data-init", !0))
        }), $(document).on("click", "[data-click=panel-remove]", function (e) {
            e.preventDefault(), $(this).tooltip("destroy"), $(this).closest(".panel").remove()
        }), $(document).on("hover", "[data-click=panel-collapse]", function (e) {
            $(this).attr("data-init") || ($(this).tooltip({
                title: "Collapse / Expand",
                placement: "bottom",
                trigger: "hover",
                container: "body"
            }), $(this).tooltip("show"), $(this).attr("data-init", !0))
        }), $(document).on("click", "[data-click=panel-collapse]", function (e) {
            e.preventDefault(), $(this).closest(".panel").find(".panel-body").slideToggle()
        }), $(document).on("hover", "[data-click=panel-reload]", function (e) {
            $(this).attr("data-init") || ($(this).tooltip({
                title: "Reload",
                placement: "bottom",
                trigger: "hover",
                container: "body"
            }), $(this).tooltip("show"), $(this).attr("data-init", !0))
        }), $(document).on("click", "[data-click=panel-reload]", function (e) {
            e.preventDefault();
            var a = $(this).closest(".panel");
            if (!$(a).hasClass("panel-loading")) {
                var t = $(a).find(".panel-body");
                $(a).addClass("panel-loading"), $(t).prepend('<div class="panel-loader"><span class="spinner-small"></span></div>'), setTimeout(function () {
                    $(a).removeClass("panel-loading"), $(a).find(".panel-loader").remove()
                }, 2e3)
            }
        }), $(document).on("hover", "[data-click=panel-expand]", function (e) {
            $(this).attr("data-init") || ($(this).tooltip({
                title: "Expand / Compress",
                placement: "bottom",
                trigger: "hover",
                container: "body"
            }), $(this).tooltip("show"), $(this).attr("data-init", !0))
        }), $(document).on("click", "[data-click=panel-expand]", function (e) {
            e.preventDefault();
            var a = $(this).closest(".panel"),
                t = $(a).find(".panel-body"),
                i = 40;
            if (0 !== $(t).length) {
                var n = $(a).offset().top;
                i = $(t).offset().top - n
            }
            if ($("body").hasClass("panel-expand") && $(a).hasClass("panel-expand")) $("body, .panel").removeClass("panel-expand"), $(".panel").removeAttr("style"), $(t).removeAttr("style");
            else if ($("body").addClass("panel-expand"), $(this).closest(".panel").addClass("panel-expand"), 0 !== $(t).length && 40 != i) {
                var o = 40;
                $(a).find(" > *").each(function () {
                    var e = $(this).attr("class");
                    "panel-heading" != e && "panel-body" != e && (o += $(this).height() + 30)
                }), 40 != o && $(t).css("top", o + "px")
            }
            $(window).trigger("resize")
        })
    },
    handleDraggablePanel = function () {
        "use strict";
        var e = $(".panel").parent("[class*=col]");
        $(e).sortable({
            handle: ".panel-heading",
            connectWith: ".row > [class*=col]",
            stop: function (e, a) {
                a.item.find(".panel-title").append('<i class="fa fa-refresh fa-spin m-l-5" data-id="title-spinner"></i>'), handleSavePanelPosition(a.item)
            }
        })
    },
    handelTooltipPopoverActivation = function () {
        "use strict";
        0 !== $('[data-toggle="tooltip"]').length && $("[data-toggle=tooltip]").tooltip(), 0 !== $('[data-toggle="popover"]').length && $("[data-toggle=popover]").popover()
    },
    handleScrollToTopButton = function () {
        "use strict";
        $(document).scroll(function () {
            $(document).scrollTop() >= 200 ? $("[data-click=scroll-top]").addClass("in") : $("[data-click=scroll-top]").removeClass("in")
        }), $("[data-click=scroll-top]").click(function (e) {
            e.preventDefault(), $("html, body").animate({
                scrollTop: $("body").offset().top
            }, 500)
        })
    },
    handleThemePageStructureControl = function () {
        if ($.cookie && $.cookie("theme")) {
            0 !== $(".theme-list").length && ($(".theme-list [data-theme]").closest("li").removeClass("active"), $('.theme-list [data-theme="' + $.cookie("theme") + '"]').closest("li").addClass("active"));
            var e = "assets/css/theme/" + $.cookie("theme") + ".css";
            $("#theme").attr("href", e)
        }
        $.cookie && $.cookie("sidebar-styling") && 0 !== $(".sidebar").length && "grid" == $.cookie("sidebar-styling") && ($(".sidebar").addClass("sidebar-grid"), $('[name=sidebar-styling] option[value="2"]').prop("selected", !0)), $.cookie && $.cookie("header-styling") && 0 !== $(".header").length && "navbar-inverse" == $.cookie("header-styling") && ($(".header").addClass("navbar-inverse"), $('[name=header-styling] option[value="2"]').prop("selected", !0)), $.cookie && $.cookie("content-gradient") && 0 !== $("#page-container").length && "enabled" == $.cookie("content-gradient") && ($("#page-container").addClass("gradient-enabled"), $('[name=content-gradient] option[value="2"]').prop("selected", !0)), $.cookie && $.cookie("content-styling") && 0 !== $("body").length && "black" == $.cookie("content-styling") && ($("body").addClass("flat-black"), $('[name=content-styling] option[value="2"]').prop("selected", !0)), $(".theme-list [data-theme]").click(function () {
            var e = "assets/css/theme/" + $(this).attr("data-theme") + ".css";
            $("#theme").attr("href", e), $(".theme-list [data-theme]").not(this).closest("li").removeClass("active"), $(this).closest("li").addClass("active"), $.cookie("theme", $(this).attr("data-theme"))
        }), $(".theme-panel [name=header-styling]").on("change", function () {
            var e = 1 == $(this).val() ? "navbar-default" : "navbar-inverse",
                a = 1 == $(this).val() ? "navbar-inverse" : "navbar-default";
            $("#header").removeClass(a).addClass(e), $.cookie("header-styling", e)
        }), $(".theme-panel [name=sidebar-styling]").on("change", function () {
            2 == $(this).val() ? ($("#sidebar").addClass("sidebar-grid"), $.cookie("sidebar-styling", "grid")) : ($("#sidebar").removeClass("sidebar-grid"), $.cookie("sidebar-styling", "default"))
        }), $(".theme-panel [name=content-gradient]").on("change", function () {
            2 == $(this).val() ? ($("#page-container").addClass("gradient-enabled"), $.cookie("content-gradient", "enabled")) : ($("#page-container").removeClass("gradient-enabled"), $.cookie("content-gradient", "disabled"))
        }), $(document).on("change", ".theme-panel [name=content-styling]", function () {
            2 == $(this).val() ? ($("body").addClass("flat-black"), $.cookie("content-styling", "black")) : ($("body").removeClass("flat-black"), $.cookie("content-styling", "default"))
        }), $(document).on("change", ".theme-panel [name=sidebar-fixed]", function () {
            1 == $(this).val() ? (2 == $(".theme-panel [name=header-fixed]").val() && (alert("Default Header with Fixed Sidebar option is not supported. Proceed with Fixed Header with Fixed Sidebar."), $('.theme-panel [name=header-fixed] option[value="1"]').prop("selected", !0), $("#header").addClass("navbar-fixed-top"), $("#page-container").addClass("page-header-fixed")), $("#page-container").addClass("page-sidebar-fixed"), $("#page-container").hasClass("page-sidebar-minified") || generateSlimScroll($('.sidebar [data-scrollbar="true"]'))) : ($("#page-container").removeClass("page-sidebar-fixed"), 0 !== $(".sidebar .slimScrollDiv").length && ($(window).width() <= 979 ? $(".sidebar").each(function () {
                if (!$("#page-container").hasClass("page-with-two-sidebar") || !$(this).hasClass("sidebar-right")) {
                    $(this).find(".slimScrollBar").remove(), $(this).find(".slimScrollRail").remove(), $(this).find('[data-scrollbar="true"]').removeAttr("style");
                    var e = $(this).find('[data-scrollbar="true"]').parent(),
                        a = $(e).html();
                    $(e).replaceWith(a)
                }
            }) : $(window).width() > 979 && ($('.sidebar [data-scrollbar="true"]').slimScroll({
                destroy: !0
            }), $('.sidebar [data-scrollbar="true"]').removeAttr("style"))), 0 === $("#page-container .sidebar-bg").length && $("#page-container").append('<div class="sidebar-bg"></div>'))
        }), $(document).on("change", ".theme-panel [name=header-fixed]", function () {
            1 == $(this).val() ? ($("#header").addClass("navbar-fixed-top"), $("#page-container").addClass("page-header-fixed"), $.cookie("header-fixed", !0)) : (1 == $(".theme-panel [name=sidebar-fixed]").val() && (alert("Default Header with Fixed Sidebar option is not supported. Proceed with Default Header with Default Sidebar."), $('.theme-panel [name=sidebar-fixed] option[value="2"]').prop("selected", !0), $("#page-container").removeClass("page-sidebar-fixed"), 0 === $("#page-container .sidebar-bg").length && $("#page-container").append('<div class="sidebar-bg"></div>')), $("#header").removeClass("navbar-fixed-top"), $("#page-container").removeClass("page-header-fixed"), $.cookie("header-fixed", !1))
        })
    },
    handleThemePanelExpand = function () {
        $(document).on("click", '[data-click="theme-panel-expand"]', function () {
            var e = ".theme-panel";
            $(e).hasClass("active") ? $(e).removeClass("active") : $(e).addClass("active")
        })
    },
    handleAfterPageLoadAddClass = function () {
        0 !== $("[data-pageload-addclass]").length && $(window).load(function () {
            $("[data-pageload-addclass]").each(function () {
                var e = $(this).attr("data-pageload-addclass");
                $(this).addClass(e)
            })
        })
    },
    handleSavePanelPosition = function (e) {
        "use strict";
        if (0 !== $(".ui-sortable").length) {
            var a = [],
                t = 0;
            $.when($(".ui-sortable").each(function () {
                var e = $(this).find("[data-sortable-id]");
                if (0 !== e.length) {
                    var i = [];
                    $(e).each(function () {
                        var e = $(this).attr("data-sortable-id");
                        i.push({
                            id: e
                        })
                    }), a.push(i)
                } else a.push([]);
                t++
            })).done(function () {
                var t = window.location.href;
                t = (t = t.split("?"))[0], localStorage.setItem(t, JSON.stringify(a)), $(e).find('[data-id="title-spinner"]').delay(500).fadeOut(500, function () {
                    $(this).remove()
                })
            })
        }
    },
    handleLocalStorage = function () {
        "use strict";
        if ("undefined" != typeof Storage && "undefined" != typeof localStorage) {
            var e = window.location.href;
            e = (e = e.split("?"))[0];
            var a = localStorage.getItem(e);
            if (a) {
                a = JSON.parse(a);
                var t = 0;
                $.when($(".panel").parent('[class*="col-"]').each(function () {
                    var e = a[t],
                        i = $(this);
                    e && $.each(e, function (e, a) {
                        var t = $('[data-sortable-id="' + a.id + '"]').not('[data-init="true"]');
                        if (0 !== $(t).length) {
                            var n = $(t).clone();
                            $(t).remove(), $(i).append(n), $('[data-sortable-id="' + a.id + '"]').attr("data-init", "true")
                        }
                    }), t++
                })).done(function () {
                    window.dispatchEvent(new CustomEvent("localstorage-position-loaded"))
                })
            }
        } else alert("Your browser is not supported with the local storage")
    },
    handleResetLocalStorage = function () {
        "use strict";
        $(document).on("click", "[data-click=reset-local-storage]", function (e) {
            e.preventDefault();
            $("body").append('<div class="modal fade" data-modal-id="reset-local-storage-confirmation">    <div class="modal-dialog">        <div class="modal-content">            <div class="modal-header">                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>                <h4 class="modal-title"><i class="fa fa-refresh m-r-5"></i> Reset Local Storage Confirmation</h4>            </div>            <div class="modal-body">                <div class="alert alert-info m-b-0">Would you like to RESET all your saved widgets and clear Local Storage?</div>            </div>            <div class="modal-footer">                <a href="javascript:;" class="btn btn-sm btn-white" data-dismiss="modal"><i class="fa fa-close"></i> No</a>                <a href="javascript:;" class="btn btn-sm btn-inverse" data-click="confirm-reset-local-storage"><i class="fa fa-check"></i> Yes</a>            </div>        </div>    </div></div>'), $('[data-modal-id="reset-local-storage-confirmation"]').modal("show")
        }), $(document).on("hidden.bs.modal", '[data-modal-id="reset-local-storage-confirmation"]', function (e) {
            $('[data-modal-id="reset-local-storage-confirmation"]').remove()
        }), $(document).on("click", "[data-click=confirm-reset-local-storage]", function (e) {
            e.preventDefault();
            var a = window.location.href;
            a = (a = a.split("?"))[0], localStorage.removeItem(a), location.reload()
        })
    },
    handleIEFullHeightContent = function () {
        (window.navigator.userAgent.indexOf("MSIE ") > 0 || navigator.userAgent.match(/Trident.*rv\:11\./)) && $('.vertical-box-row [data-scrollbar="true"][data-height="100%"]').each(function () {
            var e = $(this).closest(".vertical-box-row"),
                a = $(e).height();
            $(e).find(".vertical-box-cell").height(a)
        })
    },
    handleUnlimitedTabsRender = function () {
        function e(e, a) {
            var t = $(e).closest(".tab-overflow"),
                i = parseInt($(t).find(".nav.nav-tabs").css("margin-left")),
                n = $(t).width(),
                o = 0,
                s = 0;
            switch ($(t).find("li").each(function () {
                $(this).hasClass("next-button") || $(this).hasClass("prev-button") || (o += $(this).width())
            }), a) {
                case "next":
                    (l = o + i - n) <= n ? (s = l - i, setTimeout(function () {
                        $(t).removeClass("overflow-right")
                    }, 150)) : s = n - i - 80, 0 != s && $(t).find(".nav.nav-tabs").animate({
                        marginLeft: "-" + s + "px"
                    }, 150, function () {
                        $(t).addClass("overflow-left")
                    });
                    break;
                case "prev":
                    var l;
                    (l = -i) <= n ? ($(t).removeClass("overflow-left"), s = 0) : s = l - n + 80, $(t).find(".nav.nav-tabs").animate({
                        marginLeft: "-" + s + "px"
                    }, 150, function () {
                        $(t).addClass("overflow-right")
                    })
            }
        }

        function a() {
            $(".tab-overflow").each(function () {
                var e = $(this).width(),
                    a = 0,
                    t = $(this),
                    i = e;
                $(t).find("li").each(function () {
                    var t = $(this);
                    a += $(t).width(), $(t).hasClass("active") && a > e && (i -= a)
                }),
                    function (e, a) {
                        parseInt($(e).css("margin-left"));
                        var t = $(e).width(),
                            i = $(e).find("li.active").width(),
                            n = a > -1 ? a : 150,
                            o = 0;
                        if ($(e).find("li.active").prevAll().each(function () {
                            i += $(this).width()
                        }), $(e).find("li").each(function () {
                            o += $(this).width()
                        }), i >= t) {
                            var s = i - t;
                            o != i && (s += 40), $(e).find(".nav.nav-tabs").animate({
                                marginLeft: "-" + s + "px"
                            }, n)
                        }
                        i != o && o >= t ? $(e).addClass("overflow-right") : $(e).removeClass("overflow-right"), i >= t && o >= t ? $(e).addClass("overflow-left") : $(e).removeClass("overflow-left")
                    }(this, 0)
            })
        }
        $('[data-click="next-tab"]').click(function (a) {
            a.preventDefault(), e(this, "next")
        }), $('[data-click="prev-tab"]').click(function (a) {
            a.preventDefault(), e(this, "prev")
        }), $(window).resize(function () {
            $(".tab-overflow .nav.nav-tabs").removeAttr("style"), a()
        }), a()
    },
    handleMobileSidebar = function () {
        "use strict";
        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) && $("#page-container").hasClass("page-sidebar-minified") && ($('#sidebar [data-scrollbar="true"]').css("overflow", "visible"), $('.page-sidebar-minified #sidebar [data-scrollbar="true"]').slimScroll({
            destroy: !0
        }), $('.page-sidebar-minified #sidebar [data-scrollbar="true"]').removeAttr("style"), $(".page-sidebar-minified #sidebar [data-scrollbar=true]").trigger("mouseover"));
        var e = 0;
        $(".page-sidebar-minified .sidebar [data-scrollbar=true] a").bind("touchstart", function (a) {
            var t = (a.originalEvent.touches[0] || a.originalEvent.changedTouches[0]).pageY;
            e = t - parseInt($(this).closest("[data-scrollbar=true]").css("margin-top"))
        }), $(".page-sidebar-minified .sidebar [data-scrollbar=true] a").bind("touchmove", function (a) {
            if (a.preventDefault(), /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                var t = (a.originalEvent.touches[0] || a.originalEvent.changedTouches[0]).pageY - e;
                $(this).closest("[data-scrollbar=true]").css("margin-top", t + "px")
            }
        }), $(".page-sidebar-minified .sidebar [data-scrollbar=true] a").bind("touchend", function () {
            var t = $(this).closest("[data-scrollbar=true]"),
                i = $(window).height(),
                n = parseInt($("#sidebar").css("padding-top")),
                o = $("#sidebar").height();
            e = $(t).css("margin-top");
            var s = n;
            $(".sidebar").not(".sidebar-right").find(".nav").each(function () {
                s += $(this).height()
            });
            if (-parseInt(e) + $(".sidebar").height() >= s && i <= s && o <= s) {
                var l = i - s - 20;
                $(t).animate({
                    marginTop: l + "px"
                })
            } else parseInt(e) >= 0 || o >= s ? $(t).animate({
                marginTop: "0px"
            }) : (l = e, $(t).animate({
                marginTop: l + "px"
            }))
        })
    },
    handleUnlimitedTopMenuRender = function () {
        "use strict";

        function e(e, a) {
            var t = $(e).closest(".nav"),
                i = parseInt($(t).css("margin-left")),
                n = $(".top-menu").width() - 88,
                o = 0,
                s = 0;
            switch ($(t).find("li").each(function () {
                $(this).hasClass("menu-control") || (o += $(this).width())
            }), a) {
                case "next":
                    (l = o + i - n) <= n ? (s = l - i + 128, setTimeout(function () {
                        $(t).find(".menu-control.menu-control-right").removeClass("show")
                    }, 150)) : s = n - i - 128, 0 != s && $(t).animate({
                        marginLeft: "-" + s + "px"
                    }, 150, function () {
                        $(t).find(".menu-control.menu-control-left").addClass("show")
                    });
                    break;
                case "prev":
                    var l;
                    (l = -i) <= n ? ($(t).find(".menu-control.menu-control-left").removeClass("show"), s = 0) : s = l - n + 88, $(t).animate({
                        marginLeft: "-" + s + "px"
                    }, 150, function () {
                        $(t).find(".menu-control.menu-control-right").addClass("show")
                    })
            }
        }

        function a() {
            var e = $(".top-menu .nav"),
                a = $(".top-menu .nav > li"),
                t = $(".top-menu .nav > li.active"),
                i = $(".top-menu"),
                n = (parseInt($(e).css("margin-left")), $(i).width() - 128),
                o = $(".top-menu .nav > li.active").width(),
                s = 0;
            if ($(t).prevAll().each(function () {
                o += $(this).width()
            }), $(a).each(function () {
                $(this).hasClass("menu-control") || (s += $(this).width())
            }), o >= n) {
                var l = o - n + 128;
                $(e).animate({
                    marginLeft: "-" + l + "px"
                }, 0)
            }
            o != s && s >= n ? $(e).find(".menu-control.menu-control-right").addClass("show") : $(e).find(".menu-control.menu-control-right").removeClass("show"), o >= n && s >= n ? $(e).find(".menu-control.menu-control-left").addClass("show") : $(e).find(".menu-control.menu-control-left").removeClass("show")
        }
        $('[data-click="next-menu"]').click(function (a) {
            a.preventDefault(), e(this, "next")
        }), $('[data-click="prev-menu"]').click(function (a) {
            a.preventDefault(), e(this, "prev")
        }), $(window).resize(function () {
            $(".top-menu .nav").removeAttr("style"), a()
        }), a()
    },
    handleTopMenuSubMenu = function () {
        "use strict";
        $(".top-menu .sub-menu .has-sub > a").click(function () {
            var e = $(this).closest("li").find(".sub-menu").first(),
                a = $(this).closest("ul").find(".sub-menu").not(e);
            $(a).not(e).slideUp(250, function () {
                $(this).closest("li").removeClass("expand")
            }), $(e).slideToggle(250, function () {
                var e = $(this).closest("li");
                $(e).hasClass("expand") ? $(e).removeClass("expand") : $(e).addClass("expand")
            })
        })
    },
    handleMobileTopMenuSubMenu = function () {
        "use strict";
        $(".top-menu .nav > li.has-sub > a").click(function () {
            if ($(window).width() <= 767) {
                var e = $(this).closest("li").find(".sub-menu").first(),
                    a = $(this).closest("ul").find(".sub-menu").not(e);
                $(a).not(e).slideUp(250, function () {
                    $(this).closest("li").removeClass("expand")
                }), $(e).slideToggle(250, function () {
                    var e = $(this).closest("li");
                    $(e).hasClass("expand") ? $(e).removeClass("expand") : $(e).addClass("expand")
                })
            }
        })
    },
    handleTopMenuMobileToggle = function () {
        "use strict";
        $('[data-click="top-menu-toggled"]').click(function () {
            $(".top-menu").slideToggle(250)
        })
    },
    handleClearSidebarSelection = function () {
        $(".sidebar .nav > li, .sidebar .nav .sub-menu").removeClass("expand").removeAttr("style")
    },
    handleClearSidebarMobileSelection = function () {
        $("#page-container").removeClass("page-sidebar-toggled")
    },
    App = function () {
        "use strict";
        return {
            init: function () {
                this.initLocalStorage(), this.initSidebar(), this.initTopMenu(), this.initPageLoad(), this.initComponent(), this.initThemePanel(), handlePageScrollContentAnimation()
            },
            initSidebar: function () {
                /* handleSidebarMenu(),  handleMobileSidebarToggle(), */ handleSidebarMinify(), handleMobileSidebar()
            },
            initSidebarSelection: function () {
                handleClearSidebarSelection()
            },
            initSidebarMobileSelection: function () {
                handleClearSidebarMobileSelection()
            },
            initTopMenu: function () {
                handleUnlimitedTopMenuRender(), handleTopMenuSubMenu(), handleMobileTopMenuSubMenu(), handleTopMenuMobileToggle()
            },
            initPageLoad: function () {
                handlePageContentView()
            },
            initComponent: function () {
                handleDraggablePanel(), handleIEFullHeightContent(), handleSlimScroll(), handleUnlimitedTabsRender(), handlePanelAction(), handelTooltipPopoverActivation(), handleScrollToTopButton(), handleAfterPageLoadAddClass()
            },
            initLocalStorage: function () {
                handleLocalStorage()
            },
            initThemePanel: function () {
                handleThemePageStructureControl(), handleThemePanelExpand(), handleResetLocalStorage()
            },
            scrollTop: function () {
                $("html, body").animate({
                    scrollTop: $("body").offset().top
                }, 0)
            }
        }
    }();


// 테이블 위아래에 x-scroll
$(function(){
    const table_width = $(".main-table").css('width');
    console.log('table-width : ',table_width);
    $(".above-table").css('width', table_width);
    console.log('above_table-width : ',$(".above_table").css('width'));
    $(".table-wrapper1").scroll(function(){
        $(".table-wrapper2")
            .scrollLeft($(".table-wrapper1").scrollLeft());
    });
    $(".table-wrapper2").scroll(function(){
        $(".table-wrapper1")
            .scrollLeft($(".table-wrapper2").scrollLeft());
    });
});