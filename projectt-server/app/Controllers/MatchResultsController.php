<?php namespace App\Controllers;

use App\Models\GameModel;
use CodeIgniter\API\ResponseTrait;

class MatchResultsController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $viewRoot = strpos($_SERVER['REQUEST_URI'],'web/') > 0 ? 'web' : 'web';
        $this->initMemberData(session(), session()->get('member_idx'));
        return view("$viewRoot/sub07");
    }
}
