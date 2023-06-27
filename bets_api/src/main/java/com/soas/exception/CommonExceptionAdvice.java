package com.soas.exception;

import com.soas.prop.CustomResponse;
import com.soas.prop.ResponseCode;
import org.springframework.http.ResponseEntity;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.ControllerAdvice;
import org.springframework.web.bind.annotation.ExceptionHandler;

import lombok.extern.log4j.Log4j2;
import org.springframework.web.servlet.NoHandlerFoundException;


/**
 * @author 	 : soas
 * @caption  : 공통 에러 처리
 */
@Log4j2
@ControllerAdvice
public class CommonExceptionAdvice {

	
	/* 404 */
	@ExceptionHandler({ NoHandlerFoundException.class })
	public String handleNoHandlerFoundException(Exception ex, Model model) {
		log.error("handleNoHandlerFoundException throw Exception" + ex);
		return "error/common";
	}

}
