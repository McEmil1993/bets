package com.soas.dao.impl;

import com.soas.dao.MysqlMapper;
import org.springframework.stereotype.Repository;

import java.util.HashMap;
import java.util.List;
import java.util.Map;


@Repository
public class BettingDAO extends MysqlMapper{


    public void insertCasinoBet (Map<String,Object> params) {
        insert("BettingMapper.insertCasinoBet", params);
    }


    public void insertCasinoBonus (Map<String,Object> params) {
        insert("BettingMapper.insertCasinoBonus", params);
    }


    public void insertSlotBet (Map<String,Object> params) {
        insert("BettingMapper.insertSlotBet", params);
    }


    public void insertSlotBonus (Map<String,Object> params) {
        insert("BettingMapper.insertSlotBonus", params);
    }


    public void insertEsptBet (Map<String,Object> params) {
        insert("BettingMapper.insertEsptBet", params);
    }


    public void insertEsptBonus (Map<String,Object> params) {
        insert("BettingMapper.insertEsptBonus", params);
    }


    public void updateCasinoBet (Map<String,Object> params) {
        update("BettingMapper.updateCasinoBet", params);
    }


    public void updateSlotBet (Map<String,Object> params) {
        update("BettingMapper.updateSlotBet", params);
    }


    public void updateEsptBet (Map<String,Object> params) {
        update("BettingMapper.updateEsptBet", params);
    }


    public List<Map<String,Object>> selectDelayBetList (Map<String,Object> params) {
        return selectList("BettingMapper.selectDelayBetList", params);
    }

    public void updateLastDepositBet (Map<String,Object> params) {
        update("BettingMapper.updateLastDepositBet", params);
    }

    public void updateBetPlus (Map<String,Object> params) {
        update("BettingMapper.updateBetPlus", params);
    }

    public void insertHashBet (Map<String,Object> params) {
        insert("BettingMapper.insertHashBet", params);
    }

    public void updateHashBet (Map<String,Object> params) {
        update("BettingMapper.updateHashBet", params);
    }
    public void updateLoseHashBet (Map<String,Object> params) {
        update("BettingMapper.updateLoseHashBet", params);
    }

    public void insertCashLog (Map<String,Object> params) {
        insert("BettingMapper.insertCashLog", params);
    }

    public Map<String,Object> selectKplayBet (Map<String,Object> params) {
        return selectOne("BettingMapper.selectKplayBet", params);
    }


    public Integer  selectHLHashBet(Map<String,Object> params) {
		return selectOne("BettingMapper.selectHLHashBet", params);
	}

    public Integer  selectRTHashBet(Map<String,Object> params) {
  		return selectOne("BettingMapper.selectRTHashBet", params);
  	}

    public Integer  selectBRHashBet(Map<String,Object> params) {
  		return selectOne("BettingMapper.selectBRHashBet", params);
  	}

    public void insertOdHashBet(Map<String,Object> params) {
        insert("BettingMapper.insertOdHashBet", params);
    }

    public void updateOdBetHashBet(Map<String,Object> params) {
        update("BettingMapper.updateOdBetHashBet", params);
    }

    public void updateOdRewardHashBet(Map<String,Object> params) {
        update("BettingMapper.updateOdRewardHashBet", params);
    }
}
