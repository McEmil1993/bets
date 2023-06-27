package com.soas.util;

import java.io.File;
import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.lang.reflect.Array;
import java.math.BigDecimal;
import java.math.BigInteger;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLEncoder;
import java.security.MessageDigest;
import java.security.SecureRandom;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.Random;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import javax.servlet.http.HttpServletRequest;

import org.json.simple.JSONArray;

import com.fasterxml.jackson.core.JsonParseException;
import com.fasterxml.jackson.databind.JsonMappingException;
import com.fasterxml.jackson.databind.ObjectMapper;

import net.minidev.json.JSONObject;
import net.minidev.json.parser.JSONParser;


/**
 * @author 	 : soas
 * @caption  : 공통 함수 Class
 */
public class CommonUtil {

	

	/**
	 * 빈 문자열 <code>""</code>.
	 */
	public static final String EMPTY = "";

	public static void printStackTrace(Exception e) {
		if (e != null) {
			String errorMsg = "";
			for (int i = 0; i < e.getStackTrace().length; i++) {
				System.err.println(e.getStackTrace()[i].toString());
				errorMsg += e.getStackTrace()[i].toString();
			}
		}
	}
	
	public static void printStackTrace(StackTraceElement[] s) {
		if (s != null) {
			for (int i = 0; i <s.length; i++) {
				System.err.println(s[i].toString());
			}
		}
	}

	/**
	 * 문자열을 지정한 분리자에 의해 배열로 리턴하는 메서드.
	 * 
	 * @param source
	 *            원본 문자열
	 * @param separator
	 *            분리자
	 * @return result 분리자로 나뉘어진 문자열 배열
	 */
	public static String[] split(String source, String separator) throws NullPointerException {
		String[] returnVal = null;
		int cnt = 1;

		int index = source.indexOf(separator);
		int index0 = 0;
		while (index >= 0) {
			cnt++;
			index = source.indexOf(separator, index + 1);
		}
		returnVal = new String[cnt];
		cnt = 0;
		index = source.indexOf(separator);
		while (index >= 0) {
			returnVal[cnt] = source.substring(index0, index);
			index0 = index + 1;
			index = source.indexOf(separator, index + 1);
			cnt++;
		}
		returnVal[cnt] = source.substring(index0);

		return returnVal;
	}

	/**
	 * <p>
	 * {@link String#toLowerCase()}를 이용하여 소문자로 변환한다.
	 * </p>
	 *
	 * <pre>
	 * StringUtil.lowerCase(null)  = null
	 * StringUtil.lowerCase("")    = ""
	 * StringUtil.lowerCase("aBc") = "abc"
	 * </pre>
	 *
	 * @param str
	 *            소문자로 변환되어야 할 문자열
	 * @return 소문자로 변환된 문자열, null이 입력되면 <code>null</code> 리턴
	 */
	public static String lowerCase(String str) {
		if (str == null) {
			return null;
		}

		return str.toLowerCase();
	}

	/**
	 * <p>
	 * {@link String#toUpperCase()}를 이용하여 대문자로 변환한다.
	 * </p>
	 *
	 * <pre>
	 * StringUtil.upperCase(null)  = null
	 * StringUtil.upperCase("")    = ""
	 * StringUtil.upperCase("aBc") = "ABC"
	 * </pre>
	 *
	 * @param str
	 *            대문자로 변환되어야 할 문자열
	 * @return 대문자로 변환된 문자열, null이 입력되면 <code>null</code> 리턴
	 */
	public static String upperCase(String str) {
		if (str == null) {
			return null;
		}

		return str.toUpperCase();
	}

	/**
	 * <p>
	 * 입력된 String의 앞쪽에서 두번째 인자로 전달된 문자(stripChars)를 모두 제거한다.
	 * </p>
	 *
	 * <pre>
	 * StringUtil.stripStart(null, *)          = null
	 * StringUtil.stripStart("", *)            = ""
	 * StringUtil.stripStart("abc", "")        = "abc"
	 * StringUtil.stripStart("abc", null)      = "abc"
	 * StringUtil.stripStart("  abc", null)    = "abc"
	 * StringUtil.stripStart("abc  ", null)    = "abc  "
	 * StringUtil.stripStart(" abc ", null)    = "abc "
	 * StringUtil.stripStart("yxabc  ", "xyz") = "abc  "
	 * </pre>
	 *
	 * @param str
	 *            지정된 문자가 제거되어야 할 문자열
	 * @param stripChars
	 *            제거대상 문자열
	 * @return 지정된 문자가 제거된 문자열, null이 입력되면 <code>null</code> 리턴
	 */
	public static String stripStart(String str, String stripChars) {
		int strLen;
		if (str == null || (strLen = str.length()) == 0) {
			return str;
		}
		int start = 0;
		if (stripChars == null) {
			while ((start != strLen) && Character.isWhitespace(str.charAt(start))) {
				start++;
			}
		} else if (stripChars.length() == 0) {
			return str;
		} else {
			while ((start != strLen) && (stripChars.indexOf(str.charAt(start)) != -1)) {
				start++;
			}
		}

		return str.substring(start);
	}

