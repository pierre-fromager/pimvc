<?php

namespace Pimvc\Views\Helpers\Toolbar;

class Glyph {

    const IMG_EDIT_TITLE = 'Editer';
    const IMG_EDIT_FILE = 'pencil';
    const IMG_EDIT_CLASS = 'btn-warning';
    const EDIT_ACTION = 'edit';
    
    const IMG_IMPORT_TITLE = 'Importer BBCode';
    const IMG_IMPORT_FILE = 'import-icon.png';
    const IMG_IMPORT_CLASS = '';
    const IMPORT_ACTION = '/edit/process/bbcode/id/';
    
    const IMG_DETAIL_TITLE = 'Détails';
    const IMG_DETAIL_FILE = 'eye-open';
    const IMG_DETAIL_CLASS = 'btn-success';
    const DETAIL_ACTION = '/detail/id/';
    
    const IMG_PEOPLE_TITLE = 'Personne';
    const IMG_PEOPLE_FILE = 'user';
    const IMG_PEOPLE_CLASS = '';
    const PEOPLE_ACTION = '/people/id/';
    
    const IMG_SUBTITLE_TITLE = 'Sous-titrage';
    const IMG_SUBTITLE_FILE = 'subtitles';
    const IMG_SUBTITLE_CLASS = 'btn-success';
    const SUBTITLE_ACTION = '/subtitle/tid/';
        
    const IMG_DEL_TITLE = 'Supprimer';
    const IMG_DEL_FILE = 'trash';
    const IMG_DEL_CLASS = 'btn-danger';
    const DELETE_ACTION = '/delete/id/';
    
    const IMG_VALIDATE_TITLE = 'Valider';
    const IMG_VALIDATE_FILE = 'ok';
    const IMG_VALIDATE_CLASS = 'btn-info';
    const VALIDATE_ACTION = '/validate/id/';
      
    const IMG_PDF_TITLE = 'Imprimer Pdf';
    const IMG_PDF_FILE = 'print';
    const IMG_PDF_CLASS = 'btn-default';
    //const PDF_CONTROLLER = 'article';
    const PDF_ACTION = '/print/id/';
    
    const IMG_CLONE_TITLE = 'Dupliquer';
    const IMG_CLONE_FILE = 'retweet';
    const IMG_CLONE_CLASS = 'btn-info';
    const CLONE_ACTION = '/clone/id/';
       
    const IMG_NEWSLETTER_TITLE = 'Newsletter';
    const IMG_NEWSLETTER_FILE = 'envelope';
    const IMG_NEWSLETTER_CLASS = '';
    const NEWSLETTER_ACTION = '/select/id/';
    const NEWSLETTER_CONTROLLER = 'newsletter';
    const IMG_MAILTASK_TITLE = 'Courrier tâche';
    const IMG_MAILTASK_FILE = 'envelope';
    const IMG_MAILTASK_CLASS = '';
    const MAILTASK_ACTION = '/task/id/';
    const MAILTASK_CONTROLLER = 'mail';
    
    const IMG_SHIPPING_TITLE = 'Livraison';
    const IMG_SHIPPING_FILE = 'shipping.png';
    const IMG_SHIPPING_CLASS = '';
    const SHIPPING_ACTION = '/detail/id/';
    const SHIPPING_CONTROLLER = 'bonlivraisons';
    
    const IMG_LWORKORDER_TITLE = 'Ligne Bon Travails';
    const IMG_LWORKORDER_FILE = 'lworkorder.png';
    const IMG_LWORKORDER_CLASS = '';
    const LWORKORDER_ACTION = '/list/id/';
    const LWORKORDER_CONTROLLER = 'lignedemandetravails';
    
    const IMG_ORDER_TITLE = 'Commande';
    const IMG_ORDER_FILE = 'cart.png';
    const IMG_ORDER_CLASS = '';
    const ORDER_ACTION = '/edit/id/';
    const ORDER_CONTROLLER = 'commande';
    
