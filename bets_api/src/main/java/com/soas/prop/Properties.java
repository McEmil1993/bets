package com.soas.prop;


import org.springframework.stereotype.Service;

import java.util.HashMap;
import java.util.Map;


@Service("Properties")
public class Properties {

    /* 점검 관련 */
    public static final String INSPECTION_VALUE_SITE        = "service_site";
    public static final String INSPECTION_VALUE_CASINO     = "service_casino";
    public static final String INSPECTION_VALUE_SLOT     = "service_slot";

    public static String KPLAY_API_DOMAIN = "http://kplayone.com";                        /* (PROD) KPLAY API 도메인 */
    public static final String KPLAY_AG_CODE = "BON2509";                                 /* KPLAY 계정 코드 */
    public static final String KPLAY_AG_TOKEN = "0VeraFOE6j6yG2iqfcNjn1dHcEzOu9ox";      /* KPLAY API 토큰 */
    public static final String KPLAY_SECRET_KEY = "OixfkKu01DANt8OBz0thXQZk1afqLGfo";     /* KPLAY 시크릿 토큰 */

    /* Constants */
    public static final String CONSTANTS_TYPE_CASINO = "C";
    public static final String CONSTANTS_TYPE_SLOT = "S";
    public static final String CONSTANTS_TYPE_ESPORTS = "E";
    public static final int DELAY_BETTING_LIMIT = 30;

    public static final Map<String,String> CONSTANTS_TYPE_BONUS = new HashMap<String,String>(){{
        put("0", "I");
        put("1", "P");
        put("2", "J");
    }};

}
