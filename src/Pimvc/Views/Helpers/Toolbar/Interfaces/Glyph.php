<?php

/**
 * Description of Pimvc\Views\Helpers\Toolbar\Interfaces\Glyph
 *
 * @author pierrefromager
 */

namespace Pimvc\Views\Helpers\Toolbar\Interfaces;

interface Glyph {

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
    const CLONE_ACTION = '/duplicate/id/';
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
    
    public function __construct($params, $controler, $exclude = [], $actionPrefix = '', $actionSuffix = '');
    public function __toString();

}
