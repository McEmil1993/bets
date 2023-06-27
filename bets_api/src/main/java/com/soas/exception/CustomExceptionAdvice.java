package com.soas.exception;

import com.soas.prop.CustomException;
import com.soas.prop.CustomResponse;
import lombok.extern.log4j.Log4j2;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.ExceptionHandler;
import org.springframework.web.bind.annotation.ResponseBody;
import org.springframework.web.bind.annotation.RestControllerAdvice;


/**
 * @author 	 : soas
 * @caption  : CustomException 처리
 */
@Log4j2
@ResponseBody
@RestControllerAdvice
public class CustomExceptionAdvice {


	@ExceptionHandler({ CustomException.class })
	protected ResponseEntity<CustomResponse> handleCustomException(CustomException e) {
		log.info("handleCustomException throw CustomException : " + e.getResponseCode());
		return CustomResponse.toResponseEntity(e.getResponseCode());
	}


}
