<?php

namespace Pimvc\Views\Helpers\Toolbar;

use \Pimvc\Html\Element\Decorator;

class Glyph implements Interfaces\Glyph
{
    protected $content = '';
    protected $params = [];
    protected $exclude = [];
    protected $baseUrl = '';
    protected $editLink = '';
    protected $deleteLink = '';
    protected $controlerLink = '';
    protected $id = '';
    protected $actionPrefix;
    protected $actionSuffix;

    /**
     * @see __contruct
     *
     * @param array $params
     * @param string $controler
     */
    public function __construct($params, $controler, $exclude = [], $actionPrefix = '', $actionSuffix = '')
    {
        $this->setBaseUrl();
        $defaultExclude =  $this->getDefaultExclude();
        $this->exclude = (!empty($exclude))
            ? array_merge($defaultExclude, $exclude)
            : $defaultExclude;
        $this->id = (isset($params['id']))
            ? $params['id']
            : '';
        $controlSplit = (strpos($controler, DIRECTORY_SEPARATOR) !== false)
            ? explode(DIRECTORY_SEPARATOR, $controler)
            : $controler;
        $this->controlerLink = (is_array($controlSplit))
            ? $controlSplit[0]
            : $controler;
        $this->actionPrefix = $actionPrefix;
        $this->actionSuffix = $actionSuffix;
        if (!empty($this->id) && !empty($this->controlerLink)) {
            $this->process();
        }
    }
    
    /**
     * setBaseUrl
     *
     */
    private function setBaseUrl()
    {
        $this->baseUrl = \Pimvc\App::getInstance()->getRequest()->getBaseUrl()
            . DIRECTORY_SEPARATOR;
    }
    
    /**
     * getDefaultExclude
     *
     * @return array
     */
    private function getDefaultExclude()
    {
        return [
            self::EXCLUDE_EDIT => false
            , self::EXCLUDE_DETAIL => false
            , self::EXCLUDE_DELETE => false
            , self::EXCLUDE_VALIDATE => false
            , self::EXCLUDE_REFUSE => false
            , self::EXCLUDE_PDF => false
            , self::EXCLUDE_MAILTASK => true
            , self::EXCLUDE_IMPORT => false
            , self::EXCLUDE_NEWSLETTER => false
            , self::EXCLUDE_SHIPPING => false
            , self::EXCLUDE_LWORKORDER => false
            , self::EXCLUDE_PEOPLE => false
            , self::EXCLUDE_ORDER => true
            , self::EXCLUDE_SUBTTITLE => true
            , self::EXCLUDE_CLONE => true
        ];
    }

    /**
     * getGlyphLink
     *
     * @param type $imgTitle
     * @param type $imgFilename
     * @param type $imgClass
     * @param type $imgLink
     * @param type $confirmMessage
     * @return type
     */
    private function getGlyphLink($imgTitle, $imgFilename, $imgClass, $imgLink, $confirmMessage = '', $target = '_self')
    {
        $iconOptions = array(
            'class' => 'glyphicon glyphicon-' . $imgFilename
        );
        $icon = new Decorator('i', ' ', $iconOptions);
        $iconWrapperOptions = array(
            'class' => 'btn btn-default btn-xs ' . $imgClass
            , 'alt' => $imgTitle
            , 'title' => $imgTitle
        );
        $iconWrapper = new Decorator(
            'span',
            (string) $icon,
            $iconWrapperOptions
        );
        $linkOptions = array(
            'href' => $this->baseUrl . $imgLink
            , 'target' => $target
        );
        if (!empty($confirmMessage)) {
            $linkOptions['onclick'] = 'return jsconfirm('
                . 'window.event,'
                . '\'' . $confirmMessage . '\''
                . ')';
        }
        $link = new Decorator(
            'a',
            (string) $iconWrapper,
            $linkOptions
        );
        return (string) $link;
    }
 
