package com.soas.initializer;

import lombok.extern.log4j.Log4j2;
import org.springframework.beans.factory.InitializingBean;
import org.springframework.stereotype.Service;
import org.springframework.web.WebApplicationInitializer;

import javax.servlet.ServletContext;
import java.util.TimeZone;

@Log4j2
@Service
public class WebAppInitializer implements WebApplicationInitializer, InitializingBean {
	
	@Override
	public void onStartup(ServletContext container) {
		log.info("=======================================");
		log.info("WebAppInitializer.onStartup");
		log.info("=======================================");
		TimeZone.setDefault(TimeZone.getTimeZone("Asia/Seoul"));
		
	}
	
	@Override
	public void afterPropertiesSet() throws Exception {
		log.info("=======================================");
		log.info("WebAppInitializer.afterPropertiesSet");
		log.info("=======================================");
		
	}
}