	/**
	 * <p>
	 * 입력된 String의 뒤쪽에서 두번째 인자로 전달된 문자(stripChars)를 모두 제거한다.
	 * </p>
	 *
	 * <pre>
	 * StringUtil.stripEnd(null, *)          = null
	 * StringUtil.stripEnd("", *)            = ""
	 * StringUtil.stripEnd("abc", "")        = "abc"
	 * StringUtil.stripEnd("abc", null)      = "abc"
	 * StringUtil.stripEnd("  abc", null)    = "  abc"
	 * StringUtil.stripEnd("abc  ", null)    = "abc"
	 * StringUtil.stripEnd(" abc ", null)    = " abc"
	 * StringUtil.stripEnd("  abcyx", "xyz") = "  abc"
	 * </pre>
	 *
	 * @param str
	 *            지정된 문자가 제거되어야 할 문자열
	 * @param stripChars
	 *            제거대상 문자열
	 * @return 지정된 문자가 제거된 문자열, null이 입력되면 <code>null</code> 리턴
	 */
	public static String stripEnd(String str, String stripChars) {
		int end;
		if (str == null || (end = str.length()) == 0) {
			return str;
		}

		if (stripChars == null) {
			while ((end != 0) && Character.isWhitespace(str.charAt(end - 1))) {
				end--;
			}
		} else if (stripChars.length() == 0) {
			return str;
		} else {
			while ((end != 0) && (stripChars.indexOf(str.charAt(end - 1)) != -1)) {
				end--;
			}
		}

		return str.substring(0, end);
	}

	/**
	 * <p>
	 * 입력된 String의 앞, 뒤에서 두번째 인자로 전달된 문자(stripChars)를 모두 제거한다.
	 * </p>
	 *
	 * <pre>
	 * StringUtil.strip(null, *)          = null
	 * StringUtil.strip("", *)            = ""
	 * StringUtil.strip("abc", null)      = "abc"
	 * StringUtil.strip("  abc", null)    = "abc"
	 * StringUtil.strip("abc  ", null)    = "abc"
	 * StringUtil.strip(" abc ", null)    = "abc"
	 * StringUtil.strip("  abcyx", "xyz") = "  abc"
	 * </pre>
	 *
	 * @param str
	 *            지정된 문자가 제거되어야 할 문자열
	 * @param stripChars
	 *            제거대상 문자열
	 * @return 지정된 문자가 제거된 문자열, null이 입력되면 <code>null</code> 리턴
	 */
	public static String strip(String str, String stripChars) {
		if (isEmpty(str)) {
			return str;
		}

		String srcStr = str;
		srcStr = stripStart(srcStr, stripChars);

		return stripEnd(srcStr, stripChars);
	}

	/**
	 * 문자열 A에서 Z사이의 랜덤 문자열을 구하는 기능을 제공 시작문자열과 종료문자열 사이의 랜덤 문자열을 구하는 기능
	 *
	 * @param startChr
	 *            - 첫 문자
	 * @param endChr
	 *            - 마지막문자
	 * @return 랜덤문자
	 * @exception MyException
	 * @see
	 */
	public static String getRandomStr(char startChr, char endChr) {

		int randomInt;
		String randomStr = null;

		// 시작문자 및 종료문자를 아스키숫자로 변환한다.
		int startInt = Integer.valueOf(startChr);
		int endInt = Integer.valueOf(endChr);

		// 시작문자열이 종료문자열보가 클경우
		if (startInt > endInt) {
			throw new IllegalArgumentException("Start String: " + startChr + " End String: " + endChr);
		}

		// 랜덤 객체 생성
		SecureRandom rnd = new SecureRandom();

		do {
			// 시작문자 및 종료문자 중에서 랜덤 숫자를 발생시킨다.
			randomInt = rnd.nextInt(endInt + 1);
		} while (randomInt < startInt); // 입력받은 문자 'A'(65)보다 작으면 다시 랜덤 숫자 발생.

		// 랜덤 숫자를 문자로 변환 후 스트링으로 다시 변환
		randomStr = (char) randomInt + "";

		// 랜덤문자열를 리턴
		return randomStr;
	}
	

