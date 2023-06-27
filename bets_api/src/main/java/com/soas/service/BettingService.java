package com.soas.service;


import java.util.List;
import java.util.Map;

public interface BettingService {


    /* KPlay 배팅 */
    public int saveKpBetting (Map<String,Object> params) throws Exception;

    /* ODDS 배팅 */
    public int saveOdBetting (Map<String,Object> params) throws Exception;

    /* ODDS 배팅 */
    public int doOdBetting (Map<String,Object> params) throws Exception;

    
    /* 보너스 */
    public int saveBonus (Map<String,Object> params) throws Exception;

    /* 당첨 */
    public int modifyWinBetting (Map<String,Object> params) throws Exception;


    /* 당첨 */
    public int doOdReward (Map<String,Object> params) throws Exception;

    /* 낙첨 */
    public int modifyLoseBetting (Map<String,Object> params) throws Exception;

    /* 취소 */
    public int modifyCancelBetting (Map<String,Object> params) throws Exception;

    /* 대기 목록 조회 */
    public List<Map<String,Object>> getDelayBetList (Map<String,Object> params) throws Exception;

    /* ODDS 지연 배팅 낙첨 처리 */
    public void modifyDelayOdBet (Map<String,Object> params) throws Exception;

}
