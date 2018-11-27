<?php
/**
 * Description of requestInterface
 *
 * @author Pierre Fromager
 */
namespace Pimvc\Controller\Interfaces;

interface Request extends Base
{

    /**
     * __construct
     *
     * @param \Pimvc\App $app
     * @param array $params
     */
    public function __construct(\Pimvc\App $app, array $params = []);

    /**
     * getParams
     *
     * @return array
     */
    public function getParams($key = '');

    /**
     * hasValue
     *
     * @param string $param
     * @return mixed
     */
    public function hasValue($param);

    /**
     * forward
     *
     * @param string $controller
     * @param string $action
     * @param array $params
     * @return mixed
     */
    public function forward($controller = '', $action = '', $params = []);
}
