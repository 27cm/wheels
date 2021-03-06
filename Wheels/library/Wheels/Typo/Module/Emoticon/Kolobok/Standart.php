<?php

namespace Wheels\Typo\Module\Smile\Kolobok;

use Wheels\Typo\Module\Smile\Kolobok;

/**
 * Стандартные "колобки".
 *
 * @link http://www.kolobok.us/content_plugins/gallery/gallery.php?smiles.2.1
 */
class Standart extends Kolobok
{
    /**
     * URL изображений.
     *
     * @var string
     */
    static public $url = 'http://www.kolobok.us/smiles/standart/%s.gif';

    /**
     * Смайлики.
     *
     * @todo Возможность переопределять
     * @var array
     */
    static public $smiles
        = array(
            // Улыбка, радость
            'smile' => array(':)', ':-)', '=)', '+)'),

            // Грусть, печаль
            'sad'   => array(':(', ':-(', '=(', '+)'),

            // Смех
            'rofl'  => array(':D', ':-D', '=D', '+D'),


            // Персонажи:

            // Ангел
            'angel' => array('O:-)', 'O:)', 'O+)', 'O=)', '0:-)', '0:)', '0+)', '0=)'),

            'smile' => 'smile3',

        );
}