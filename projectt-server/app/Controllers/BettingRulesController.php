<?php

namespace App\Controllers;

use App\Models\BettingRulesModel;
use CodeIgniter\API\ResponseTrait;
use App\Util\CodeUtil;

class BettingRulesController extends BaseController {

    use ResponseTrait;

    public function index() {
        //$viewRoot = strpos($_SERVER['REQUEST_URI'], 'web/') > 0 ? 'web' : 'web';
        $chkMobile = CodeUtil::rtn_mobile_chk();

        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        if (0 < session()->get('tm_unread_cnt')) {
            //echo "<script>alert('미확인 쪽지를 확인 바랍니다.'); </script>";
            //return redirect()->to(base_url("/$viewRoot/betting_history?menu=d"));
        	$url = base_url("/web/note");
            echo "<script>
            alert('미확인 쪽지를 확인 바랍니다.');
            window.location.href='$url';
            </script>";
            return;
        }

        $bettingRulesModel = new BettingRulesModel();

        $sql = "SELECT * FROM base_rule ";
        $sql2 = "SELECT * FROM base_rule_2 ";

        $baseRule = $bettingRulesModel->db->query($sql)->getResultArray();
        $baseRule2 = $bettingRulesModel->db->query($sql2)->getResultArray();


        $sql_level_rule = "SELECT u_level, set_type, set_type_val FROM t_game_config";
        $result = $bettingRulesModel->db->query($sql_level_rule)->getResultArray();
        $level_rule = array();
        foreach ($result as $k => $v) {
            $level = $v['u_level'];
            $set_type = $v['set_type'];
            $level_rule[$set_type][$level] = $v['set_type_val'];
        }

        $sql_category_rule_real = "SELECT sub_category, content FROM category_rule WHERE category = 1";
        $sql_category_rule_sports = "SELECT sub_category, content FROM category_rule WHERE category = 2";
        $sql_category_rule_mini = "SELECT sub_category, content FROM category_rule WHERE category = 3";

        $result = $bettingRulesModel->db->query($sql_category_rule_real)->getResultArray();
        $category_rule_real = null;
        foreach ($result as $k => $v) {
            $category_rule_real[$v['sub_category']] = $v['content'];
        }

        $result = $bettingRulesModel->db->query($sql_category_rule_sports)->getResultArray();
        $category_rule_sports = null;
        foreach ($result as $k => $v) {
            $category_rule_sports[$v['sub_category']] = $v['content'];
        }

        $result = $bettingRulesModel->db->query($sql_category_rule_mini)->getResultArray();
        $category_rule_mini = null;
        foreach ($result as $k => $v) {
            $category_rule_mini[$v['sub_category']] = $v['content'];
        }

        return view("$viewRoot/betting_rules", [
            'baseRule' => $baseRule,
            'baseRule2' => $baseRule2,
            'level_rule' => $level_rule,
            'category_rule_real' => $category_rule_real,
            'category_rule_sports' => $category_rule_sports,
            'category_rule_mini' => $category_rule_mini
                ]
        );
    }

    public function bnf_lvl() {

        $viewRoot = strpos($_SERVER['REQUEST_URI'], 'web/') > 0 ? 'web' : 'web';

        if (0 < session()->get('tm_unread_cnt')) {
           $url = base_url("/web/note");
            echo "<script>
            alert('미확인 쪽지를 확인 바랍니다.');
            window.location.href='$url';
            </script>";
            return;
        }

        return view("$viewRoot/sub02a", [
        ]);
    }

}