    const IMG_PATH = 'public/images/toolbar/';
    const IMG_SEP = '&nbsp;';
    
    const EXCLUDE_CLONE = 'xclone';
    const EXCLUDE_IMPORT = 'ximport';
    const EXCLUDE_NEWSLETTER = 'xnewsletter';
    const EXCLUDE_MAILTASK = 'xmailtask';
    const EXCLUDE_EDIT = 'xedit';
    const EXCLUDE_DETAIL = 'xdetail';
    const EXCLUDE_DELETE = 'xdelete';
    const EXCLUDE_VALIDATE = 'xvalidate';
    const EXCLUDE_PDF = 'xpdf';
    const EXCLUDE_SHIPPING = 'xshipping';
    const EXCLUDE_LWORKORDER = 'xlworkorder';
    const EXCLUDE_PEOPLE = 'xpeople';
    const EXCLUDE_ORDER = 'xorder';
    const EXCLUDE_SUBTTITLE = 'xsubtitle';
    
    const CONFIRM_MESSAGE_EDIT = 'Voulez vous editer ?';
    const CONFIRM_MESSAGE_PRINT = 'Voulez vous imprimer ?';
    const CONFIRM_MESSAGE_DELETE = 'Voulez vous supprimer ?';
    const CONFIRM_MESSAGE_CLONE = 'Voulez vous dupliquer ?';

    protected $content = '';
    protected $params = array();
    protected $exclude = array();
    protected $baseUrl = '';
    protected $editLink = '';
    protected $deleteLink = '';
    protected $controlerLink = '';
    protected $id = '';
    protected $numero_bdl = '';
    protected $numero_ligne = '';
    protected $actionPrefix;
    protected $actionSuffix;