	public static String getRandomStr(int size, boolean tolower) {
    	Random ran = new Random();
        StringBuffer sb = new StringBuffer();
        int num = 0;
        
        do {
            num = ran.nextInt(75)+48;
            if((num>=48 && num<=57) || (num>=65 && num<=90) || (num>=97 && num<=122)) {
                sb.append((char)num);
            }else {
                continue;
            }
        } while (sb.length() < size);
        
		if(tolower) {
			return sb.toString().toLowerCase();
		}
		return sb.toString();
	}

	public static String getRandomNum(int min, int max) {
		SecureRandom rnd = new SecureRandom();
		int val = rnd.nextInt((max-min)+1) + min ;
		return String.valueOf(val);
	}

	public static String filePathBlackList(String value) {
		String returnValue = value;
		if (returnValue == null || returnValue.trim().equals("")) {
			return "";
		}

		returnValue = returnValue.replaceAll("\\.\\./", ""); // ../
		returnValue = returnValue.replaceAll("\\.\\.\\\\", ""); // ..\

		return returnValue;
	}


	public static String filePathWhiteList(String value) {
		return value;
	}

	public static String convertOsPath(String path) {
		if (File.separator.equals("\\") )
			path = path.replaceAll("/", "\\\\");
		return path;
	}
	
	public static String getFileName(String path) {
		int pos = path.lastIndexOf('.');
		if (pos != -1) {
			path = path.substring(path.lastIndexOf(File.separator) + 1, pos);
		} else {
			path = path.substring(path.lastIndexOf(File.separator) + 1, pos);
		}
		path = path.replace("\\", "/");
		return path;
	}
	
	public static String getFileExt(String path) {
		return path.substring(path.lastIndexOf('.') + 1, path.length());
	}
	
	public static String getFilePath(String path) {
		path = path.substring(0, path.lastIndexOf(File.separator));
		path = path.replace("\\", "/");
		return path;
	}
	
	public static boolean isIPAddress(String str) {
		Pattern ipPattern = Pattern.compile("\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}");

		return ipPattern.matcher(str).matches();
    }

	 public static String removeCRLF(String parameter) {
		 return parameter.replaceAll("\r", "").replaceAll("\n", "");
	 }


	/**
	 * 클라이언트(Client)의 IP주소를 조회하는 기능
	 * 
	 * @param HttpServletRequest
	 *            request Request객체
	 * @return String ipAddr IP주소
	 * @exception Exception
	 */
	public static String getIP(HttpServletRequest request) {

		String ip = request.getHeader("X-Forwarded-For");

        if (ip == null) {
            ip = request.getHeader("Proxy-Client-IP");
        }
        if (ip == null) {
            ip = request.getHeader("WL-Proxy-Client-IP"); // 웹로직
        }
        if (ip == null) {
            ip = request.getHeader("HTTP_CLIENT_IP");
        }
        if (ip == null) {
            ip = request.getHeader("HTTP_X_FORWARDED_FOR");
        }
        if (ip == null) {
            ip = request.getRemoteAddr();
        }

        if (!isEmpty(ip))
        	ip = ip.replaceAll("\r", "").replaceAll("\n", "");
        
		return ip;
	}

	/**
	 * 클라이언트(Client)의 웹브라우저 종류를 조회하는 기능
	 * 
	 * @param HttpServletRequest
	 *            request Request객체
	 * @return String webKind 웹브라우저 종류
	 * @exception Exception
	 */
	public static String getWebKind(HttpServletRequest request) throws Exception {

		String user_agent = request.getHeader("user-agent");

		// 웹브라우저 종류 조회
		String webKind = "";
		if (user_agent.toUpperCase().indexOf("GECKO") != -1) {
			if (user_agent.toUpperCase().indexOf("NESCAPE") != -1) {
				webKind = "Netscape (Gecko/Netscape)";
			} else if (user_agent.toUpperCase().indexOf("FIREFOX") != -1) {
				webKind = "Mozilla Firefox (Gecko/Firefox)";
			} else {
				webKind = "Mozilla (Gecko/Mozilla)";
			}
		} else if (user_agent.toUpperCase().indexOf("MSIE") != -1) {
			if (user_agent.toUpperCase().indexOf("OPERA") != -1) {
				webKind = "Opera (MSIE/Opera/Compatible)";
			} else {
				webKind = "Internet Explorer (MSIE/Compatible)";
			}
		} else if (user_agent.toUpperCase().indexOf("SAFARI") != -1) {
			if (user_agent.toUpperCase().indexOf("CHROME") != -1) {
				webKind = "Google Chrome";
			} else {
				webKind = "Safari";
			}
		} else if (user_agent.toUpperCase().indexOf("THUNDERBIRD") != -1) {
			webKind = "Thunderbird";
		} else {
			webKind = "Other Web Browsers";
		}
		return webKind;
	}

