<?php

/**
 * Description of Response Controller
 *
 * @author Pierre Fromager
 */

namespace Pimvc\Controller;

use Pimvc\App;
use Pimvc\Http\Response as httpResponse;

abstract class Response extends Request implements Interfaces\Response{
    
    /**
     * __construct
     * 
     * @param App $app
     * @param array $params
     */
    public function __construct(\Pimvc\App $app, $params = []) {
        parent::__construct($app, $params);
    }
    
    /**
     * redirect
     * 
     * @param string $url
     * @return Pimvc\Http\Response
     */
    public function redirect($url) {
        return $this->getApp()
            ->getResponse()
            ->setContent('')
            ->setType(httpResponse::TYPE_HTML)
            ->setHttpCode(302)
            ->redirect($url);
    }
       
    /**
     * getHtmlResponse
     * 
     * @param string|View $view
     * @param string $cookieName
     * @param string $cookieValue
     * @param int $httpCode
     * @return \Pimvc\Http\Response
     */
    public function getHtmlResponse($view, $cookieName = '', $cookieValue = '', $httpCode = 200) {
        $response = $this->getApp()
            ->getResponse()
            ->setContent($view)
            ->setType(httpResponse::TYPE_HTML)
            ->setHttpCode($httpCode);
        if ($cookieName && $cookieValue) {
            $response->withCookie($cookieName, $cookieValue);
        }
        return $response;
    }

    /**
     * getJsonReponse
     * 
     * @param mixed $content
     * @return \Pimvc\Http\Response
     */
    public function getJsonReponse($content) {
        return $this->getApp()->getResponse()
            ->setContent($content)
            ->setType(httpResponse::TYPE_JSON)
            ->setHttpCode(200);
    }

}
