<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Bbs_dao.php');

$UTIL = new CommonUtil();

if(0 != $_SESSION['u_business']){
    die();
}
//////// login check start
include_once(_BASEPATH . '/common/login_check.php');
//////// login check end

$BdsAdminDAO = new Admin_Bbs_DAO(_DB_NAME_WEB);
$db_conn = $BdsAdminDAO->dbconnect();

if ($db_conn) {
    if(false === GameCode::checkAdminType($_SESSION,$BdsAdminDAO)){
        die();
    }
    
    $idx = $BdsAdminDAO->real_escape_string($_GET['idx']);
    $p_data['sql'] = "SELECT a.idx, a.type, a.division, a.title, a.content, a.update_dt FROM template AS a WHERE idx = $idx";
    $db_dataArr = $BdsAdminDAO->getQueryData($p_data);

    $BdsAdminDAO->dbclose();
}
?>

<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="ko">
    <!--<![endif]-->

    <?php
    include_once(_BASEPATH . '/common/head.php');
    ?>

    <link rel="stylesheet" href="<?= _STATIC_COMMON_PATH ?>/docsupport/prism.css">
    <link rel="stylesheet" href="<?= _STATIC_COMMON_PATH ?>/docsupport/chosen.css">

    <script>
        $(document).ready(function () {
            App.init();
            FormPlugins.init();

            $('ul.tabs li').click(function () {
                var tab_id = $(this).attr('data-tab');

                $('ul.tabs li').removeClass('current');
                $('.tab-content').removeClass('current');

                $(this).addClass('current');
                $("#" + tab_id).addClass('current');
            })
        });
    </script>

    <script type="text/javascript" src="/smarteditor28/js/HuskyEZCreator.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?= _STATIC_COMMON_PATH ?>/js/admMsg.js" charset="utf-8"></script>

    <body>
        <div class="wrap">
            <?php
            $menu_name = "board_menu_5";

            include_once(_BASEPATH . '/common/left_menu.php');
            include_once(_BASEPATH . '/common/iframe_head_menu.php');
            ?>
            <!-- Contents -->
            <div class="con_wrap">
                <form id="regform" name="regform" method="post">
                    <input type="hidden" id="autonum" name="autonum">
                    <div class="title">
                        <a href="">
                            <i class="mte i_group mte-2x vam"></i>
                            <h4>템플릿 상세/수정</h4>
                        </a>
                    </div>

                    <!-- list -->
                    <div class="panel reserve">
                        <div class="tline">
                            <table class="mlist">
                                <input type="hidden" name="idx" id="idx" value="<?php echo $db_dataArr[0]['idx'] ?>">
                                <tr>
                                    <th style="width: 150px; text-align:left">구분</th>
                                    <td>
                                        <select name="type" id="type" style="width: 100%">
                                            <option value=0 <?php if ($db_dataArr[0]['type'] == 0) {
                echo 'selected';
            } ?>>쪽지</option>
                                            <option value=1 <?php if ($db_dataArr[0]['type'] == 1) {
                echo 'selected';
            } ?>>답변</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 150px; text-align:left">분 류</th>
                                    <td><input type="text" name="division" id="division" style="width:780px; height:30px;" value="<?= $db_dataArr[0]['division'] ?>"></td>
                                </tr>
                                <tr>
                                    <th style="width: 150px; text-align:left">제 목</th>
                                    <td>
                                        <div class="confing_box">
                                            <input type="text"  name="send_m_title" id="send_m_title" placeholder="제목을 입력해 주세요." value="<?= $db_dataArr[0]['title'] ?>" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 150px; text-align:left">내 용</th>
                                    <td>
                                        <div id="loading"></div>
                                        <textarea name="b_content" id="b_content" rows="5" cols="100" style="width:780px; height:350px; display:none;">