	/**
	 * 클라이언트(Client)의 웹브라우저 버전을 조회하는 기능
	 * 
	 * @param HttpServletRequest
	 *            request Request객체
	 * @return String webVer 웹브라우저 버전
	 * @exception Exception
	 */
	public static String getWebVer(HttpServletRequest request) throws Exception {

		String user_agent = request.getHeader("user-agent");

		// 웹브라우저 버전 조회
		String webVer = "";
		String[] arr = { "MSIE", "OPERA", "NETSCAPE", "FIREFOX", "SAFARI" };
		for (int i = 0; i < arr.length; i++) {
			int s_loc = user_agent.toUpperCase().indexOf(arr[i]);
			if (s_loc != -1) {
				int f_loc = s_loc + arr[i].length();
				webVer = user_agent.toUpperCase().substring(f_loc, f_loc + 5);
				webVer = webVer.replaceAll("/", "").replaceAll(";", "").replaceAll("^", "").replaceAll(",", "")
						.replaceAll("//.", "");
			}
		}
		return webVer;
	}
	

    
    /**
     * Map을 json으로 변환한다.
     *
     * @param map Map<String, Object>.
     * @return JSONObject.
     */
    public static JSONObject getJsonStringFromMap( Map<String, Object> map )
    {
        JSONObject jsonObject = new JSONObject();
        for( Map.Entry<String, Object> entry : map.entrySet() ) {
            String key = entry.getKey();
            Object value = entry.getValue();
            jsonObject.put(key, value);
        }
        
        return jsonObject;
    }
    
    /**
     * List<Map>을 jsonArray로 변환한다.
     *
     * @param list List<Map<String, Object>>.
     * @return JSONArray.
     */
    public static JSONArray getJsonArrayFromList( List<Map<String, Object>> list )
    {
        JSONArray jsonArray = new JSONArray();
        for( Map<String, Object> map : list ) {
            jsonArray.add( getJsonStringFromMap( map ) );
        }
        
        return jsonArray;
    }
    
    /**
     * List<Map>을 jsonString으로 변환한다.
     *
     * @param list List<Map<String, Object>>.
     * @return String.
     */
    public static String getJsonStringFromList( List<Map<String, Object>> list )
    {
        JSONArray jsonArray = getJsonArrayFromList( list );
        return jsonArray.toJSONString();
    }
 
    /**
     * JsonObject를 Map<String, String>으로 변환한다.
     *
     * @param jsonObj JSONObject.
     * @return Map<String, Object>.
     */
    @SuppressWarnings("unchecked")
    public static Map<String, Object> getMapFromJsonObject( JSONObject jsonObj )
    {
        Map<String, Object> map = null;
        
        try {
            
            map = new ObjectMapper().readValue(jsonObj.toJSONString(), Map.class) ;
            
        } catch (JsonParseException e) {
            printStackTrace(e);
        } catch (JsonMappingException e) {
            printStackTrace(e);
        } catch (IOException e) {
            printStackTrace(e);
        }
 
        return map;
    }
 
