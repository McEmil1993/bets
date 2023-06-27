package com.soas.interceptor;

import com.soas.prop.CustomException;
import com.soas.prop.CustomResponse;
import com.soas.prop.Properties;
import com.soas.prop.ResponseCode;
import lombok.extern.log4j.Log4j2;
import org.springframework.web.servlet.ModelAndView;
import org.springframework.web.servlet.handler.HandlerInterceptorAdapter;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;


/**
 * @author 	 : soas
 * @caption  : Rest API Interceptor
 */
@Log4j2
public class RestInterceptor extends HandlerInterceptorAdapter {



	@Override
	public boolean preHandle(HttpServletRequest request, HttpServletResponse response, Object handler) throws Exception {

		String secretKey = request.getHeader("secret-key");
		log.info("secretKey : " + secretKey);

		if(!Properties.KPLAY_SECRET_KEY.equals(secretKey))
			throw new CustomException(ResponseCode.INVALID_AUTH);

		return super.preHandle(request, response, handler);
	}

	@Override
	public void postHandle(HttpServletRequest request, HttpServletResponse response, Object handler, ModelAndView modelAndView) throws Exception {

		super.postHandle(request, response, handler, modelAndView);
	}

//	private void writeErrorResult(HttpServletResponse response, int status, String message) {
//		response.setCharacterEncoding("utf-8");
//		response.setContentType("application/json");
//		ResultCode ret = new ResultCode();
//		ret.setCode(status);
//		ret.setMessage(message);
//		try {
//			PrintWriter out = response.getWriter();
//			out.write(ret.toString());
//			out.flush();
//			out.close();
//		} catch(Exception ex) {
//			CommonUtil.printStackTrace(ex);
//		}
//	}
	
}
