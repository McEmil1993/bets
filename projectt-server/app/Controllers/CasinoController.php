<?php

namespace App\Controllers;

use App\Game\KorGm;
use Cassandra\Date;
use App\Game\Casino;
use App\Util\CodeUtil;
use App\Models\MemberModel;
use App\Models\AccountModel;
use App\Models\TBettingInfo;
use App\Models\TLogCashModel;
use App\Models\TGameIdManager;
use App\Models\TGameConfigModel;
use App\Models\TProductIdManager;
use CodeIgniter\API\ResponseTrait;
use App\Models\TProductTypeManager;
use App\Models\TSTTSwitchManager;

class CasinoController extends BaseController {

    use ResponseTrait;

    public function enterProductType() {
        $this->logger->error(':::::::::::::::  enterProductType Info : ');
        $memberModel = new MemberModel();
        list($member, $errorMessage) = $this->isLogin($memberModel);
        if (null == $member) {
            $response['messages'] = $errorMessage;
            return $this->fail($response);
        }

        if (0 < session()->get('tm_unread_cnt')) {
            $response['messages'] = '미확인 쪽지를 확인 바랍니다.';
            return $this->fail($response);
        }

        $_POST = $memberModel->filterSanitize($_POST);
        $product_type_id = isset($_POST['product_type_id']) ? $_POST['product_type_id'] : 0;

        if (!CodeUtil::only_number($product_type_id) || 0 == $product_type_id || 10 < $product_type_id) {
            die();
        }

        list($retval, $message) = $this->checkServer($member, $product_type_id);

        if (false == $retval) {
            $response['messages'] = $message;
            return $this->fail($response);
        }

        $TSTTSwitchManager = new TSTTSwitchManager();

        $result = $TSTTSwitchManager->getCanUseData($product_type_id);
        $response = [
            'result_code' => 200,
            'data' => $response
        ];
        
        $this->logger->error(':::::::::::::::  enterProductType success : ' . json_encode($response));
        return $this->respond($response);
    }

    public function enter() {
        $this->logger->error(':::::::::::::::  enter Info : ');
        $memberModel = new MemberModel();
        list($member, $errorMessage) = $this->isLogin($memberModel);
        if (null == $member) {
            $response['messages'] = $errorMessage;
            return $this->fail($response);
        }

        /* if (0 < session()->get('tm_unread_cnt')) {
          $response['messages'] = '미확인 쪽지를 확인 바랍니다.';
          return $this->fail($response);
          } */

        $chkMobile = CodeUtil::rtn_mobile_chk();
        $viewRoot = "PC" == $chkMobile ? 'web' : 'web';

        $_POST = $memberModel->filterSanitize($_POST);
        $product_group_id = isset($_POST['product_group_id']) ? $_POST['product_group_id'] : 0;
        $product_type_id = isset($_POST['product_type_id']) ? $_POST['product_type_id'] : 0;
        $provider_id = isset($_POST['provider_id']) ? $_POST['provider_id'] : 0;

        if (!CodeUtil::only_number($product_group_id) || !CodeUtil::only_number($product_type_id) || !CodeUtil::only_number($provider_id)) {
            die();
        }

        list($retval, $message) = $this->checkServer($member, $product_type_id);

        if (false == $retval) {
            $response['messages'] = $message;
            return $this->fail($response);
        }

        switch ($provider_id) {
            case CONSTANTS_KORGM:

                $this->logger->error(':::::::::::::::  enter new KorGm : ');
                $korGm = new KorGm($this->logger);
                list($status, $lunch_url, $errorMessage) = $korGm->auth($member, $product_group_id, $product_type_id, $provider_id, $chkMobile, $memberModel, $this->logger);
                if (0 == $status) {
                    $this->logger->error('CasinoController enter : ' . $errorMessage);
                    $response['messages'] = $errorMessage;
                    return $this->fail($response);
                }

                $response = [
                    'result_code' => 200,
                    'provider_id' => $provider_id,
                    'data' => [
                        'lunch_url' => $lunch_url
                    ]
                ];

                $this->logger->error(':::::::::::::::  enter new success KorGm : ' . json_encode($response));
                return $this->respond($response);

                break;

            default:
                break;
        }
    }

