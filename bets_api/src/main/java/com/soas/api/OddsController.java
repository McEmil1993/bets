package com.soas.api;

import com.soas.prop.CustomException;
import com.soas.service.BettingService;
import com.soas.service.UserService;
import com.soas.util.CommonUtil;
import lombok.extern.log4j.Log4j2;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpEntity;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.lang.Nullable;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import java.util.HashMap;
import java.util.Map;
import java.util.regex.Pattern;
import java.util.stream.Collectors;

@Log4j2
@RestController
@RequestMapping("/api/odds")
public class OddsController {


    private UserService userService;
    private BettingService bettingService;

    @Autowired
    public void BetController(UserService userService, BettingService bettingService) {
        this.userService = userService;
        this.bettingService = bettingService;
    }


    @RequestMapping("/v1")
    public HttpEntity<? extends Object> oddsV1(@RequestParam String cmd,            /* API 분류 */
                                               @RequestParam String id,             /* User ID */
                                               @RequestParam String code,           /* User IDX */
                                               @RequestParam @Nullable String money,   /* 변동 금액 ( - OR + ) */
                                               @RequestParam @Nullable String msg   /* 배팅 정보 */
    ) throws Exception {

        Map<String, Object> result = new HashMap<>();

        log.info("#ODDS_API_V1");
        log.info("cmd : " + cmd);
        log.info("id : " + id);
        log.info("code : " + code);
        log.info("money : " + money);
        log.info("msg : " + msg);

        try {
            /* 공통 파라미터 검증 */
            if (CommonUtil.isEmpty(cmd) || CommonUtil.isEmpty(id) || CommonUtil.isEmpty(code)) {
                result.put("ret", 0);
                result.put("msg", "MISSING DATA");
                return new ResponseEntity<>(result, HttpStatus.OK);
            }

            int holdMoney = 0;      /* 보유 머니 */
            int afterMoney = 0;     /* 이후 머니 */
            int actionMoney = CommonUtil.isEmpty(money) ? 0 : Integer.parseInt(money);

            /* 회원 검증 */
            Map<String, Object> userInfo;
            try {
                userInfo = userService.getUserInfoOdd(new HashMap<String, Object>() {{
                    put("memberIdx", code);
                }});
                holdMoney = Integer.parseInt(userInfo.get("money").toString());
            } catch (CustomException e) {
                log.error(e);
                result.put("ret", 0);
                result.put("msg", "INVALID USER");
                return new ResponseEntity<>(result, HttpStatus.OK);
            }


            /* API 분기 */
            switch (cmd) {
                /* 회원 조회 */
                case "UserCheck":
                    afterMoney = holdMoney;
                    result.put("nick", userInfo.get("nickName"));
                    result.put("isuse_highlow", 1);
                    result.put("isuse_roulette", 1);
                    result.put("isuse_baccarat", 1);
                    break;
                /* 배팅 로그 */
                case "MoneyLog":
                    /* 파라미터 검증 */
                    if (CommonUtil.isEmpty(money) || CommonUtil.isEmpty(msg)) {
                        result.put("ret", 0);
                        result.put("msg", "MISSING DATA");
                        return new ResponseEntity<>(result, HttpStatus.OK);
                    }

                    Map<String, String> betMap = Pattern.compile("\\|").splitAsStream(msg.trim()).map(i -> i.split("=", 2)).collect(Collectors.toMap(a -> a[0], a -> a[1]));

                    String prodName = betMap.get("game").toString();
                    String prodType = null;

                    switch (prodName) {
                        case "highlow":
                            prodType = "H";

                            break;
                        case "roulette":
                            prodType = "R";
                            break;
                        case "baccarat":
                            prodType = "B";

                            break;
                    }

                    String finalProdType = prodType;
                    /* 배팅 */
                    if(CommonUtil.isEmpty(betMap.get("reward"))){

                    	/* 1.플레이어의 잔여 포인트가 없는 경우
                         * 2.베팅 금액보다 유저 머니가 적을 경우
                         */
                        if (holdMoney == 0 || (holdMoney < -actionMoney && actionMoney < 0)) {
                            result.put("ret", 0);
                            result.put("balance", 0);
                            result.put("msg", "not enough user point");
                            return new ResponseEntity<>(result, HttpStatus.OK);
                        }

						// afterMoney = bettingService.saveOdBetting(new HashMap<String,Object>(){{
                        afterMoney = bettingService.doOdBetting(new HashMap<String,Object>(){{
                            put("memberIdx", code);
                            put("betMoney", Math.abs(Integer.parseInt(money)));
                            put("productType", finalProdType);
                            put("wave", betMap.get("wave"));
                            put("holdMoney", userInfo.get("money").toString());
                            put("betting_type", betMap.get("betting_type"));
                            put("betting_type_label", betMap.get("betting_type_label"));
                            put("channel_code", betMap.get("channel_code"));

                        }});

                    /* 당첨 */
                    } else {
                        //afterMoney = bettingService.modifyWinBetting(new HashMap<String,Object>(){{
                    	afterMoney = bettingService.doOdReward(new HashMap<String,Object>(){{
                            put("memberIdx", code);
                            put("amount", betMap.get("reward"));
                            put("productType", finalProdType);
                            put("wave", betMap.get("wave"));
                            put("userLevel", userInfo.get("level"));
                            put("betting_type", betMap.get("betting_type"));
                            put("betting_type_label", betMap.get("betting_type_label"));
                            put("channel_code", betMap.get("channel_code"));
                        }});
                    }

                    break;
                default:
                    result.put("ret", 0);
                    result.put("msg", "INVALID CMD");
                    return new ResponseEntity<>(result, HttpStatus.OK);
            }

            /* OK Response */
            result.put("ret", 1);
            result.put("msg", "성공");
            result.put("balance", afterMoney);
            return new ResponseEntity<>(result, HttpStatus.OK);
        } catch (Exception e) {
            log.error(e);
            e.printStackTrace();
            result.put("ret", 0);
            result.put("msg", "INVALID CMD");
            return new ResponseEntity<>(result, HttpStatus.OK);
        }
    }