    /**
     * JsonArray를 List<Map<String, String>>으로 변환한다.
     *
     * @param jsonArray JSONArray.
     * @return List<Map<String, Object>>.
     */
    public static List<Map<String, Object>> getListMapFromJsonArray( JSONArray jsonArray )
    {
        List<Map<String, Object>> list = new ArrayList<Map<String, Object>>();
        
        if( jsonArray != null )
        {
            int jsonSize = jsonArray.size();
            for( int i = 0; i < jsonSize; i++ )
            {
                Map<String, Object> map = getMapFromJsonObject( ( JSONObject ) jsonArray.get(i) );
                list.add( map );
            }
        }
        
        return list;
    }
    
//    @SuppressWarnings("deprecation")
	public static JSONObject toJsonObject(String json) {
    	try {
    		JSONParser p = new JSONParser(JSONParser.MODE_JSON_SIMPLE);
    		return (JSONObject)p.parse(json);
    	} catch(Exception e) {
    		System.out.println(e.toString());
    	}

    	return null;
    }
	
	
	/**
	 * 전화번호 포멧으로 변경
	 * @param src
	 * @return
	 */
	public static String formatPhoneNum(String src) {
		if (isEmpty(src)) {
			return "";
		}
		if (src.length() == 8) {
			return src.replaceFirst("^([0-9]{4})([0-9]{4})$", "$1-$2");
		} else if (src.length() == 12) {
			return src.replaceFirst("(^[0-9]{4})([0-9]{4})([0-9]{4})$", "$1-$2-$3");
		}
	    return src.replaceFirst("(^02|[0-9]{3})([0-9]{3,4})([0-9]{4})$", "$1-$2-$3");
	}
	
	
	/**
	 * 비밀번호가  대소문자,숫자,특수문자 조합하고 8 ~14자리면
	 * @param str
	 * @return true 조건에 알맞은 비밀번호, false 조건에 안맞는 비밀번호
	 */
	public static boolean checkPassCombinantion(String str) {
//		boolean a = Pattern.matches("^(?=.*\\d)((?=.*[a-z])|(?=.*[A-Z])).{4,12}$", str);
		boolean a = Pattern.matches("^[A-Za-z0-9]{4,12}$", str);
		if (!isEmpty(str))
			return a;
 		
		return false;
	}

	public static boolean checkPassCombinantionId(String str) {
		boolean a = Pattern.matches("^[A-Za-z0-9]{4,12}$", str);
		if (!isEmpty(str))
			return a;

		return false;
	}

	public static boolean checkPassCombinantionNickname(String str) {
//		boolean a = Pattern.matches("^(?=.*\\d)(?=.*[~`!@#$%\\^&*()-])(?=.*[a-z])(?=.*[A-Z]).{8,14}$", str);
		boolean a = Pattern.matches("^[가-힣]{2,8}$", str);
		if (!isEmpty(str))
			return a;

		return false;
	}

	/**
	 * space 포함여부 확인
	 * @param str
	 * @return true 스페이스포함, false 스페이스 미포함
	 */
	public static boolean checkSpace(String str) {
		for (int i = 0; i < str.length(); i++) {
			if (str.charAt(i) == ' ')
				return true;
		}
		
		return false;
	}
	
	/**
	 * 연속된 숫자가 4번 반복되면 true
	 * @param str
	 * @return
	 */
	public static boolean checkSequence(String str) {
		int o = 0, d = 0, p = 0, n = 0, limit = 4;

		for (int i = 0; i < str.length(); i++) {
			char v = str.charAt(i);
			if (i > 0 && (p = o - v) > -2 && p < 2 && (n = p == d ? n + 1 : 0) > limit - 3)
				return true;
			d = p;
			o = v;
		}
		return false;
	}
	
	/**
	 * 연속된 숫자 4자리 안됨, 스페이스 포함되면 안됨,대소문자,숫자,특수문자,8~14자
	 * @param str
	 * @return true 사용가능 패스웨드, false 사용못하는 패스워드
	 */
	public static boolean checkPassword(String str) {
		if (checkSequence(str) || /* 연속된 숫자면 안된다 */
			checkSpace(str) || /* 스페이스가 포함되면 안된다 */
			!checkPassCombinantion(str)) /* 대소문자,숫자,특수문자,8~14자 */
			return false;

		return true;
	}


	public static boolean checkPasswordLight(String str) {
		if (	checkSpace(str) || /* 스페이스가 포함되면 안된다 */
				!checkPassCombinantion(str)) /* 대소문자,숫자,특수문자,8~14자 */
			return false;

		return true;
	}

	public static boolean checkNickname(String str) {
		if (	checkSpace(str) || /* 스페이스가 포함되면 안된다 */
				!checkPassCombinantionNickname(str)) /* 대소문자,숫자,특수문자,8~14자 */
			return false;

		return true;
	}
	
	public static String removeHtmlTag(String str){
		return str.replaceAll("<(/)?([a-zA-Z]*)(\\s[a-zA-Z]*=[^>]*)?(\\s)*(/)?>", "");
	}
	
