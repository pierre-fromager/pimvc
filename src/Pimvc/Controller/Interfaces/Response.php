<?php

/**
 * Description of basicInterface
 *
 * @author Pierre Fromager
 */

namespace Pimvc\Controller\Interfaces;

use Pimvc\App;

interface Response extends Request
{

    /**
     * __construct
     *
     * @param \Pimvc\App $app
     * @param array $params
     */
    public function __construct(App $app, $params = []);

    /**
     * redirect
     *
     * @param string $url
     */
    public function redirect($url);
    
    /**
     * getHtmlResponse
     *
     * @param Pimv\View $view
     * @param string $cookieName
     * @param string $cookieValue
     */
    public function getHtmlResponse($view, $cookieName = '', $cookieValue = '');

    /**
     * getJsonReponse
     *
     * @param mixed $content
     */
    public function getJsonResponse($content);
}
