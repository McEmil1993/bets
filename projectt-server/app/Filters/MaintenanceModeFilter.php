<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class MaintenanceModeFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        switch (service('uri')->getPath()) {
            case('web/casino'):
                $setType = 'service_casino';
                break;
            case('web/slot'):
                $setType = 'service_slot';
                break;
        }

        if ($setType) {
            $db = db_connect();
            $query = $db->query("SELECT set_type, set_type_val FROM t_game_config WHERE set_type = ?", [$setType]);
            $row = $query->getRow();
            if ($row && $row->set_type_val === 'Y') {
                return \Config\Services::response()->setBody(view('web/maintenance_mode')); 
            }
            $db->close();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
