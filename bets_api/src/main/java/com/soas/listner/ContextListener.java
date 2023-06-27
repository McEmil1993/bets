package com.soas.listner;

import lombok.extern.log4j.Log4j2;

import javax.servlet.ServletContextEvent;
import javax.servlet.ServletContextListener;
import javax.servlet.annotation.WebListener;

@Log4j2
@WebListener
public class ContextListener implements ServletContextListener {

	public ContextListener(){}
	
	@Override
	public void contextInitialized(ServletContextEvent sce) {
		
		log.info("=======================================");
		log.info("ContextListner.contextInitialized");
		log.info("=======================================");
		
	}

	@Override
	public void contextDestroyed(ServletContextEvent sce) {
		
		log.info("=======================================");
		log.info("ContextListner.contextDestroyed");
		log.info("=======================================");
		
	}

}