    public function productsList() {

        $memberModel = new MemberModel();
        $_POST = $memberModel->filterSanitize($_POST);
        //$product_type_id = isset($_POST['product_type_id']) ? $_POST['product_type_id'] : 0;
        $provider_id = isset($_POST['provider_id']) ? $_POST['provider_id'] : 0;

        if (!CodeUtil::only_number($provider_id)) {
            die();
        }

        $getProductType = (new TSTTSwitchManager)->where('provider_id', $provider_id)->find();

        switch ($provider_id) {
            case CONSTANTS_KORGM:
                $korGm = new KorGm($this->logger);
                foreach ($getProductType as $productTypes) {
                    $response = $korGm->getKgDataList('/game/products?type=' . $productTypes['product_type_name']);
                    if ($response->status == 0) {
                        $this->logger->error('CasinoController productsList : ' . $response->message);
                        return $this->fail($response->message);
                    }

                    foreach ($response->data as $products) {
                        $product_group_id = 0;
                        if (PRODUCT_TYPE_CASINO == $productTypes['product_type_id']) {
                            if (1 == $products->productId) {
                                $product_group_id = 1;
                            } else if (2 == $products->productId) {
                                $product_group_id = 2;
                            } else if (7 == $products->productId) {
                                $product_group_id = 3;
                            } else if (8 == $products->productId) {
                                $product_group_id = 4;
                            } else if (9 == $products->productId) {
                                $product_group_id = 5;
                            } else if (10 == $products->productId) {
                                $product_group_id = 6;
                            } else if (13 == $products->productId) {
                                $product_group_id = 7;
                            }
                        } else if (PRODUCT_TYPE_SLOT == $productTypes['product_type_id']) {
                            if (100 == $products->productId) {
                                $product_group_id = 100;
                            } else if (104 == $products->productId) {
                                $product_group_id = 101;
                            } else if (106 == $products->productId) {
                                $product_group_id = 102;
                            } else if (107 == $products->productId) {
                                $product_group_id = 103;
                            } else if (103 == $products->productId) {
                                $product_group_id = 104;
                            } else if (124 == $products->productId) {
                                $product_group_id = 105;
                            } else if (125 == $products->productId) {
                                $product_group_id = 106;
                            } else if (127 == $products->productId) {
                                $product_group_id = 107;
                            } else if (130 == $products->productId) {
                                $product_group_id = 108;
                            } else if (133 == $products->productId) {
                                $product_group_id = 109;
                            }
                        }
                        (new TProductIdManager)->replace([
                            'product_id' => $products->productId,
                            'product_short_name' => $products->shortName,
                            'desc' => $products->shortName,
                            'is_use' => ($products->productStatus) ? 'ON' : 'OFF',
                            'status' => $products->productStatus,
                            'product_name_en' => $products->namesSet->en,
                            'product_name_kr' => $products->namesSet->ko,
                            'product_group_id' => $product_group_id,
                            'product_type_id' => $productTypes['product_type_id'],
                            'provider_id' => $provider_id,
                                ], true);
                    }
                }
                return $this->respond([
                            'result_code' => 200, // Success
                            'message' => "Success",
                ]);
                
            case CONSTANTS_SLOTBANK:    {
            
                
            }
                   break;
            default:
                break;
        }
    }

    public function gamesList() {
        $memberModel = new MemberModel();
        $tProductIdManager = new TProductIdManager();
        $tGameIdManager = new TGameIdManager();

        $_POST = $memberModel->filterSanitize($_POST);
        $provider_id = isset($_POST['provider_id']) ? $_POST['provider_id'] : 0;
        $product_type_id = isset($_POST['product_type_id']) ? $_POST['product_type_id'] : null;
        $product_group_id = isset($_POST['product_group_id']) ? $_POST['product_group_id'] : null;

        if (!CodeUtil::only_number($provider_id) || !CodeUtil::only_number($product_type_id) || !CodeUtil::only_number($product_group_id)) {
            return $this->fail('No Record found', 400);
        }

        if ($product_group_id && $product_group_id > 0) {
            $getProductTypeName = (new TProductIdManager)->where('product_type_id', $product_type_id)->first();
            $productTypeName = null;
            if ($getProductTypeName) {
                $productTypeName = $getProductTypeName['product_type_name'];
            }
            switch ($provider_id) {
                case CONSTANTS_KORGM:
                    $korGm = new KorGm($this->logger);
                    $getProductIdManager = $tProductIdManager->where('provider_id', $provider_id)->where('product_group_id', $product_group_id);
                    if ($getProductIdManager->countAllResults() && $productTypeName) {
                        $getProductIdManager->chunk(5, function ($aProviderProducts) use ($provider_id, $korGm, $tGameIdManager, $productTypeName) {
                            $response = $korGm->getKgDataList('/game/gamesList?type=' . $productTypeName . '&productId=' . $aProviderProducts['product_id']);
                            if ($response->status == 0) {
                                $this->logger->error('CasinoController gamesList : ' . $response->message);
                                return $this->fail($response->message);
                            }
                            foreach ($response->data as $games) {
                                $tGameIdManager->replace([
                                    'game_id' => $games->gameId,
                                    'gamet_name_en' => $games->namesSet->en,
                                    'game_name_kr' => $games->namesSet->ko,
                                    'status' => $games->gameStatus,
                                    'product_type_id' => $aProviderProducts['product_type_id'],
                                    'product_id' => $aProviderProducts['product_id'],
                                    'provider_id' => $provider_id,
                                        ], true);
                            }
                        }
                        );

                        return $this->respond([
                                    'status' => 200,
                                    'message' => 'Success',
                        ]);
                    }
                    return $this->fail('No Record found', 400);
                /* todo: change $productTypeName value if product type name is not match e.g 'casinon'  */
                default:
                    break;
            }
        }
        return $this->fail('Product Group Id is required.', 400);
    }

