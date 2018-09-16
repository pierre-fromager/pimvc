<?php

/**
 * Description of Standart
 *
 * @author pierrefromager
 */

namespace Pimvc\Views\Helpers\Widgets;

use Pimvc\Views\Helpers\Widget as widgetHelper;
use Pimvc\Views\Helpers\Widgets\Interfaces\Rdash as widgetInterface;

class Rdash extends widgetHelper implements widgetInterface
{

    /**
     * __construct
     *
     */
    public function __construct()
    {
        return parent::__construct()
                ->setTitleDecorator('h3')
                ->setSectionDecorator(self::PARAM_SECTION)
                ->setSectionOptions([self::PARAM_CLASS => 'widget'])
                ->setBodyDecorator(self::DECORATOR_BODY)
                ->setBodyOptions([self::PARAM_CLASS => self::CLASS_BODY])
                ->setHeaderDecorator('div')
                ->setHeaderOptions([self::PARAM_CLASS => 'widget-header'])
                ->setTitleOptions()
                ->setFooterDecorator('div')
                ->setFooteOptions([self::PARAM_CLASS => 'widget-footer'])
                ->render();
    }
}