    /**
     * @see __contruct
     * 
     * @param array $params
     * @param string $controler 
     */
    public function __construct($params, $controler, $exclude = array(), $actionPrefix = '', $actionSuffix = '') {
        $defaultExclude = array(
            self::EXCLUDE_EDIT => false
            , self::EXCLUDE_DETAIL => false
            , self::EXCLUDE_DELETE => false
            , self::EXCLUDE_VALIDATE => false
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
        );
        $this->exclude = (!empty($exclude)) 
            ? array_merge($defaultExclude, $exclude) 
            : $defaultExclude;

        $this->id = (isset($params['id'])) 
                ? $params['id'] 
                : '';
        $this->numero_bdl = (isset($params['numero_bdl'])) 
                ? $params['numero_bdl'] 
                : '';
        $this->numero_ligne = (isset($params['numero_ligne'])) 
                ? $params['numero_ligne'] 
                : '';
        
        $this->baseUrl = $this->baseUrl = \Pimvc\App::getInstance()->getRequest()->getBaseUrl();
        $controlSplit = (strpos($controler, '/') !== false) 
            ? explode('/', $controler) 
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
     * getGlyphLink
     * 
     * @param type $imgTitle
     * @param type $imgFilename
     * @param type $imgClass
     * @param type $imgLink
     * @param type $confirmMessage
     * @return type 
     */
    private function getGlyphLink($imgTitle, $imgFilename, $imgClass, $imgLink, $confirmMessage = '', $target = '_self') {
        $iconOptions = array(
            'class' => 'glyphicon glyphicon-' . $imgFilename
        );
        $icon = new \Pimvc\Html\Element\Decorator('i', ' ', $iconOptions);
        $iconWrapperOptions = array(
            'class' => 'btn btn-default btn-xs ' . $imgClass
            , 'alt' => $imgTitle
            , 'title' => $imgTitle
        );
        $iconWrapper = new \Pimvc\Html\Element\Decorator(
            'span'
            , (string) $icon
            , $iconWrapperOptions
        );
        //var_dump($imgLink);die;
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
        $link = new \Pimvc\Html\Element\Decorator(
            'a'
            , (string) $iconWrapper
            , $linkOptions
        );
        return (string) $link;
    }
 
    /**
     * getEditLink returns link for editing
     * 
     * @return string 
     */
    private function getEditLink() {
        $editLink = (!$this->exclude[self::EXCLUDE_EDIT]) 
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_EDIT_TITLE
                , self::IMG_EDIT_FILE
                , self::IMG_EDIT_CLASS
                , $this->getActionLink(self::EDIT_ACTION)
                , $confirm = self::CONFIRM_MESSAGE_EDIT
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
    private function getActionLink($action) {
        return $this->controlerLink . DIRECTORY_SEPARATOR
            . $this->actionPrefix . $action . $this->actionSuffix
            . DIRECTORY_SEPARATOR . 'id' . DIRECTORY_SEPARATOR . $this->id;
    }

    /**
     * getPdfLink returns link for printing
     * 
     * @return string 
     */
    private function getPdfLink() {
        $editLink = (!$this->exclude[self::EXCLUDE_PDF]) 
            ? self::IMG_SEP . $this->getGlyphLink(
                    self::IMG_PDF_TITLE
                    , self::IMG_PDF_FILE
                    , self::IMG_PDF_CLASS
                    , $this->controlerLink . self::PDF_ACTION . $this->id
                    , $confirmMessage = self::CONFIRM_MESSAGE_PRINT
                    , $target = '_blank'
                ) 
            : '';
        return $editLink;
    }
    
    /**
     * getDetailLink returns link for editing
     * 
     * @return string 
     */
    private function getDetailLink() {
        $detailLink = (!$this->exclude[self::EXCLUDE_DETAIL]) 
            ? self::IMG_SEP . $this->getGlyphLink(
                    self::IMG_DETAIL_TITLE
                    , self::IMG_DETAIL_FILE
                    , self::IMG_DETAIL_CLASS
                    , $this->getActionLink('detail')
                ) 
            : '';
        return $detailLink;
    }
    
    /**
     * getSubtitleLink returns link for subtitling
     * 
     * @return string 
     */
    private function getSubtitleLink() {
        $subtitleLink = (!$this->exclude[self::EXCLUDE_SUBTTITLE]) 
            ? self::IMG_SEP . $this->getGlyphLink(
                    self::IMG_SUBTITLE_TITLE
                    , self::IMG_SUBTITLE_FILE
                    , self::IMG_SUBTITLE_CLASS
                    , $this->controlerLink . self::SUBTITLE_ACTION . $this->id
                ) 
            : '';
        return $subtitleLink;
    }
    
    /**
     * getCloneLink returns link for cloning object
     * 
     * @return string 
     */
    private function getCloneLink() {
        $cloneLink = (!$this->exclude[self::EXCLUDE_CLONE]) 
            ? self::IMG_SEP . $this->getGlyphLink(
                    self::IMG_CLONE_TITLE
                    , self::IMG_CLONE_FILE
                    , self::IMG_CLONE_CLASS
                    , $this->getActionLink('clone')
                    , self::CONFIRM_MESSAGE_CLONE
                ) 
            : '';
        return $cloneLink;
    }
    
    /**
     * getPeopleLink returns link for people file
     * 
     * @return string 
     */
    private function getPeopleLink() {
        $detailLink = (!$this->exclude[self::EXCLUDE_PEOPLE]) 
            ? self::IMG_SEP . $this->getGlyphLink(
                    self::IMG_PEOPLE_TITLE
                    , self::IMG_PEOPLE_FILE
                    , self::IMG_PEOPLE_CLASS
                    , $this->getActionLink('people')
                ) 
            : '';
        return $detailLink;
    }
    
    /**
     * getDeleteLink returns link for deleting article
     * 
     * @return string 
     */
    private function getDeleteLink() {
         $deleteLink = (!$this->exclude[self::EXCLUDE_DELETE]) 
            ? self::IMG_SEP . $this->getGlyphLink(
                    self::IMG_DEL_TITLE
                    , self::IMG_DEL_FILE
                    , self::IMG_DEL_CLASS
                    , $this->getActionLink('delete')
                    , $confirm = self::CONFIRM_MESSAGE_DELETE
                ) 
            : '';
       return $deleteLink;
    }
    
    /**
     * getNewsletterLink returns link for newslettering article
     * 
     * @return string 
     */
    private function getNewsletterLink() {
        $newsletterLink = (!$this->exclude[self::EXCLUDE_NEWSLETTER]) 
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_NEWSLETTER_TITLE
                , self::IMG_NEWSLETTER_FILE
                , self::IMG_NEWSLETTER_CLASS
                , self::NEWSLETTER_CONTROLLER . self::NEWSLETTER_ACTION . $this->id
                ) 
            : '';
        return $newsletterLink;
    }
        
    /**
     * getMailtaskLink returns link for mail task
     * 
     * @return string 
     */
    private function getMailtaskLink() {
        $mailLink = (!$this->exclude[self::EXCLUDE_MAILTASK]) 
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_MAILTASK_TITLE
                , self::IMG_MAILTASK_FILE
                , self::IMG_MAILTASK_CLASS
                , self::MAILTASK_CONTROLLER . self::MAILTASK_ACTION . $this->id
                ) 
            : '';
        return $mailLink;
    }
    
