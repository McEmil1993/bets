package com.soas.service.impl;

import com.soas.dao.impl.BettingDAO;
import com.soas.dao.impl.GameDAO;
import com.soas.dao.impl.UserDAO;
import com.soas.prop.CustomException;
import com.soas.prop.Properties;
import com.soas.prop.ResponseCode;
import com.soas.service.BettingService;
import com.soas.service.PropertiesService;
import com.soas.service.UserService;
import com.soas.util.CommonUtil;
import lombok.extern.log4j.Log4j2;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.HashMap;
import java.util.List;
import java.util.Map;


@Log4j2
@Service("BettingService")
public class BettingServiceImpl implements BettingService {
		
    private BettingDAO bettingDAO;
    private UserDAO userDAO;
    private GameDAO gameDAO;
    private UserService userService;
    private PropertiesService propertiesService;

    @Autowired
    public void BettingServiceImpl (BettingDAO bettingDAO, UserDAO userDAO, GameDAO gameDAO, UserService userService, PropertiesService propertiesService) {
        this.bettingDAO = bettingDAO;
        this.userDAO = userDAO;
        this.gameDAO = gameDAO;
        this.userService = userService;
        this.propertiesService = propertiesService;
    }


    @Override
    public int saveKpBetting(Map<String, Object> params) throws Exception {

        boolean isMaster = false;
        if(!CommonUtil.isEmpty(params.get("isMaster")))
            isMaster = Boolean.valueOf(params.get("isMaster").toString());

        /* 사이트 점검 */
        boolean isSiteInspection = propertiesService.isInspection(new HashMap<String,Object>(){{
            put("type", Properties.INSPECTION_VALUE_SITE);
        }});
        if(isSiteInspection && !isMaster)
            throw new CustomException(ResponseCode.INSPECTION_SITE);

        /* 이미 진행된 배팅 */
        Map<String,Object> bettingInfo = gameDAO.selectProductByTransaction(params);
        if(!CommonUtil.isEmpty(bettingInfo))
            throw new CustomException(ResponseCode.DUPLICATED_DEBIT);

        String productComment = "";
        switch (getProductType(params)) {
            /* 카지노 */
            case Properties.CONSTANTS_TYPE_CASINO :

                /* 카지노 점검 체크 */
                boolean isCasinoInspection = propertiesService.isInspection(new HashMap<String,Object>(){{
                    put("type", Properties.INSPECTION_VALUE_CASINO);
                }});
                if(isCasinoInspection && !isMaster)
                    throw new CustomException(ResponseCode.INSPECTION_SITE);

                bettingDAO.insertCasinoBet(params);
                bettingDAO.updateLastDepositBet(new HashMap<String,Object>(){{
                    put("memberIdx", params.get("memberIdx"));
                    put("casinoBetMoney", params.get("betMoney"));
                }});
                
                bettingDAO.updateBetPlus(new HashMap<String,Object>(){{
                    put("memberIdx", params.get("memberIdx"));
                    put("betMoney", params.get("betMoney"));
                }});

                productComment = "카지노";
                break;

            /* 슬롯 */
            case Properties.CONSTANTS_TYPE_SLOT :

                /* 슬롯 점검 체크 */
                boolean isSlotInspection = propertiesService.isInspection(new HashMap<String,Object>(){{
                    put("type", Properties.INSPECTION_VALUE_SLOT);
                }});
                if(isSlotInspection && !isMaster)
                    throw new CustomException(ResponseCode.INSPECTION_SITE);

                bettingDAO.insertSlotBet(params);
                bettingDAO.updateLastDepositBet(new HashMap<String,Object>(){{
                    put("memberIdx", params.get("memberIdx"));
                    put("slotBetMoney", params.get("betMoney"));
                }});

                bettingDAO.updateBetPlus(new HashMap<String,Object>(){{
                    put("memberIdx", params.get("memberIdx"));
                    put("betMoney", params.get("betMoney"));
                }});

                productComment = "슬롯게임";
                break;

            /* 이스포츠 */
            case Properties.CONSTANTS_TYPE_ESPORTS:

                bettingDAO.insertEsptBet(params);
                bettingDAO.updateLastDepositBet(new HashMap<String,Object>(){{
                    put("memberIdx", params.get("memberIdx"));
                    put("esptBetMoney", params.get("betMoney"));
                }});

                bettingDAO.updateBetPlus(new HashMap<String,Object>(){{
                    put("memberIdx", params.get("memberIdx"));
                    put("betMoney", params.get("betMoney"));
                }});

                productComment = "이스포츠";
                break;

            /* 협의되지 않은 제품 */
            default:
                throw new CustomException(ResponseCode.INVALID_PRODUCT);
        }

        /* Cash Logs 저장 */
        String finalProductComment = productComment;
        saveCashLog(new HashMap<String,Object>(){{
            put("type", "B");
            put("memberIdx", params.get("memberIdx"));
            put("transactionId", params.get("transactionId"));
            put("money", -Integer.parseInt(params.get("betMoney").toString()));
            put("comment", finalProductComment);
        }});

        /* 회원 머니 차감 */
        int money = userService.modifyMoney(new HashMap<String,Object>(){{
            put("kpId", params.get("kpId"));
            put("memberIdx", params.get("memberIdx"));
            put("addMoney", - Integer.parseInt(params.get("betMoney").toString()));
        }});

        return money;
    }