    @RequestMapping("/test")
    public HttpEntity<? extends Object> oddsV2(@RequestParam @Nullable String msg) throws Exception {


//////////////////데이터 직접 입력하는 부분 / KP_MBR_INF테이블에 데이터가 있어야 회원검직 로직에서 통과됩니다.//////////////////////////
    	String cmd = "MoneyLog"; /* API 분류 */
        String id = "test77"; /* User ID */
        String code = "10198"; /* User IDX */
        String money = "-100000"; /* 변동 금액 ( - OR + ) */
        //String msg = "game=highlow|channel_code=|wave=258501|id=kmuch|code=6493|amount=10000|betting_type=NUM_R|betting_type_label=J,Q,K,A|betting_rate=2.6"; /* 배팅 정보 */

        Map<String, String> betMap = new HashMap<>();
        betMap.put("game", "highlow");
        //betMap.put("reward", ""); // 입력시 당첨, 없을 경우 베팅
        betMap.put("wave", "258501");
        betMap.put("betting_type", "NUM_R");
        betMap.put("betting_type_label", "J,Q,K,A");
        betMap.put("channel_code", "");
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        Map<String, Object> result = new HashMap<>();

        log.info("#oddsV2");
        log.info("cmd : " + cmd);
        log.info("id : " + id);
        log.info("code : " + code);
        log.info("money : " + money);
        log.info("msg : " + msg);

        try {
            /* 공통 파라미터 검증 */
            if (CommonUtil.isEmpty(cmd) || CommonUtil.isEmpty(id) || CommonUtil.isEmpty(code)) {
                result.put("ret", 0);
                result.put("msg", "MISSING DATA");
                return new ResponseEntity<>(result, HttpStatus.OK);
            }

            int holdMoney = 0;      /* 보유 머니 */
            int afterMoney = 0;     /* 이후 머니 */
            int actionMoney = CommonUtil.isEmpty(money) ? 0 : Integer.parseInt(money);

            /* 회원 검증 */
            Map<String, Object> userInfo;
            try {
                userInfo = userService.getUserInfoOdd(new HashMap<String, Object>() {{
                    put("memberIdx", code);
                }});
                holdMoney = Integer.parseInt(userInfo.get("money").toString());
            } catch (CustomException e) {
                log.error(e);
                result.put("ret", 0);
                result.put("msg", "INVALID USER");
                return new ResponseEntity<>(result, HttpStatus.OK);
            }

System.out.println(userInfo.get("nickName"));
            /* API 분기 */
            switch (cmd) {
                /* 회원 조회 */
                case "UserCheck":
                    afterMoney = holdMoney;
                    result.put("nick", userInfo.get("nickName"));
                    result.put("isuse_highlow", 1);
                    result.put("isuse_roulette", 1);
                    result.put("isuse_baccarat", 1);
                    break;
                /* 배팅 로그 */
                case "MoneyLog":
                    /* 파라미터 검증 */
                    if (CommonUtil.isEmpty(money) || CommonUtil.isEmpty(msg)) {
                        result.put("ret", 0);
                        result.put("msg", "MISSING DATA");
                        return new ResponseEntity<>(result, HttpStatus.OK);
                    }

                    //Map<String, String> betMap = Pattern.compile("\\|").splitAsStream(msg.trim()).map(i -> i.split("=", 2)).collect(Collectors.toMap(a -> a[0], a -> a[1]));

                    String prodName = betMap.get("game").toString();
                    String prodType = null;

                    switch (prodName) {
                        case "highlow":
                            prodType = "H";

                            break;
                        case "roulette":
                            prodType = "R";
                            break;
                        case "baccarat":
                            prodType = "B";

                            break;
                    }

                    String finalProdType = prodType;
                    /* 배팅 */
                    if(CommonUtil.isEmpty(betMap.get("reward"))){

                    	/* 1.플레이어의 잔여 포인트가 없는 경우
                         * 2.베팅 금액보다 유저 머니가 적을 경우
                         */
                        if (holdMoney == 0 || (holdMoney < -actionMoney && actionMoney < 0)) {
                            result.put("ret", 0);
                            result.put("balance", 0);
                            result.put("msg", "not enough user point");
                            return new ResponseEntity<>(result, HttpStatus.OK);
                        }

						// afterMoney = bettingService.saveOdBetting(new HashMap<String,Object>(){{
                        afterMoney = bettingService.doOdBetting(new HashMap<String,Object>(){{
                            put("memberIdx", code);
                            put("betMoney", Math.abs(Integer.parseInt(money)));
                            put("productType", finalProdType);
                            put("wave", betMap.get("wave"));
                            put("holdMoney", userInfo.get("money").toString());
                            put("betting_type", betMap.get("betting_type"));
                            put("betting_type_label", betMap.get("betting_type_label"));
                            put("channel_code", betMap.get("channel_code"));

                        }});

                    /* 당첨 */
                    } else {
                        //afterMoney = bettingService.modifyWinBetting(new HashMap<String,Object>(){{
                    	afterMoney = bettingService.doOdReward(new HashMap<String,Object>(){{
                            put("memberIdx", code);
                            put("amount", betMap.get("reward"));
                            put("productType", finalProdType);
                            put("wave", betMap.get("wave"));
                            put("userLevel", userInfo.get("level"));
                            put("betting_type", betMap.get("betting_type"));
                            put("betting_type_label", betMap.get("betting_type_label"));
                            put("channel_code", betMap.get("channel_code"));
                        }});
                    }

                    break;
                default:
                    result.put("ret", 0);
                    result.put("msg", "INVALID CMD");
                    return new ResponseEntity<>(result, HttpStatus.OK);
            }

            /* OK Response */
            result.put("ret", 1);
            result.put("msg", "성공");
            result.put("balance", afterMoney);
            return new ResponseEntity<>(result, HttpStatus.OK);
        } catch (Exception e) {
            log.error(e);
            e.printStackTrace();
            result.put("ret", 0);
            result.put("msg", "INVALID CMD");
            return new ResponseEntity<>(result, HttpStatus.OK);
        }
    }
}
