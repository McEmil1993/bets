<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_LIB/base_config.php');

include_once(_BASEPATH . '/common/_common_inc_class.php');

include_once(_DAOPATH . '/class_Admin_Common_dao.php');

include_once(_DAOPATH . '/class_Admin_Member_dao.php');

include_once(_LIBPATH . '/class_redis.php');

$UTIL = new CommonUtil();

$selContent = trim(isset($_REQUEST['selContent']) ? $_REQUEST['selContent'] : 1);

$p_data['m_idx'] = trim(isset($_REQUEST['m_idx']) ? $_REQUEST['m_idx'] : 0);
$page = trim(isset($_REQUEST['page']) ?$_REQUEST['page'] : 1);

if ($p_data['m_idx'] < 1) {
    $UTIL->alertClose('회원정보가 없습니다.');
    exit;
}

$db_m_idx = $p_data['m_idx'];

$accessLogRedis = new credis(REDIS_IP, REDIS_PORT, REDIS_PASSWORD, REDIS_DATABASE, REDIS_EXPIRE);
if(!$accessLogRedis->connect()){
    //$UTIL->alertClose('회원정보가 없습니다.');
    echo '레디스 연결 실패.';
    exit;
}

$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
if ($p_data['page'] < 1) {
    $p_data['page'] = 1;
}

$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : _NUM_PER_PAGE);
if ($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = _NUM_PER_PAGE;
}

$total_cnt = $accessLogRedis->llen($db_m_idx);

$p_data['page_per_block'] = _B_BLOCK_COUNT;
//$p_data['start'] = ($p_data['page'] - 1) * $p_data['num_per_page'];

$p_data['start'] = -1 * (($p_data['page']) * $p_data['num_per_page']);
$p_data['end'] = $p_data['start'] + $p_data['num_per_page'] - 1;

$total_cnt = $accessLogRedis->llen($db_m_idx);
//$redis_dataArr = $accessLogRedis->lrange($db_m_idx, $p_data['start'], $p_data['start'] + $p_data['num_per_page']);
$redis_dataArr = $accessLogRedis->lrange($db_m_idx, $p_data['start'], $p_data['end']);
$redis_dataArr = array_reverse($redis_dataArr);

$total_page = ceil($total_cnt / $p_data['num_per_page']);        // 페이지 수
$total_block = ceil($total_page / $p_data['page_per_block']);     // 총 블럭 수
$block = ceil($p_data['page'] / $p_data['page_per_block']); // 현재 블럭
$first_page = ($p_data['page_per_block'] * ($block - 1)) + 1;       // 첫번째 페이지
$last_page = $p_data['page_per_block'] * $block;       // 마지막 페이지

if ($block >= $total_block)
    $last_page = $total_page;

$reqFile = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
$default_link = "$reqFile?m_idx=" . $p_data['m_idx'];
?>

<html lang="ko">

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
    <script type="text/javascript" src="<?= _STATIC_COMMON_PATH ?>/js/admMsg.js" charset="utf-8"></script>
    <script src="<?= _STATIC_COMMON_PATH ?>/js/admCommon.js"></script>
    <body>
        <div class="wrap">

                <div class="">
                    <!-- list -->
                    <div class="panel reserve" style="min-width: 960px; padding: 10px;">
                        <i class="mte i_group mte-2x vam"></i> <h4>회원 접속 로그</h4>
                        <!--<span style="float:right">
                            <a href="javascript:;" class="btn h30 btn_blu" onClick="popupWinPost('/member_w/pop_msg_write.php','popmsg',660,1000,'msg','<?//=$db_m_idx?>');">쪽지</a>
                            <a href="javascript:;" onclick="" class="btn h30 btn_blu">중복베팅 등록</a>
                            <a href="javascript:self.close();" class="btn h30 btn_mdark" style="color:#fff">닫 기</a>
                        </span>-->
                        <div class="tline">
                            <table class="mlist">
                                <tr>
                                    <th>레벨</th>
                                    <th>아이디</th>
                                    <th>닉네임</th>
                                    <th>정산(입-출)</th>
                                    <th>접속유형</th>
                                    <th>페이지정보</th>
                                    <th>페이지주소</th>
                                    <th>접근경로</th>
                                    <th>IP</th>
                                    <th>접근시간</th>
                                </tr>
                        <?php
                            if ($total_cnt > 0) {

                                $i = 0;
                                if (!empty($redis_dataArr)) {
                                    foreach ($redis_dataArr as $value) {
                                        $row = json_decode($value, true);

                                        if ($row['level'] == 9) {
                                            $row['gs'] = 0;
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $row['level'] ?></td>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= $row['nickName'] ?></td>
                                            <td style='text-align:right'><?= isset($row['cal']) ? number_format($row['cal']) : 0 ?></td>
                                            <td><?= $row['access_type'] ?></td>
                                            <td><?= '' == $row['page'] ? 'main':$row['page'] ?></td>
                                            <td><?= $row['path'] ?></td>
                                            <td><?= $row['hostname'] ?></td>
                                            <td><?= $row['client_ip'] ?></td>
                                            <td><?= $row['now_time'] ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                }
                            } else {
                                ?>
                                <tr><td colspan="10">데이터가 없습니다.</td></tr>
                                <?php
                            }
                            ?>
                                </tr>
                            </table>
                            <?php
                            include_once(_BASEPATH . '/common/page_num.php');
                            ?>  
                        </div>
                    </div>

                    <!-- END list -->
                </div>
                <!-- END Contents -->
            </form>  
        </div>

    </body>
</html>
<script>
    /*const getPageInfo = function (page) {
        let info = page;
        if('' == page){
            info ='메인';
        }
        
        switch(page){
            case 'customer_service':
                info = '고객센터';
            case 'minigame':
                info = '파워볼';
            case 'powerladder':
                info = '파워사다리';
            case 'kinoadder':
                info = '키노사다리';
        }
        return;
    }*/
</script>