    @Override
    public int saveOdBetting(Map<String, Object> params) throws Exception {


        bettingDAO.insertHashBet(params);
        bettingDAO.updateLastDepositBet(new HashMap<String,Object>(){{
            put("memberIdx", params.get("memberIdx"));
            put("hashBetMoney", params.get("betMoney"));
        }});

        bettingDAO.updateBetPlus(new HashMap<String,Object>(){{
            put("memberIdx", params.get("memberIdx"));
            put("betMoney", params.get("betMoney"));
        }});
        
        /* Cash Logs 저장 */
        saveCashLog(new HashMap<String,Object>(){{
            put("type", "B");
            put("memberIdx", params.get("memberIdx"));
            put("wave", params.get("wave"));
            put("money", -Integer.parseInt(params.get("betMoney").toString()));
            put("comment", "해시게임");
        }});        

        /* 회원 머니 차감 */
        int money = userService.modifyMoney(new HashMap<String,Object>(){{
            put("kpId", params.get("kpId"));
            put("memberIdx", params.get("memberIdx"));
            put("addMoney", - Integer.parseInt(params.get("betMoney").toString()));
        }});


        return money;

    }

    @Override
	public int doOdBetting(Map<String, Object> params) throws Exception {

		String prodName = params.get("productType").toString();
		int idx = 0;
		switch (prodName) {
		case "H": {
			log.info("doOdBetting before selectHLHashBet: " + idx);
			idx = bettingDAO.selectHLHashBet(params);
			log.info("doOdBetting after selectHLHashBet: " + idx);
		}
			break;
		case "R": {
			idx = bettingDAO.selectRTHashBet(params);
		}
			break;
		case "B": {
			idx = bettingDAO.selectBRHashBet(params);
		}
			break;
		}

		params.put("hash_bet_idx",idx);
		params.put("id",0);
		
		
		if (0 < idx) {
			bettingDAO.updateOdBetHashBet(params);
			
		} else {
			bettingDAO.insertOdHashBet(params);
			idx = (Integer)params.get("id");
			
		}

		// bettingDAO.insertHashBet(params);
		bettingDAO.updateLastDepositBet(new HashMap<String, Object>() {
			{
				put("memberIdx", params.get("memberIdx"));
				put("hashBetMoney", params.get("betMoney"));
			}
		});

        bettingDAO.updateBetPlus(new HashMap<String,Object>(){{
            put("memberIdx", params.get("memberIdx"));
            put("betMoney", params.get("betMoney"));
        }});

		/* Cash Logs 저장 */
		
		int finalAcIdx = idx;
		saveCashLog(new HashMap<String, Object>() {
			{
				put("type", "B");
				put("memberIdx", params.get("memberIdx"));
				put("wave", params.get("wave"));
				put("money", -Integer.parseInt(params.get("betMoney").toString()));
				put("comment", "해시게임");
				put("idx",  finalAcIdx);
				put("gameType",  "Hash");
				
			}
		});

		/* 회원 머니 차감 */
		int money = userService.modifyMoney(new HashMap<String, Object>() {
			{
				put("kpId", params.get("kpId"));
				put("memberIdx", params.get("memberIdx"));
				put("addMoney", -Integer.parseInt(params.get("betMoney").toString()));
			}
		});

		return money;
	}