    /**
     * getNewsletterLink returns link for newslettering article
     * 
     * @return string 
     */
    private function getValidateLink() {
        $validateLink = (!$this->exclude[self::EXCLUDE_VALIDATE]) 
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_VALIDATE_TITLE
                , self::IMG_VALIDATE_FILE
                , self::IMG_VALIDATE_CLASS
                , $this->controlerLink . self::VALIDATE_ACTION . $this->id
            ) 
            : '';
        return $validateLink;
    }
    
     /**
     * getImportLink returns link for bbcode import
     * 
     * @return string 
     */
    private function getImportLink() {
        $editLink = (!$this->exclude[self::EXCLUDE_IMPORT]) 
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_IMPORT_TITLE
                , self::IMG_IMPORT_FILE
                , self::IMG_IMPORT_CLASS
                , $this->controlerLink . self::IMPORT_ACTION . $this->id
                ) 
            : '';
        return $editLink;
    }
    
    /**
     * getLworkorderLink returns link for line work order 
     * 
     * @return string 
     */
    private function getLworkorderLink() {
        $validateLink = (!$this->exclude[self::EXCLUDE_LWORKORDER]) 
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_LWORKORDER_TITLE
                , self::IMG_LWORKORDER_FILE
                , self::IMG_LWORKORDER_CLASS
                , self::LWORKORDER_CONTROLLER . self::LWORKORDER_ACTION . $this->id 
                    . '/numero_ligne/' . $this->numero_ligne
                ) : '';
        return $validateLink;
    }
    
    /**
     * getOrderLink returns link for order 
     * 
     * @return string 
     */
    private function getOrderLink() {
        $orderLink = (!$this->exclude[self::EXCLUDE_ORDER]) 
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_ORDER_TITLE
                , self::IMG_ORDER_FILE
                , self::IMG_ORDER_CLASS
                , self::ORDER_CONTROLLER . self::ORDER_ACTION . $this->id
                ) 
            : '';
        return $orderLink;
    }

    /**
     * getShppingLink returns link for shipping
     * 
     * @return string 
     */
    private function getShppingLink() {
        $shippingLink = (!$this->exclude[self::EXCLUDE_SHIPPING]) 
            ? self::IMG_SEP . $this->getGlyphLink(
                self::IMG_SHIPPING_TITLE
                , self::IMG_SHIPPING_FILE
                , self::SHIPPING_CONTROLLER . self::SHIPPING_ACTION . $this->id 
                    . '/numero_bdl/' . $this->numero_bdl
                ) 
            : '';
        return $shippingLink;
    }
    
    /**
     * process toolbar process
     */
    protected function process() {
        $this->content = $this->getPeopleLink()
            . $this->getPdfLink()
            . $this->getDetailLink()
            . $this->getOrderLink()
            . $this->getValidateLink()
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
    public function __toString() {
        return (string) $this->content;
    }

}
