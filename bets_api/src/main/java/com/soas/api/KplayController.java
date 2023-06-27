package com.soas.api;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.soas.prop.CustomException;
import com.soas.prop.CustomResponse;
import com.soas.service.BettingService;
import com.soas.service.UserService;
import com.soas.util.CommonUtil;
import lombok.extern.log4j.Log4j2;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpEntity;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.HashMap;
import java.util.Map;


@Log4j2
@RestController
@RequestMapping("/")
public class KplayController {


    private UserService userService;
    private BettingService bettingService;

    @Autowired
    public void BetController(UserService userService, BettingService bettingService) {
        this.userService = userService;
        this.bettingService = bettingService;
    }


    /* 차감 API 1 */
    @PostMapping("/debit")
    public HttpEntity<? extends Object> debit(@RequestBody String paramStr) throws Exception {

        //int holdMoney = 0;
        try {
            ObjectMapper mapper = new ObjectMapper();
            Map<String, Object> params = mapper.readValue(paramStr, Map.class);
            log.info("##################################");
            log.info(params);

            String kpId = params.get("user_id").toString();
            String productId = params.get("prd_id").toString();
            String gameId = CommonUtil.isEmpty(params.get("game_id").toString()) ? "0" : params.get("game_id").toString();
            String transactionId = params.get("txn_id").toString();
            int betMoney = Long.valueOf(Math.round(Double.valueOf(params.get("amount").toString()))).intValue();

            /* 배팅 & 승리 동시 처리일 시 승리금액 */
            int winMoney = 0;
            boolean hasWinMoney = false;
            if(!CommonUtil.isEmpty(params.get("credit_amount"))){
                hasWinMoney = true;
                winMoney = Integer.parseInt(params.get("credit_amount").toString());
            }


            /* 회원 조회 */
            Map<String,Object> userInfo = userService.getUserInfo(new HashMap<String,Object>(){{
                put("kpId", kpId);
            }});
            if(CommonUtil.isEmpty(userInfo))
                return new ResponseEntity<>(new HashMap<String,Object>(){{
                    put("status", 0);
                    put("error", "INVALID_USER");
                }}, HttpStatus.OK);
            String memberIdx = userInfo.get("memberIdx").toString();

            /* 보유 금액보다 큰 배팅 */
            int holdMoney = Integer.parseInt(userInfo.get("money").toString());
            if(holdMoney < betMoney)
                return new ResponseEntity<>(new HashMap<String,Object>(){{
                    put("status", 0);
                    put("error", "INSUFFICIENT_FUNDS");
                }}, HttpStatus.OK);

            /* 배팅 저장 */
            int afterMoney = bettingService.saveKpBetting(new HashMap<String,Object>(){{
                put("kpId", kpId);
                put("memberIdx", memberIdx);
                put("betMoney", betMoney);
                put("productId", productId);
                put("gameId", gameId);
                put("transactionId", transactionId);
                put("holdMoney", holdMoney);
                
                if(Integer.parseInt(userInfo.get("level").toString()) == 9){
                    put("isMaster", true);
                }
            }});

            /* IF 배팅 & 승리 동시 처리 */
            if(winMoney > 0){
                /* 배팅 승리 처리 */
                Map<String,Object> betParams = new HashMap<>();
                betParams.put("kpId", kpId);
                betParams.put("memberIdx", memberIdx);
                betParams.put("amount", winMoney);
                betParams.put("transactionId", transactionId);
                betParams.put("betMoney", betMoney);
                bettingService.modifyWinBetting(betParams);
                afterMoney += winMoney;

            } else if (hasWinMoney && winMoney == 0) {
                /* 배팅 패배 처리 */
                Map<String,Object> betParams = new HashMap<>();
                betParams.put("kpId", kpId);
                betParams.put("memberIdx", memberIdx);
                betParams.put("amount", winMoney);
                betParams.put("transactionId", transactionId);
                betParams.put("betMoney", betMoney);
                bettingService.modifyLoseBetting(betParams);
            }

            /* Response */
            Map<String,Object> result = new HashMap<>();
            result.put("balance", afterMoney);
            result.put("status", 1);
            return new ResponseEntity<>(result, HttpStatus.OK);

        } catch (CustomException ce) {
            ce.printStackTrace();
            log.error(ce);
            return CustomResponse.toResponseEntity(ce.getResponseCode());
        } catch (Exception e) {
            e.printStackTrace();
            log.error(e);
            return new ResponseEntity<>(new HashMap<String,Object>(){{
                put("balance", 0);
                put("error", "SERVER ERROR");
            }}, HttpStatus.OK);
        }
    }