    @Override
    public int saveBonus(Map<String, Object> params) throws Exception {

        String bonusType = params.get("bonusType").toString();
        String type = Properties.CONSTANTS_TYPE_BONUS.get(bonusType);
        params.put("type", type);

        String productComment = "[보너스 지급] ";
        switch (getProductType(params)) {
            /* 카지노 */
            case Properties.CONSTANTS_TYPE_CASINO :
                bettingDAO.insertCasinoBonus(params);
                productComment += "카지노";
                break;

            /* 슬롯 */
            case Properties.CONSTANTS_TYPE_SLOT :
                bettingDAO.insertSlotBonus(params);
                productComment += "슬롯게임";
                break;

            /* 이스포츠 */
            case Properties.CONSTANTS_TYPE_ESPORTS:
                bettingDAO.insertEsptBonus(params);
                productComment += "이스포츠";
                break;

            /* 협의되지 않은 제품 */
            default:
                throw new CustomException(ResponseCode.INVALID_PRODUCT);
        }

        /* Cash Logs 저장 */
        String finalProductComment = productComment;
        saveCashLog(new HashMap<String,Object>(){{
            put("type", "W");
            put("memberIdx", params.get("memberIdx"));
            put("transactionId", params.get("transactionId"));
            put("money", Integer.parseInt(params.get("amount").toString()));
            put("comment", finalProductComment);
        }});

        /* 회원 머니 증가 */
        int money = userService.modifyMoney(new HashMap<String,Object>(){{
            put("kpId", params.get("kpId"));
            put("memberIdx", params.get("memberIdx"));
            put("addMoney", Integer.parseInt(params.get("amount").toString()));
        }});
        
        return money;
    }

    @Override
    public int modifyWinBetting(Map<String, Object> params) throws Exception {

        params.put("type", "W");
        params.put("addMoney", Integer.parseInt(params.get("amount").toString()));
        log.info("========================================================");
    	log.info("modifyWinBetting");
    	log.info("params : "+params);
    	log.info("========================================================");
        modifyBetting(params);
        int afterMoney = userService.modifyMoney(params);

        return afterMoney;
    }

    @Override
	public int doOdReward(Map<String, Object> params) throws Exception {
		params.put("type", "W");
		params.put("addMoney", Integer.parseInt(params.get("amount").toString()));
		log.info("========================================================");
		log.info("doOdReward");
		log.info("params : " + params);
		log.info("========================================================");
		modifyHashBetting(params);
		int afterMoney = userService.modifyMoney(params);

		return afterMoney;
	}

    
    @Override
    public int modifyLoseBetting(Map<String, Object> params) throws Exception {

        params.put("type", "L");
        params.put("addMoney", 0);

        modifyBetting(params);
        int afterMoney = userService.modifyMoney(params);

        return afterMoney;
    }

    @Override
    public int modifyCancelBetting(Map<String, Object> params) throws Exception {

        params.put("type", "C");
        params.put("addMoney", Integer.parseInt(params.get("amount").toString()));

        modifyBetting(params);
        int afterMoney = userService.modifyMoney(params);

        return afterMoney;
    }

    @Override
    public List<Map<String, Object>> getDelayBetList(Map<String, Object> params) throws Exception {
        return bettingDAO.selectDelayBetList(params);
    }

    @Override
    public void modifyDelayOdBet(Map<String, Object> params) throws Exception {
        bettingDAO.updateLoseHashBet(params);
    }

    /* 카지노 OR 슬롯 구분 */
    private String getProductType (Map<String,Object> params) throws Exception {

        try {
            if(CommonUtil.isEmpty(params.get("productId")))
                params.put("productId", gameDAO.selectProductByTransaction(params).get("prdId"));

            return gameDAO.selectProductInfo(params).get("type").toString();
        } catch (NullPointerException npe){
            throw new CustomException(ResponseCode.INVALID_PRODUCT);
        }

    }


