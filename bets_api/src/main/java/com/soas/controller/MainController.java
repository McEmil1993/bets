package com.soas.controller;

import java.util.Locale;

import javax.servlet.http.HttpServletRequest;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;


import lombok.extern.log4j.Log4j2;


@Log4j2
@Controller
public class MainController {


//	private GameService gameService;
//	private CustomerService customerService;

//	@Autowired
//	public MainController(GameService gameService, CustomerService customerService) {
//		this.gameService = gameService;
//		this.customerService = customerService;
//	}


	@RequestMapping(value = "/", method = RequestMethod.GET)
	public String home(Locale locale, Model model, HttpServletRequest req) throws Exception {

		return "/main";
	}
}