    /* 차감 API 2 */
    @PostMapping("/buyin")
    public HttpEntity<? extends Object> buyin(@RequestBody String paramStr) throws Exception {

        try {

            ObjectMapper mapper = new ObjectMapper();
            Map<String, Object> params = mapper.readValue(paramStr, Map.class);
            log.info("##################################");
            log.info(params);

            String kpId = params.get("user_id").toString();
            String productId = params.get("prd_id").toString();
            String gameId = CommonUtil.isEmpty(params.get("game_id").toString()) ? "0" : params.get("game_id").toString();
            String transactionId = params.get("txn_id").toString();
            int betMoney = Long.valueOf(Math.round(Double.valueOf(params.get("amount").toString()))).intValue();

            /* 배팅 & 승리 동시 처리일 시 승리금액 */
            int winMoney = 0;
            boolean hasWinMoney = false;
            if(!CommonUtil.isEmpty(params.get("credit_amount"))){
                hasWinMoney = true;
                winMoney = Integer.parseInt(params.get("credit_amount").toString());
            }


            /* 회원 조회 */
            Map<String,Object> userInfo = userService.getUserInfo(new HashMap<String,Object>(){{
                put("kpId", kpId);
            }});
            if(CommonUtil.isEmpty(userInfo))
                return new ResponseEntity<>(new HashMap<String,Object>(){{
                    put("status", 0);
                    put("error", "INVALID_USER");
                }}, HttpStatus.OK);
            String memberIdx = userInfo.get("memberIdx").toString();

            /* 보유 금액보다 큰 배팅 */
            int holdMoney = Integer.parseInt(userInfo.get("money").toString());
            if(holdMoney < betMoney)
                return new ResponseEntity<>(new HashMap<String,Object>(){{
                    put("status", 0);
                    put("error", "INSUFFICIENT_FUNDS");
                }}, HttpStatus.OK);

            /* 배팅 저장 */
            int afterMoney = bettingService.saveKpBetting(new HashMap<String,Object>(){{
                put("kpId", kpId);
                put("memberIdx", memberIdx);
                put("betMoney", betMoney);
                put("productId", productId);
                put("gameId", gameId);
                put("transactionId", transactionId);
                put("holdMoney", holdMoney);
                
                if(Integer.parseInt(userInfo.get("level").toString()) == 9){
                    put("isMaster", true);
                }
            }});

            /* IF 배팅 & 승리 동시 처리 */
            if(winMoney > 0){
                /* 배팅 승리 처리 */
                Map<String,Object> betParams = new HashMap<>();
                betParams.put("kpId", kpId);
                betParams.put("memberIdx", memberIdx);
                betParams.put("amount", winMoney);
                betParams.put("transactionId", transactionId);
                betParams.put("betMoney", betMoney);
                bettingService.modifyWinBetting(betParams);
            } else if (hasWinMoney && winMoney == 0) {
                /* 배팅 패배 처리 */
                Map<String,Object> betParams = new HashMap<>();
                betParams.put("kpId", kpId);
                betParams.put("memberIdx", memberIdx);
                betParams.put("amount", winMoney);
                betParams.put("transactionId", transactionId);
                betParams.put("betMoney", betMoney);
                bettingService.modifyLoseBetting(betParams);
            }

            /* Response */
            Map<String,Object> result = new HashMap<>();
            result.put("balance", afterMoney);
            result.put("status", 1);
            return new ResponseEntity<>(result, HttpStatus.OK);

        } catch (CustomException ce) {
            ce.printStackTrace();
            log.error(ce);
            return CustomResponse.toResponseEntity(ce.getResponseCode());
        } catch (Exception e) {
            e.printStackTrace();
            log.error(e);
            return new ResponseEntity<>(new HashMap<String,Object>(){{
                put("balance", 0);
                put("error", "SERVER ERROR");
            }}, HttpStatus.OK);
        }
    }


