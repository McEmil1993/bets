<?php

namespace App\Controllers;

use App\Models\GameModel;
use App\Models\MemberBetDetailModel;
use App\Models\MemberModel;
use App\Models\MemberMoneyExchangeHistoryModel;
use App\Models\LSportsFixturesModel;
use App\Models\LSportsSportsModel;
use CodeIgniter\API\ResponseTrait;
use App\Util\PullOperations;
use CodeIgniter\Log\Logger;
use App\Util\CodeUtil;

class PublisherController extends BaseController {

    use ResponseTrait;

    // public function index() {
    //     return view("web/index", []);
    // }
    // public function layout() {
    //     return view("web/layout", []);
    // }
    // public function inspection() {
    //     return view("web/inspection", []);
    // }


    public function realtime() {
        // $member_idx = session()->get('member_idx');
        // $chkMobile = CodeUtil::rtn_mobile_chk();

        // $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        return view("web/realtime", []);
    }

    public function sports() {
        // $member_idx = session()->get('member_idx');
        // $chkMobile = CodeUtil::rtn_mobile_chk();

        // $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        return view("web/sports", []);
    }

    // return view("$viewRoot/casino_list", [
    //     'prd_type' => $prd_type,
    //     'gameList' => $gameList,
    //     'prodList' => $prodList
    // ]);


    public function classic() {
        return view("web/classic", []);
    }

}
