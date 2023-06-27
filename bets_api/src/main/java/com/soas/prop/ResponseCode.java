package com.soas.prop;

import lombok.AllArgsConstructor;
import lombok.Getter;
import org.springframework.http.HttpStatus;

@AllArgsConstructor
public enum ResponseCode {

    /* 200 OK : 성공 */
    OK(HttpStatus.OK, "SUCCESS"),

    /* 400 BAD_REQUEST : 잘못된 요청 */
    INVALID_PARAMETER(HttpStatus.BAD_REQUEST, "INVALID DATA PARAMETERS"),
    INVALID_USER(HttpStatus.BAD_REQUEST, "INVALID_USER"),
    INVALID_AUTH(HttpStatus.BAD_REQUEST, "ACCESS_DENIED"),
    INVALID_PRODUCT(HttpStatus.BAD_REQUEST, "INVALID_DEBIT"),
    DUPLICATED_DEBIT(HttpStatus.BAD_REQUEST, "DUPLICATE_DEBIT"),
    DUPLICATED_BETTING(HttpStatus.BAD_REQUEST, "DUPLICATE_CREDIT"),

    /* 500 INTERNAL_SERVER_ERROR : 서버 동작 오류 */
    SYSTEM_ERROR(HttpStatus.INTERNAL_SERVER_ERROR, "UNKNOWN ERROR"),

    /* 503 SERVICE_UNABLE : 사이트 점검 */
    INSPECTION_SITE(HttpStatus.SERVICE_UNAVAILABLE, "현재 사이트 점검중입니다."),

    ;

    @Getter
    private final HttpStatus httpStatus;
    @Getter
    private final String detail;

}