    public function callback() {
        $uri = current_url(true);
        $provider_name = $uri->getSegment(3);
        $function_name = $uri->getSegment(4);
        // http://evo.com:8080/casino/callback/korgm
        $this->logger->info('join : callback url' . $uri);

        $__rawBody = file_get_contents("php://input"); // 본문을 불러옴
        $__getData = json_decode($__rawBody, true);
        $this->logger->info(':::::::::::::::  call_back request : ' . json_encode($__getData));

        $casinoObj = null;
        switch ($provider_name) {
            case 'kplay':
                //$casinoObj = new Casino(config(App::class)->ApiUrl, config(App::class)->AgToken, config(App::class)->AgCode, config(App::class)->SercetKey, $this->logger);
                break;
            case 'korgm':
                $casinoObj = new KorGm($this->logger);
                break;
            default:
                break;
        }

        if (null == $casinoObj) {
            $response = [
                'status' => 0,
                'error' => 'UNKNOWN_ERROR'
            ];
            $this->logger->error('callback response : ' . json_encode($response, JSON_UNESCAPED_UNICODE));
            $this->logger->error('callback fail provider_name : ' . $provider_name);
            return;
        }

        $memberModel = new MemberModel();
        $memberModel->db->transStart();
        $userId = $__getData['userId'];

        list($member, $message) = $casinoObj->isLoginById($userId, $function_name, $memberModel);
        if (null == $member) {
            $memberModel->db->transRollback();
            $response = [
                'status' => 0,
                'error' => 'INVALID_USER'
            ];
            $this->logger->error('callback response : ' . json_encode($response, JSON_UNESCAPED_UNICODE));
            return json_encode($response, JSON_UNESCAPED_UNICODE);
        }


        if (false == $this->checkServer($member, LGTB_CASINO)) {
            $memberModel->db->transRollback();
            $response = [
                'status' => 0,
                'error' => 'UNKNOWN_ERROR'
            ];
            $this->logger->error('callback response : ' . json_encode($response, JSON_UNESCAPED_UNICODE));
            return json_encode($response, JSON_UNESCAPED_UNICODE);
        }

        list($retCode, $result) = $casinoObj->processRequest($member, $memberModel, $function_name, $__getData, $this->logger);
        if (0 == $retCode) {
            $memberModel->db->transRollback();
            $response = [
                'status' => $retCode,
                'error' => $result
            ];
        } else {
            $memberModel->db->transComplete();
            $response = [
                'status' => $retCode,
                'balance' => (double) $result
            ];
        }

        $this->logger->info('callback response : ' . json_encode($response, JSON_UNESCAPED_UNICODE));
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function orderStatus() {
        $memberModel = new MemberModel();
        $korGm = new KorGm($this->logger);
        $TBettingInfo = new TBettingInfo();

        $getPendingBets = (object) $TBettingInfo->getPendingBets();
        foreach ($getPendingBets as $pendingBets) {
            $userId = $pendingBets['member_idx'];
            $memberModel->db->transStart();

            $response = $korGm->getKgDataList('/game/orderStatus?productId=' . $pendingBets['product_id'] . '&transactionId=' . $pendingBets['trx_id']);
            if ($response->status == 0) {
                $this->logger->error('CasinoController orderStatus : ' . $response->message);
                return $this->fail($response->message);
            }
            $requestData = [
                'productId' => $pendingBets['product_id'],
                'transactionId' => $pendingBets['trx_id'],
                'transactionAmount' => $response->payout,
                'gameId' => $pendingBets['game_id'],
                'roundId' => $pendingBets['round_id'],
            ];

            $function_name = null;
            if ($response->transactionState == 1) { /* Settled <Credit> */
                $function_name = 'credit';
            } elseif ($response->transactionState == 2) { /* Settled <Cancel/Refund> */
                $function_name = 'cancel';
            }

            if ($function_name != null) {
                $member = $memberModel->setMemberWhereId($userId);
                list($retCode, $result, $message) = $korGm->$function_name($member, $requestData, $memberModel);
                if (0 == $retCode) {
                    $memberModel->db->transRollback();
                } else {
                    $memberModel->db->transComplete();
                }
            }
        }

        return $this->respond([
                    'result_code' => 200, // Success
                    'message' => $getPendingBets,
        ]);
    }

    public function transactions() {
        $memberModel = new MemberModel();

        $_POST = $memberModel->filterSanitize($_POST);
        $startDate = isset($_POST['startDate']) ? date("Y-m-d H:i:s", strtotime($_POST['startDate'])) : null;
        $endData = isset($_POST['endData']) ? date("Y-m-d H:i:s", strtotime($_POST['endData'])) : null;

        $korGm = new KorGm($this->logger);
        $response = $korGm->getKgDataList('/game/transactions?startDate=' . $startDate . '&endDate=' . $endData);

        if ($response->status == 0) {
            $this->logger->error('CasinoController transactions : ' . json_encode($response, JSON_UNESCAPED_UNICODE));

            return $this->fail($response->message);
        }

        return $this->respond($response);
    }

}
