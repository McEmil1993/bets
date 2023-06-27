<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');
include_once(_BASEPATH . '/common/_common_inc_class.php');
include_once(_DAOPATH . '/class_Admin_Common_dao.php');
include_once(_DAOPATH . '/class_Admin_Member_dao.php');

$UTIL = new CommonUtil();

$p_data['m_idx'] = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);

if ($p_data['m_idx'] < 1) {
    $UTIL->alertClose('회원정보가 없습니다.');
    exit;
}

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if ($db_conn) {

    $p_data['m_idx'] = $MEMAdminDAO->real_escape_string($p_data['m_idx']);
      
    $db_dataArr_msg_set = $MEMAdminDAO->getTemplateList(0); // 쪽지

    $p_data['sql'] = "select id, nick_name from member where idx=" . $p_data['m_idx'] . " ";
    $db_data_mem = $MEMAdminDAO->getQueryData($p_data);

    $MEMAdminDAO->dbclose();
}
?>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="ko">
    <!--<![endif]-->

<?php
include_once(_BASEPATH . '/common/head.php');
?>
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
    <script type="text/javascript" src="<?= _STATIC_COMMON_PATH ?>/js/admMsg.js?v=<?php echo date("YmdHis"); ?>" charset="utf-8"></script>

    <body>

        <div class="wrap">
            <form id="regform" name="regform" method="post">
                <input type="hidden" id="autonum" name="autonum">
                <input type="hidden" id="m_idx" name="m_idx" value="<?= $p_data['m_idx'] ?>">
                <!-- Contents -->
                <div class="">
                    <!-- list -->
                    <div class="panel reserve" style="min-width: 960px; padding: 10px;">
                        <i class="mte i_chat mte-2x vam"></i> <h4>쪽지 쓰기</h4>
                        <div class="tline">
                            <table class="mlist">
                                <tr>
                                    <th style="width: 150px; text-align:left">받는 사람</th>
                                    <td style="text-align:left"><?= $db_data_mem[0]['id'] ?> ( <?= $db_data_mem[0]['nick_name'] ?> )</td>
                                </tr>
                                <tr>
                                    <th style="width: 150px; text-align:left">제 목</th>
                                    <td>
                                        <div class="confing_box">
                                            <input type="text"  name="send_m_title" id="send_m_title" placeholder="제목을 입력해 주세요." />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 150px; text-align:left">템플릿 선택</th>
                                    <td>
                                        <select name="set_msg" id="set_msg" onchange="javascript:getSetMsg(this.value);" style="width: 100%">
                                            <option value="">-- 선택안함 --</option>
<?php
if (!empty($db_dataArr_msg_set)) {
    foreach ($db_dataArr_msg_set as $row) {
        ?>
                                                    <option value="<?= $row['idx'] ?>"><?= $row['title'] ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 150px; text-align:left">내 용</th>
                                    <td>
                                        <div id="loading"></div>
                                        <textarea name="b_content" id="b_content" rows="5" cols="100" style="width:780px; height:350px; display:none;"></textarea><br>
                                    </td>
                                </tr>

                            </table>                

                            <div style="height: 20px"></div>
                        </div>

                        <div class="panel_tit" style="text-align: center;">
                            <div class="clx modal_foot">
                                <a href="javascript:;" id="adm_btn_msg_send_pop" class="btn h30 btn_blu" data-dismiss="modal">쪽지 발송</a>
                                <a href="javascript:self.close();" class="btn h30 btn_mdark" data-dismiss="modal">닫기</a>
                            </div>
                        </div>
                    </div>

                    <!-- END list -->
                </div>
                <!-- END Contents -->
            </form>  
        </div>

        <script>
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
                    //예제 코드 //oEditors.getById["ir1"].exec("PASTE_HTML", ["로딩이 완료된 후에 본문에 삽입되는 text입니다."]); 
                }, fCreator: "createSEditor2"

            });

        </script>

    </body>
</html>