    /* 증감 API */
    @PostMapping("/credit")
    public HttpEntity<? extends Object> credit(@RequestBody String paramStr) throws Exception {

        int holdMoney = 0;
        try {

            ObjectMapper mapper = new ObjectMapper();
            Map<String, Object> params = mapper.readValue(paramStr, Map.class);
            log.info("##################################");
            log.info(params);

            String kpId = params.get("user_id").toString();
            String transactionId = params.get("txn_id").toString();
            int amount = Long.valueOf(Math.round(Double.valueOf(params.get("amount").toString()))).intValue();
            int isCancel = Integer.parseInt(params.get("is_cancel").toString());

            /* 회원 조회 */
            Map<String,Object> userInfo = userService.getUserInfo(new HashMap<String,Object>(){{
                put("kpId", kpId);
            }});
            if(CommonUtil.isEmpty(userInfo))
                return new ResponseEntity<>(new HashMap<String,Object>(){{
                    put("status", 0);
                    put("error", "INVALID_USER");
                }}, HttpStatus.OK);
            String memberIdx = userInfo.get("memberIdx").toString();
            holdMoney = Integer.parseInt(userInfo.get("money").toString());

            Map<String,Object> betParams = new HashMap<>();
            betParams.put("kpId", kpId);
            betParams.put("memberIdx", memberIdx);
            betParams.put("amount", amount);
            betParams.put("transactionId", transactionId);
            betParams.put("userLevel", userInfo.get("level"));

            int afterMoney = 0;

            /* 취소 */
            if(isCancel == 1) {
                afterMoney = bettingService.modifyCancelBetting(betParams);

                /* 승리 */
            } else if (isCancel == 0 && amount > 0) {
                afterMoney = bettingService.modifyWinBetting(betParams);

                /* 패배 */
            } else {
                afterMoney = bettingService.modifyLoseBetting(betParams);
            }

            /* Response */
            Map<String,Object> result = new HashMap<>();
            result.put("balance", afterMoney);
            result.put("status", 1);
            return new ResponseEntity<>(result, HttpStatus.OK);

        } catch (CustomException ce) {
            ce.printStackTrace();
            log.error(ce);
            return CustomResponse.toResponseEntity(ce.getResponseCode(), holdMoney);
        } catch (Exception e) {
            e.printStackTrace();
            log.error(e);
            return new ResponseEntity<>(new HashMap<String,Object>(){{
                put("error", "SERVER ERROR");
            }}, HttpStatus.OK);
        }
    }


    /* 잔액 조회 API */
    @PostMapping("/balance")
    public HttpEntity<? extends Object> balance(@RequestBody String paramStr) throws Exception {

        try {
            ObjectMapper mapper = new ObjectMapper();
            Map<String, Object> params = mapper.readValue(paramStr, Map.class);
            log.info("##################################");
            log.info(params);

            String kpId = params.get("user_id").toString();
            params.put("kpId", kpId);

            Map<String,Object> userInfo = userService.getUserInfo(params);
            if(CommonUtil.isEmpty(userInfo))
                return new ResponseEntity<>(new HashMap<String,Object>(){{
                    put("status", 0);
                    put("error", "INVALID_USER");
                }}, HttpStatus.OK);

            int money = Integer.valueOf(String.valueOf(userInfo.get("money")));

            return new ResponseEntity<>(new HashMap<String,Object>(){{
                put("balance", money);
                put("status", 1);
            }}, HttpStatus.OK);

        } catch (CustomException ce) {
            ce.printStackTrace();
            log.error(ce);
            return CustomResponse.toResponseEntity(ce.getResponseCode());
        } catch (Exception e){
            e.printStackTrace();
            log.error(e);
            return new ResponseEntity<>(new HashMap<String,Object>(){{
                put("balance", 0);
                put("error", "SERVER ERROR");
            }}, HttpStatus.OK);
        }

    }


    /* 보너스 API */
    @PostMapping("/bonus")
    public HttpEntity<? extends Object> bonus(@RequestBody String paramStr) throws Exception {

        try {

            ObjectMapper mapper = new ObjectMapper();
            Map<String, Object> params = mapper.readValue(paramStr, Map.class);
            log.info("##################################");
            log.info(params);

            String kpId = params.get("user_id").toString();
            String transactionId = params.get("txn_id").toString();
            String productId = params.get("prd_id").toString();
            String type = params.get("type").toString();
            int amount = Long.valueOf(Math.round(Double.valueOf(params.get("amount").toString()))).intValue();

            /* 회원 조회 */
            Map<String,Object> userInfo = userService.getUserInfo(new HashMap<String,Object>(){{
                put("kpId", kpId);
            }});
            if(CommonUtil.isEmpty(userInfo))
                return new ResponseEntity<>(new HashMap<String,Object>(){{
                    put("status", 0);
                    put("error", "NOT FOUND USER");
                }}, HttpStatus.BAD_REQUEST);
            String memberIdx = userInfo.get("memberIdx").toString();

            int afterMoney = bettingService.saveBonus(new HashMap<String,Object>(){{
                put("kpId", kpId);
                put("memberIdx", memberIdx);
                put("transactionId", transactionId);
                put("productId", productId);
                put("bonusType", type);
                put("amount", amount);
                put("holdMoney", userInfo.get("money").toString());
            }});

            /* Response */
            Map<String,Object> result = new HashMap<>();
            result.put("balance", afterMoney);
            result.put("status", 1);
            return new ResponseEntity<>(result, HttpStatus.OK);

        } catch (CustomException ce) {
            ce.printStackTrace();
            log.error(ce);
            return CustomResponse.toResponseEntity(ce.getResponseCode());
        } catch (Exception e) {
            e.printStackTrace();
            log.error(e);
            return new ResponseEntity<>(new HashMap<String,Object>(){{
                put("balance", 0);
                put("error", "SERVER ERROR");
            }}, HttpStatus.OK);
        }
    }


}
