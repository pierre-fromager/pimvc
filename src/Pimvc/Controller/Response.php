<?php
/**
 * Description of Pimvc\Controller\Response
 *
 * @author Pierre Fromager
 */
namespace Pimvc\Controller;

abstract class Response extends Request implements Interfaces\Response
{

    /**
     * __construct
     *
     * @param App $app
     * @param array $params
     */
    public function __construct(\Pimvc\App $app, array $params = [])
    {
        parent::__construct($app, $params);
    }

    /**
     * redirect
     *
     * @param string $url
     * @return \Pimvc\Http\Response
     */
    public function redirect($url)
    {
        return $this->getApp()
                ->getResponse()
                ->setContent('')
                ->setType(\Pimvc\Http\Response::TYPE_HTML)
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
    public function getHtmlResponse($view, $cookieName = '', $cookieValue = '', $httpCode = 200)
    {
        $response = $this->getApp()
            ->getResponse()
            ->setContent($view)
            ->setType(\Pimvc\Http\Response::TYPE_HTML)
            ->setHttpCode($httpCode);
        if ($cookieName && $cookieValue) {
            $response->withCookie($cookieName, $cookieValue);
        }
        return $response;
    }

    /**
     * getJsonResponse
     *
     * @param mixed $content
     * @param int $httpCode
     * @return \Pimvc\Http\Response
     */
    public function getJsonResponse($content, $httpCode = 200)
    {
        return $this->getApp()
                ->getResponse()
                ->setContent($content)
                ->setType(\Pimvc\Http\Response::TYPE_JSON)
                ->setHttpCode($httpCode);
    }
}