<?= $db_dataArr[0]['content'] ?>
                                        </textarea><br>
                                    </td>
                                </tr>
                            </table>                

                            <div style="height: 20px"></div>
                        </div>

                        <div class="panel_tit">
                            <div align="center">
                                <a href="javascript:;" id="adm_btn_notice_send" class="btn h30 btn_green" style="color: white">수정</a>
                                <a href="/board_w/template_list.php" id="adm_btn_notice_cancel" class="btn h30 btn_green" style="color: white">목록</a>
                            </div>
                        </div>
                    </div>
                    <!-- END list -->
                    <script type="text/javascript" src="<?= _STATIC_COMMON_PATH ?>/docsupport/chosen.jquery.js"></script>
                    <script type="text/javascript" src="<?= _STATIC_COMMON_PATH ?>/docsupport/prism.js" charset="utf-8"></script>
                    <script type="text/javascript" src="<?= _STATIC_COMMON_PATH ?>/docsupport/init.js" charset="utf-8"></script>
                </form>        
            </div>
            <!-- END Contents -->
        </div>
<?php
include_once(_BASEPATH . '/common/bottom.php');
?> 
        <script>
        $(document).ready(function () {
            $("#adm_btn_notice_send").click(function () {
                var str_msg = '수정 하시겠습니까?';
                var idx = $("#idx").val();
                var msg_title = $("#send_m_title").val();

                // 구분
                var type = $('#type').val();

                // 분류 체크
                var division = $('#division').val();

                // 제목 길이 체크
                if (msg_title.length < 3) {
                    alert('제목을 입력해 주세요.');
                    $('#send_m_title').select();
                    $('#send_m_title').focus();
                    return;
                }

                oEditors.getById["b_content"].exec("UPDATE_CONTENTS_FIELD", []);

                var msg_content = $("#b_content").val();
                //alert(msg_content);
                //var url_bcontent = encodeURIComponent(msg_content);
                //alert(url_bcontent);

                if (msg_content.length < 3) {
                    alert('내용을 입력해 주세요.');
                    $('#b_content').select();
                    $('#b_content').focus();
                    return;
                }

                var result = confirm(str_msg);
                if (result) {
                    var prctype = "reg";

                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: '/board_w/_template_prc_update.php',
                        data: {'idx': idx, 'type': type, 'division': division, 'msg_title': msg_title, 'msg_content': msg_content},
                        success: function (result) {
                            if (result['retCode'] == "1000") {
                                alert('수정하였습니다.');
                                location.replace('/board_w/template_list.php');
                                return;
                            } else {
                                alert(result['retMsg']);
                                return;
                            }
                        },
                        error: function (request, status, error) {
                            alert('수정에 실패하였습니다(1).');
                            return;
                        }
                    });
                } else {
                    return;
                }
            });
        });

        function resize(obj) {
            obj.style.height = "1px";
            obj.style.height = (12 + obj.scrollHeight) + "px";
        }

        var oEditors = [];

        nhn.husky.EZCreator.createInIFrame({
            oAppRef: oEditors,
            elPlaceHolder: "b_content",
            sSkinURI: "/smarteditor28/SmartEditor2Skin.html",
            htParams: {
                bUseToolbar: true, // 툴바 사용 여부 (true:사용/ false:사용하지 않음)
                bUseVerticalResizer: true, // 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
                bUseModeChanger: true, // 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
                //bSkipXssFilter : true,		// client-side xss filter 무시 여부 (true:사용하지 않음 / 그외:사용)
                //aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
                fOnBeforeUnload: function () {
                    //alert("완료!");
                },
                SE_EditingAreaManager: {
                    //sDefaultEditingMode : 'HTMLSrc'
                }
            }, //boolean
            fOnAppLoad: function () {
                //예제 코드 
                //oEditors.getById["b_content"].exec("PASTE_HTML", ["로딩이 완료된 후에 본문에 삽입되는 text입니다."]); 

            }, fCreator: "createSEditor2"

        });

        // oEditors.getById["b_content"].exec("SET_IR",[""]);
        // oEditors.getById["b_content"].exec("PASTE_HTML",["test"]);
        </script>
    </body>
</html>
