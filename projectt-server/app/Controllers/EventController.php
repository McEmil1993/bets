<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\NoticeModel;
use CodeIgniter\API\ResponseTrait;
use App\Util\CodeUtil;

class EventController extends BaseController {

    use ResponseTrait;

    public function index() {
        $member_idx = session()->get('member_idx');

        $chkMobile = CodeUtil::rtn_mobile_chk();

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

         if (false == session()->has('member_idx') || !isset($member_idx)) {
            $url = base_url("/$viewRoot/index");
            echo "<script>
        	alert('로그인 후 이용해주세요.');
        	window.location.href='$url';
        	</script>";
            return;
        }

        if (0 < session()->get('tm_unread_cnt')) {
            $url = base_url("/web/note");
            echo "<script>
            alert('미확인 쪽지를 확인 바랍니다.');
            window.location.href='$url';
            </script>";
            return;
        }

        $this->initMemberData(session(), session()->get('member_idx'));
        $page = isset($_POST['page']) ? $_POST['page'] - 1 : 0;
        if (!is_int((int)$page)) {
            $url = base_url("/web/note");
            echo "<script>
            alert('인자값 오류입니다.');
            window.location.href='$url';
            </script>";
            return;
        }

        $eventModel = new EventModel();
       
        $list = $eventModel
                ->where('status', 1)
                ->limit(10, $page * 10)
                ->orderBy('update_dt', 'desc')
                ->find();

        $noticeModel = new NoticeModel();
        $notice = $noticeModel
                ->where('status', 1)
                ->limit(10, $page * 10)
                ->orderBy('update_dt', 'asc')
                ->find();

        return view("$viewRoot/event", [
            'list' => $list,
            'notice' => $notice,
            'page' => $page
        ]);
    }

    // 이벤트 내용 상세보기
    public function viewEventDetail() {
        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        $idx = isset($_GET['idx']) ? $_GET['idx'] : 0;
        $type = isset($_GET['type']) ? $_GET['type'] : 0;

        if (!is_int((int)$idx) || !is_int((int)$type)) {
            $url = base_url("/web/note");
            echo "<script>
            alert('인자값 오류입니다.');
            window.location.href='$url';
            </script>";
            return;
        }
        
        // 0 : 공지, 1 : 이벤트
        //$viewFile = 'event';
        $viewFile = 'event_view';
        if ($type == 0) {
            $eventModel = new NoticeModel();
        } else {
            $eventModel = new EventModel();
        }

        $list = $eventModel
                ->where('idx', $idx)
                ->orderBy('idx', 'desc')
                ->find();

        return view("$viewRoot/$viewFile", [
            'type' => $type,
            'item' => $list[0]
        ]);
    }

}