    /* 카지노 OR 슬롯 배팅 내역 수정 */
    private void modifyBetting (Map<String,Object> params) throws Exception {

        String productComment = "";
        /* ODDS 해쉬게임 */
        if(!CommonUtil.isEmpty(params.get("productType"))){
            bettingDAO.updateHashBet(params);
            productComment = "해시게임";
        /* KPlay 게임 */
        } else {
            Map<String,Object> bettingInfo = gameDAO.selectProductByTransaction(params);
            if(CommonUtil.isEmpty(bettingInfo))
                throw new CustomException(ResponseCode.INVALID_PRODUCT);

            /* 이미 처리된 게임 */
            if(!bettingInfo.get("type").toString().equals("B"))
                throw new CustomException(ResponseCode.DUPLICATED_BETTING);

            switch (getProductType(params)) {
                /* 카지노 */
                case Properties.CONSTANTS_TYPE_CASINO :
                    bettingDAO.updateCasinoBet(params);
                    productComment = "카지노";
                    break;

                /* 배팅 */
                case Properties.CONSTANTS_TYPE_SLOT :
                    bettingDAO.updateSlotBet(params);
                    productComment = "슬롯게임";
                    break;

                /* 이스포츠 */
                case Properties.CONSTANTS_TYPE_ESPORTS:
                    bettingDAO.updateEsptBet(params);
                    productComment = "이스포츠";
                    break;

                /* 협의되지 않은 제품 */
                default:
                    throw new CustomException(ResponseCode.INVALID_PRODUCT);
            }
        }

        /* Cash Logs 저장 */
        if("W".equalsIgnoreCase(params.get("type").toString())){
            String finalProductComment = productComment;
            saveCashLog(new HashMap<String,Object>(){{
                put("type", "W");
                put("memberIdx", params.get("memberIdx"));
                put("transactionId", params.get("transactionId"));
                put("wave", params.get("wave"));
                put("money", Integer.parseInt(params.get("amount").toString()));
                put("comment", finalProductComment);
            }});
        }
    }

    /*해쉬 배팅내역 수정  */
	private void modifyHashBetting(Map<String, Object> params) throws Exception {

		String productComment = "";
		/* ODDS 해쉬게임 */

		// bettingDAO.updateHashBet(params);

		int idx = 0;
		String prodName = params.get("productType").toString();
		switch (prodName) {
		case "H": {
			idx = bettingDAO.selectHLHashBet(params);
		}
			break;
		case "R": {
			idx = bettingDAO.selectRTHashBet(params);
		}
			break;
		case "B": {
			idx = bettingDAO.selectBRHashBet(params);
		}
			break;
		}

		params.put("hash_bet_idx",idx);
		if (0 < idx) {
			bettingDAO.updateOdRewardHashBet(params);
		} else {
			throw new CustomException(ResponseCode.INVALID_PRODUCT);
		}

		productComment = "해시게임";

		/* Cash Logs 저장 */
		if ("W".equalsIgnoreCase(params.get("type").toString())) {
			String finalProductComment = productComment;
			int finalAcIdx = idx;
			saveCashLog(new HashMap<String, Object>() {
				{
					put("type", "W");
					put("memberIdx", params.get("memberIdx"));
					put("transactionId", params.get("transactionId"));
					put("wave", params.get("wave"));
					put("money", Integer.parseInt(params.get("amount").toString()));
					put("comment", finalProductComment);
					put("idx",  finalAcIdx);
					put("gameType",  "Hash");
					
					
				}
			});
		}
	}
	
    /* INPUT : type, memberIdx, money, transactionId || wave, comment */
    private void saveCashLog (Map<String,Object> params) throws Exception {

        Map<String,Object> userInfo = userService.getUserInfo(new HashMap<String, Object>() {{
            put("memberIdx", params.get("memberIdx"));
        }});
        params.put("holdMoney", userInfo.get("money"));
        params.put("holdPoint", userInfo.get("point"));

        int acIdx = 0;
        if (CommonUtil.isEmpty(params.get("transactionId"))) {
            acIdx = Integer.parseInt(String.valueOf(params.get("wave")));

        } else {
            Map<String,Object> betInfo = bettingDAO.selectKplayBet(new HashMap<String,Object>(){{
                put("transactionId", params.get("transactionId"));
            }});
            if(!CommonUtil.isEmpty(betInfo))
                acIdx = Integer.parseInt(betInfo.get("ttlBetIdx").toString());
        }
        
        if(!CommonUtil.isEmpty(params.get("gameType")) && "Hash" == params.get("gameType").toString()) {
			params.put("acIdx", params.get("idx"));
		} else {
			params.put("acIdx", acIdx);
		}

        String betType = params.get("type").toString();
        switch (betType) {
            /* 배팅 */
            case "B" :
                params.put("acCode", 3);
                params.put("acType", "M");
                break;
            /* 당첨 */
            case "W" :
                params.put("acCode", 7);
                params.put("acType", "P");
                break;
        }
        bettingDAO.insertCashLog(params);
    }
}