    /**
     * getEditLink returns link for editing
     *
     * @return string
     */
    private function getEditLink()
    {
        $editLink = (!$this->exclude[self::EXCLUDE_EDIT])
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_EDIT_TITLE,
                self::IMG_EDIT_FILE,
                self::IMG_EDIT_CLASS,
                $this->getActionLink(self::EDIT_ACTION),
                $confirm = self::CONFIRM_MESSAGE_EDIT
            )
            : '';
        return $editLink;
    }
    
    /**
     * getActionLink
     *
     * @param string $action
     * @return string
     */
    private function getActionLink($action)
    {
        return $this->controlerLink . DIRECTORY_SEPARATOR
            . $this->actionPrefix . $action . $this->actionSuffix
            . DIRECTORY_SEPARATOR . 'id' . DIRECTORY_SEPARATOR . $this->id;
    }

    /**
     * getPdfLink returns link for printing
     *
     * @return string
     */
    private function getPdfLink()
    {
        $editLink = (!$this->exclude[self::EXCLUDE_PDF])
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_PDF_TITLE,
                self::IMG_PDF_FILE,
                self::IMG_PDF_CLASS,
                $this->controlerLink . self::PDF_ACTION . $this->id,
                $confirmMessage = self::CONFIRM_MESSAGE_PRINT,
                $target = '_blank'
            )
            : '';
        return $editLink;
    }
    
    /**
     * getDetailLink returns link for editing
     *
     * @return string
     */
    private function getDetailLink()
    {
        $detailLink = (!$this->exclude[self::EXCLUDE_DETAIL])
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_DETAIL_TITLE,
                self::IMG_DETAIL_FILE,
                self::IMG_DETAIL_CLASS,
                $this->getActionLink('detail')
            )
            : '';
        return $detailLink;
    }
    
    /**
     * getSubtitleLink returns link for subtitling
     *
     * @return string
     */
    private function getSubtitleLink()
    {
        $subtitleLink = (!$this->exclude[self::EXCLUDE_SUBTTITLE])
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_SUBTITLE_TITLE,
                self::IMG_SUBTITLE_FILE,
                self::IMG_SUBTITLE_CLASS,
                $this->controlerLink . self::SUBTITLE_ACTION . $this->id
            )
            : '';
        return $subtitleLink;
    }
    
    /**
     * getCloneLink returns link for cloning object
     *
     * @return string
     */
    private function getCloneLink()
    {
        $cloneLink = (!$this->exclude[self::EXCLUDE_CLONE])
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_CLONE_TITLE,
                self::IMG_CLONE_FILE,
                self::IMG_CLONE_CLASS,
                $this->getActionLink('duplicate'),
                self::CONFIRM_MESSAGE_CLONE
            )
            : '';
        return $cloneLink;
    }
    
    /**
     * getPeopleLink returns link for people file
     *
     * @return string
     */
    private function getPeopleLink()
    {
        $detailLink = (!$this->exclude[self::EXCLUDE_PEOPLE])
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_PEOPLE_TITLE,
                self::IMG_PEOPLE_FILE,
                self::IMG_PEOPLE_CLASS,
                $this->getActionLink('people')
            )
            : '';
        return $detailLink;
    }
    
    /**
     * getDeleteLink returns link for deleting article
     *
     * @return string
     */
    private function getDeleteLink()
    {
        $deleteLink = (!$this->exclude[self::EXCLUDE_DELETE])
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_DEL_TITLE,
                self::IMG_DEL_FILE,
                self::IMG_DEL_CLASS,
                $this->getActionLink('delete'),
                $confirm = self::CONFIRM_MESSAGE_DELETE
            )
            : '';
        return $deleteLink;
    }
    
    /**
     * getNewsletterLink returns link for newslettering article
     *
     * @return string
     */
    private function getNewsletterLink()
    {
        $newsletterLink = (!$this->exclude[self::EXCLUDE_NEWSLETTER])
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_NEWSLETTER_TITLE,
                self::IMG_NEWSLETTER_FILE,
                self::IMG_NEWSLETTER_CLASS,
                self::NEWSLETTER_CONTROLLER . self::NEWSLETTER_ACTION . $this->id
            )
            : '';
        return $newsletterLink;
    }
        
    /**
     * getMailtaskLink returns link for mail task
     *
     * @return string
     */
    private function getMailtaskLink()
    {
        $mailLink = (!$this->exclude[self::EXCLUDE_MAILTASK])
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_MAILTASK_TITLE,
                self::IMG_MAILTASK_FILE,
                self::IMG_MAILTASK_CLASS,
                self::MAILTASK_CONTROLLER . self::MAILTASK_ACTION . $this->id
            )
            : '';
        return $mailLink;
    }
    
    /**
     * getValidateLink returns link for validation
     *
     * @return string
     */
    private function getValidateLink()
    {
        $validateLink = (!$this->exclude[self::EXCLUDE_VALIDATE])
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_VALIDATE_TITLE,
                self::IMG_VALIDATE_FILE,
                self::IMG_VALIDATE_CLASS,
                $this->controlerLink . self::VALIDATE_ACTION . $this->id
            )
            : '';
        return $validateLink;
    }
    
    /**
     * getRefuseLink returns link for refuse
     *
     * @return string
     */
    private function getRefuseLink()
    {
        $validateLink = (!$this->exclude[self::EXCLUDE_REFUSE])
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_REFUSE_TITLE,
                self::IMG_REFUSE_FILE,
                self::IMG_REFUSE_CLASS,
                $this->controlerLink . self::REFUSE_ACTION . $this->id
            )
            : '';
        return $validateLink;
    }
    
    /**
    * getImportLink returns link for bbcode import
    *
    * @return string
    */
    private function getImportLink()
    {
        $editLink = (!$this->exclude[self::EXCLUDE_IMPORT])
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_IMPORT_TITLE,
                self::IMG_IMPORT_FILE,
                self::IMG_IMPORT_CLASS,
                $this->controlerLink . self::IMPORT_ACTION . $this->id
            )
            : '';
        return $editLink;
    }
    
    /**
     * getOrderLink returns link for order
     *
     * @return string
     */
    private function getOrderLink()
    {
        $orderLink = (!$this->exclude[self::EXCLUDE_ORDER])
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_ORDER_TITLE,
                self::IMG_ORDER_FILE,
                self::IMG_ORDER_CLASS,
                self::ORDER_CONTROLLER . self::ORDER_ACTION . $this->id
            )
            : '';
        return $orderLink;
    }
    
    /**
     * process toolbar process
     */
    protected function process()
    {
        $this->content = $this->getPeopleLink()
            . $this->getPdfLink()
            . $this->getDetailLink()
            . $this->getOrderLink()
            . $this->getValidateLink()
            . $this->getRefuseLink()
            . $this->getCloneLink()
            . $this->getEditLink()
            . $this->getDeleteLink()
            . $this->getSubtitleLink()
            . $this->getImportLink()
            . $this->getNewsletterLink()
            . $this->getMailtaskLink();
    }

    /**
     * @see __toString
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->content;
    }
    
    /**
     * @see __destruct
     *
     */
    public function __destruct()
    {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }
}
