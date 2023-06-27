<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AvailabilityFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $memberIdx = session()->get('member_idx');

        switch (service('uri')->getPath()) {
            case('web/casino'):
                $gameType = 9;
                break;
            case('web/slot'):
                $gameType = 10;
                break;
        }

        if ($memberIdx && $gameType) {
            $db = db_connect();
            $query = $db->query("SELECT * FROM member_game_type WHERE member_idx = ? AND game_type = ?", [$memberIdx, $gameType]);
            $row = $query->getRow();
            if ($row && $row->status === 'OFF') {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
            $db->close();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