	/**
	 * @MethodName  : isValidEmail
	 * @author 	    : soas
	 * @since       : 2020. 9. 2
	 * @caption	    : 이메일 형식인지 체크
	 * @param 		: String
	 * @return		: boolean 
	 */
	public static boolean isValidEmail(String email) { 
		boolean err = false; 
		String regex = "^[A-Za-z0-9_\\.\\-]+@[A-Za-z0-9\\-]+\\.[A-Za-z0-9\\-]+"; 
		Pattern p = Pattern.compile(regex); 
		Matcher m = p.matcher(email); 
		
		if(m.matches()) { 
			err = true; 
		} 
		
		return err; }

	
	/**
	 * @MethodName  : isEmpty
	 * @author 	    : soas
	 * @since       : 2020. 9. 10
	 * @caption	    : Object null 혹은 빈 값 체크
	 * @param 		: Object
	 * @return		: Boolean  
	 */
	@SuppressWarnings("rawtypes")
	public static Boolean isEmpty(Object obj) {
		  if (obj instanceof String) return obj == null || "".equals(obj.toString().trim());
		  else if (obj instanceof List) return obj == null || ((List) obj).isEmpty();
		  else if (obj instanceof Map) return obj == null || ((Map) obj).isEmpty();
		  else if (obj instanceof Object[]) return obj == null || Array.getLength(obj) == 0;
		  else return obj == null;
		 }


	/**
	 * @MethodName  : getBigDecimal
	 * @author 	    : soas
	 * @since       : 2020. 9. 15
	 * @caption	    : Object => BigDecimal 변환
	 */
	public static BigDecimal getBigDecimal( Object value ) {
        BigDecimal ret = null;
        if( value != null ) {
            if( value instanceof BigDecimal ) {
                ret = (BigDecimal) value;
            } else if( value instanceof String ) {
                ret = new BigDecimal( (String) value );
            } else if( value instanceof BigInteger ) {
                ret = new BigDecimal( (BigInteger) value );
            } else if( value instanceof Number ) {
                ret = new BigDecimal( ((Number)value).doubleValue() );
            } else {
                throw new ClassCastException("Not possible to coerce ["+value+"] from class "+value.getClass()+" into a BigDecimal.");
            }
        }
        return ret;
    }


	public static String cryptWithMD5(String pass){
		try {
			MessageDigest md = MessageDigest.getInstance("MD5");
			byte[] passBytes = pass.getBytes();
			md.reset();
			byte[] digested = md.digest(passBytes);
			StringBuffer sb = new StringBuffer();
			for(int i=0;i<digested.length;i++){
				sb.append(Integer.toHexString(0xff & digested[i]));
			}
			return sb.toString();
		} catch (Exception e) {

		}
		return null;
	}

	public static String urlEncodeUTF8(Map<?,?> map) {
		StringBuilder sb = new StringBuilder();
		for (Map.Entry<?,?> entry : map.entrySet()) {
			if (sb.length() > 0) {
				sb.append("&");
			}
			sb.append(String.format("%s=%s",
					urlEncodeUTF8(entry.getKey().toString()),
					urlEncodeUTF8(entry.getValue().toString())
			));
		}
		return sb.toString();
	}


	public static String urlEncodeUTF8(String s) {
		try {
			return URLEncoder.encode(s, "UTF-8");
		} catch (UnsupportedEncodingException e) {
			throw new UnsupportedOperationException(e);
		}
	}


	/**
	 * 전달된 파라미터에 맞게 난수를 생성한다
	 * @param len : 생성할 난수의 길이
	 * @param dupCd : 중복 허용 여부 (1: 중복허용, 2:중복제거)
	 *
	 * Created by 닢향
	 * http://niphyang.tistory.com
	 */
	public static String numberGen(int len, int dupCd ) {

		Random rand = new Random();
		String numStr = ""; //난수가 저장될 변수

		for(int i=0;i<len;i++) {

			//0~9 까지 난수 생성
			String ran = Integer.toString(rand.nextInt(10));

			if(dupCd==1) {
				//중복 허용시 numStr에 append
				numStr += ran;
			}else if(dupCd==2) {
				//중복을 허용하지 않을시 중복된 값이 있는지 검사한다
				if(!numStr.contains(ran)) {
					//중복된 값이 없으면 numStr에 append
					numStr += ran;
				}else {
					//생성된 난수가 중복되면 루틴을 다시 실행한다
					i-=1;
				}
			}
		}
		return numStr;
	}


	public static String getHost(HttpServletRequest req) throws MalformedURLException {
		URL requestURL = new URL(req.getRequestURL().toString());
		String host = requestURL.getHost();
		if(host.startsWith("www"))
			host = host.substring("www".length()+1);

		return host;
	}

}
