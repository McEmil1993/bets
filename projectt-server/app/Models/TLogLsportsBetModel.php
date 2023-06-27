<?php

namespace App\Models;

use App\Util\CodeUtil;
use CodeIgniter\Model;

class TLogLsportsBetModel extends Model {

    protected $DBGroup = 'log_db';
    protected $table = 'bet_log_data_prematch';
    protected $primaryKey = 'create_dt,bet_id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'create_dt',
        'bet_id',
        'fixture_id',
        'markets_id',
        'bet_base_line',
        'bet_name',
        'bet_line',
        'bet_price',
        'tempPrice',
        'bet_status',
        'last_update_provider',
        'refund_rate',
        'kstStartDate',
        'providers'
    ];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function do_insert_bet_query($sql, $logger) {
        try {
            
            $count = count($sql);
            
            $logger->error('do_insert_bet_query count : ' . $count);
            
            $total_count = 0;
            $_s = array_chunk($sql, 5000);
            //$_s = array_chunk($sql, 1);
            $query_string = '';
            foreach ($_s as $key => $data) {
                $query_string = 'INSERT INTO `log_db`.`bet_log_data_prematch` ('
                        . 'bet_id, '
                        . 'sports_id, '
                        . 'league_id, '
                        . 'fixture_id, '
                        . 'markets_id, '
                        . 'bet_base_line, '
                        . 'bet_name, '
                        . 'bet_line, '
                        . 'bet_price, '
                        . 'tempPrice, '
                        . 'bet_status, '
                        . 'last_update_provider, '
                        . 'refund_rate, '
                        . 'kstStartDate, '
                        . 'providers
                        ) VALUES '
                        . implode(',', $data);

                $this->db->query($query_string);

                //break;
            }
        } catch (\mysqli_sql_exception $e) {
            $logger->error('do_insert_bet_query [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $logger->error('do_insert_bet_query [MYSQL EXCEPTION] message query : ' . $this->getLastQuery());
        } 
    }

    public function do_insert_bet_real_query($sql, $logger) {
        try {
            $_s = array_chunk($sql, 5000);
            //$_s = array_chunk($sql, 1);
            $query_string = '';
            foreach ($_s as $key => $data) {
                $query_string = 'INSERT INTO `log_db`.`bet_log_data_real` ('
                        . 'bet_id, '
                        . 'sports_id, '
                        . 'league_id, '
                        . 'fixture_id, '
                        . 'markets_id, '
                        . 'bet_base_line, '
                        . 'bet_name, '
                        . 'bet_line, '
                        . 'bet_price, '
                        . 'tempPrice, '
                        . 'bet_status, '
                        . 'last_update_provider, '
                        . 'refund_rate, '
                        . 'kstStartDate, '
                        . 'providers
                        ) VALUES '
                        . implode(',', $data);

                $this->db->query($query_string);

                //break;
            }
        } catch (\mysqli_sql_exception $e) {
            $logger->error('do_insert_bet_real_query [MYSQL EXCEPTION] message (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')');
            $logger->error('do_insert_bet_real_query [MYSQL EXCEPTION] message query : ' . $this->getLastQuery());
        } 
    }

}
