package com.soas.aop;


import java.util.Enumeration;

import javax.servlet.http.HttpServletRequest;

import org.aspectj.lang.ProceedingJoinPoint;
import org.aspectj.lang.annotation.Around;
import org.aspectj.lang.annotation.Aspect;
import org.springframework.stereotype.Component;
import org.springframework.web.context.request.RequestContextHolder;
import org.springframework.web.context.request.ServletRequestAttributes;
import org.springframework.web.multipart.MultipartHttpServletRequest;

import lombok.extern.log4j.Log4j2;


/**
 * @author 	 : soas
 * @caption  : AOP Log Class
 * 			   기본 로깅 작업 
 */
@Log4j2
@Aspect
@Component 
public class LogAdvice {
	

	/* API Controller URI Log */
	@Around("execution(* com..api.*Controller.*(..))")
	public Object beforeApi(ProceedingJoinPoint pjp) throws Throwable {
		
		Object result = null;

		HttpServletRequest req = ((ServletRequestAttributes) RequestContextHolder.currentRequestAttributes()).getRequest();
		log.info("========================================================");
		log.info("# CALL API : " + req.getRequestURI());

		for (Object obj : pjp.getArgs()) {
			if (obj instanceof HttpServletRequest || obj instanceof MultipartHttpServletRequest) {
				HttpServletRequest request = (HttpServletRequest) obj;
				log.info("URI : " + request.getRequestURI());
				Enumeration<?> params = request.getParameterNames();
				while (params.hasMoreElements()) {
					String name = (String) params.nextElement();
					log.info(name + " : " + request.getParameter(name));
				}
			}
		}
		log.info("========================================================");
		
		result = pjp.proceed();
		
		return result;
	
	}
	
	
}
