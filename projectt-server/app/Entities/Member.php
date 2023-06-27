<?php

namespace App\Entities;

use CodeIgniter\Entity;

class Member extends Entity {

    protected $casts = [
        'idx' => 'int',
        'id' => 'string',
        'password' => 'string',
        'nick_name' => 'string',
        'call' => 'string',
        'is_recommend' => 'string',
        'status' => 'string',
        'level' => 'int',
        'MICRO' => 'string',
        'AG' => 'string',
        'recommend_code' => 'int',
        'recommend_member' => 'int',
        'account_number' => 'string',
        'account_name' => 'string',
        'account_bank' => 'string',
        'exchange_password' => 'string',
        'is_exchange' => 'string',
        'reg_first_charge' => 'string',
        'charge_first_per' => 'string',
        'money' => 'int',
        'point' => 'int',
        'betting_p' => 'int',
        'access_token' => 'string',
        'is_coin_guid' => 'string',
        'coin_password' => 'string',
        'is_betting_slip' => 'string',
        'birth' => 'string',
        'mobile_carrier' => 'int',
        'g_money' => 'int',
        'session_key' => 'string',
        'keep_login_access_token' => 'string',
        'is_holdem_register' => 'string',
        'is_holdem_start' => 'string',
        'confirm_call' => 'string'
    ];

    public function getIdx() {
        return $this->attributes['idx'];
    }

    public function getId() {
        return $this->attributes['id'];
    }

    public function getPassword() {
        return $this->attributes['password'];
    }

    public function getNickName() {
        return $this->attributes['nick_name'];
    }

    public function getUBusiness() {
        return $this->attributes['u_business'];
    }

    public function getCall() {
        return $this->attributes['call'];
    }

    public function setCall($call) {
        $this->attributes['call'] = $call;
    }

    public function getIsRecommend() {
        return $this->attributes['is_recommend'];
    }

    public function getStatus() {
        return $this->attributes['status'];
    }

    public function getLevel() {
        return $this->attributes['level'];
    }

    public function getMICRO() {
        return $this->attributes['MICRO'];
    }

    public function getAG() {
        return $this->attributes['AG'];
    }

    public function getRecommendCode() {
        return $this->attributes['recommend_code'];
    }

    public function getRecommendMember() {
        return $this->attributes['recommend_member'];
    }

    public function getAccountNumber() {
        return $this->attributes['account_number'];
    }

    public function setAccountNumber($value) {
        $this->attributes['account_number'] = $value;
    }

    public function getAccountName() {
        return $this->attributes['account_name'];
    }

    public function setAccountName($value) {
        $this->attributes['account_name'] = $value;
    }

    public function getAccountBank() {
        return $this->attributes['account_bank'];
    }

    public function getAdminMemo() {
        return $this->attributes['admin_memo'];
    }

    public function getMoney() {
        return $this->attributes['money'];
    }
    
    public function setMoney($money) {
        $this->attributes['money'] = $money;
    }

    public function getPoint() {
        return $this->attributes['point'];
    }

    public function getBettingP() {
        return $this->attributes['betting_p'];
    }

    public function getIsExchange() {
        return $this->attributes['is_exchange'];
    }

    public function getRegFirstCharge() {
        return $this->attributes['reg_first_charge'];
    }

    public function getChargeFirstPer() {
        return $this->attributes['charge_first_per'];
    }

    public function getAccess_token() {
        return $this->attributes['access_token'];
    }

    public function setAccess_token($access_token) {
        $this->attributes['access_token'] = $access_token;
    }

    public function getIsCoinGuid() {
        return $this->attributes['is_coin_guid'];
    }

    public function setIsCoinGuid($is_coin_guid) {
        $this->attributes['is_coin_guid'] = $is_coin_guid;
    }

    public function getCoinPassword() {
        return $this->attributes['coin_password'];
    }

    public function setCoinPassword($is_coin_guid) {
        $this->attributes['coin_password'] = $is_coin_guid;
    }

    public function getIsBettingSlip() {
        return $this->attributes['is_betting_slip'];
    }

    public function setIsBettingSlip($is_betting_slip) {
        $this->attributes['is_betting_slip'] = $is_betting_slip;
    }

    public function getBirth() {
        return $this->attributes['birth'];
    }

    public function setBirth($birth) {
        $this->attributes['birth'] = $birth;
    }

    public function getmobileCarrier() {
        return $this->attributes['mobile_carrier'];
    }

    public function setmobileCarrier($mobileCarrie) {
        $this->attributes['mobile_carrier'] = $mobileCarrie;
    }

    public function getGmoney() {
        return $this->attributes['g_money'];
    }

    public function setGmoney($g_money) {
        $this->attributes['g_money'] = $g_money;
    }

    public function getSessionKey() {
        return $this->attributes['session_key'];
    }
    
    public function setSessionKey($session_key) {
        $this->attributes['session_key'] = $session_key;
    }
    
     public function get_keep_login_access_token()
    {
        return $this->attributes['keep_login_access_token'];
    }
    
    public function set_keep_login_access_toke($keep_login_access_token)
    {
        $this->attributes['keep_login_access_token'] = $keep_login_access_token;
    }
    
    public function getIsHoldemRegister() {
        return $this->attributes['is_holdem_register'];
    }

    public function setIsHoldemRegister($is_holdem_register) {
        $this->attributes['is_holdem_register'] = $is_holdem_register;
    }
    
    public function getIsHoldemStart() {
        return $this->attributes['is_holdem_start'];
    }

    public function setIsHoldemStart($is_holdem_start) {
        $this->attributes['is_holdem_start'] = $is_holdem_start;
    }

    public function getConfirmCall() {
        return $this->attributes['confirm_call'];
    }

    public function setConfirmCall($confirm_call) {
        $this->attributes['confirm_call'] = $confirm_call;
    }
}
