package com.soas.prop;

import com.fasterxml.jackson.databind.ObjectMapper;
import lombok.Builder;
import lombok.Getter;
import lombok.ToString;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;

import java.util.HashMap;
import java.util.Map;

@Getter
@Builder
public class CustomResponse {

    private final int status;
    private final int balance;
    private final String error;

    public static ResponseEntity<CustomResponse> toResponseEntity(ResponseCode responseCode) {
        int status = responseCode.getHttpStatus().value() == 200 ? 1 : 0;
        return ResponseEntity
                .status(HttpStatus.OK)
                .body(CustomResponse.builder()
                        .status(status)
                        .error(responseCode.getDetail())
                        .build()
                );
    }


    public static ResponseEntity<CustomResponse> toResponseEntity(ResponseCode responseCode, int balance) {
        int status = responseCode.getHttpStatus().value() == 200 ? 1 : 0;
        return ResponseEntity
                .status(HttpStatus.OK)
                .body(CustomResponse.builder()
                        .status(status)
                        .error(responseCode.getDetail())
                        .balance(balance)
                        .build()
                );
    }


    public static String toString(ResponseCode responseCode) {

        CustomResponse customResponse = CustomResponse.builder()
//                .code(responseCode.getHttpStatus().value())
                .error(responseCode.getHttpStatus().name())
//                .name(responseCode.name())
//                .message(responseCode.getDetail())
                .build();

        String json = "";
        try {
            json = new ObjectMapper().writeValueAsString(customResponse);
        } catch(Exception e) {
        }
        return json;

    }


}
