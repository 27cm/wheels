<?php

namespace Typo\Module;

use Typo\Module;

/**
 * Пробелы.
 *
 * Расставляет и удаляет простые и неразрывные пробелы в тексте.
 */
class Space extends Module
{
    /**
     * Настройки по умолчанию.
     *
     * @var array
     */
    protected $default_options = array();

    /**
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static public $order = array(
        'A' => 0,
        'B' => 25,
        'C' => 0,
        'D' => 0,
        'E' => 0,
        'F' => 0,
    );


    // --- Защищенные методы класса ---

    /**
     * Стадия B.
     *
     * Применяет правила для расстановки пробелов в тексте.
     *
     * @link http://habrahabr.ru/post/23250/
     *
     * @return void
     */
    protected function stageB()
    {
        $s =& $this->typo->chr;

        $rules = array(
            // Оторвать скобку от слова
            // @todo: "text  (" => "text (". "text.  (" => "text. ("
            '~((?:\)|{a}|\]\]\]){t}*)\(~u' => '$1 (',
            '~\)({t}*(?:\(|{a}|{b}))~u' => ') $1',

            // Лишние пробелы после открывающей скобочки и перед закрывающей
            '~(?<=\()({t}*)\h+~' => '$1',
            '~\h+(?={t}*\))~' => '',

            // Неразрывный пробел перед сокращениями px, dpi и lpi
            '~(?<=\d)\h(?=(px|dpi|lpi)\b)~i' => $s['nbsp'],

            // Неразрывный пробел после сокращений гл., стр., рис., илл., ил., ст., с., п.
            '~(?<=\b)((?:гл|стр|рис|табл|илл?|ст?|п)\.)\h(?=\d)~iu' => '$1' . $s['nbsp'],

            // Неразрывный пробел после сокращений см., им.
            '~(?<=\b)((?:см|им)\.)\h~iu' => '$1' . $s['nbsp'],

            // Неразрывный пробел после сокращений г., ул., пер., ...
            '~(?<=\b)((?:г|ул|пер|просп|пл|бул|наб|пр|ш|туп|им)\.)\h~iu' => '$1' . $s['nbsp'],

            // Неразрывный пробел после или перед б-р, пр-кт
			'~(?<=\b)(б\-р|пр\-кт)\h~iu' => '$1' . $s['nbsp'],
			'~(?<=\b)\h(б\-р|пр\-кт)\b(?!\h)~iu' => $s['nbsp'] . '$1',

            // Неразрывный пробел после веков
            '~(?<=\b)([XIV]{1,5})\h(?=(вв?|век)\b)~' => '$1' . $s['nbsp'],

            // Неразрывный пробел после сокращений д., кв., эт.
			'~(?<=\b)((?:д|кв|эт)\.)\h(?=\d)~iu' => '$1' . $s['nbsp'],

            // Неразрывный пробел перед открывающей скобкой
            '~\h(?=\()~' => $s['nbsp'],

            // Неразрывный пробел в датах между числом и месяцем
            '~(?<=\d)\h*(?=января|февраля|марта|апреля|мая|июня|июля|августа|сентября|октября|ноября|декабря)~iu' => $s['nbsp'],

            // Удаление пробелов в конце текста
            '~\h+$~' => '',

            // Пробел после года
			'~(?<=\b)(\d{3,4})\h*(год([ауе]|ом)?)~iu' => '$1 $2',

            // Удаление пробела перед символом процента
            '~(?<=\d)\h+(?=%)~' => '',

            // Неразрывный пробел перед единицами измерения, частицами
            '~\h(?=({m}|гр?|кг|ц|т|[кмгтпэзи]?б(ит)?|же?|бы?|л[иь]|либо)\b)~iu' => $s['nbsp'],

            // Неразрывные пробелы между инициалами и фамилией
            '~([А-ЯЁ]\.)\h([А-ЯЁ]\.)\h(?=[А-ЯЁ][а-яё])~u' => '$1' . $s['nbsp'] . '$2' . $s['nbsp'],
            '~([А-ЯЁ]\.)\h(?=[А-ЯЁ][а-яё])~u' => '$1' . $s['nbsp'],
            '~([А-ЯЁ][а-яё]+)\h([А-ЯЁ]\.)\h(?=[А-ЯЁ]\.)~u' => '$1' . $s['nbsp'] . '$2' . $s['nbsp'],
            '~([А-ЯЁ][а-яё]+)\h(?=[А-ЯЁ]\.)~u' => '$1' . $s['nbsp'],

            // Неразрывный пробел в денежных суммах
            '~(?<=\d)\h((?:млн|тыс|млрд)\.?)\h(р(уб)?)(?=\b)~iu' => $s['nbsp'] . '$1' . $s['nbsp'] . '$2',
            '~(?<=\d)\h(?=(млн|тыс|млрд)\b)~iu' => $s['nbsp'],
            '~(?<=\d)\h(?=(р(уб)?|коп)\b)~iu' => $s['nbsp'],

            // Неразрывный пробел в номере версии программы
            '~(?<=\b([vв]\.))\h(?=\d)~iu' => $s['nbsp'],

            // Неразрывный пробел перед последним словом в предложении
            '~(\h{t}*[^\h]+{t}*)\h(?={t}*{a}{1,8}{t}*(\n|$|[?!.]))~u' => '$1' . $s['nbsp'],

            // Неразрывный пробел после предлогов, союзов и коротких слов
            '~(?<=\b)([a-zа-яё]{2}|а|в|и|к|о|с|у|я|вне|обо|ото|изо|под|подо|пред|предо|про|над|надо|как|без|безо|что|для|там|ещё|или|меж|между|перед|передо|около|через|сквозь|для|при)\h(?={a}{3,8})~iu' => '$1' . $s['nbsp'],

            // Полупробел между сволом номера или параграфа и числом
            '~(№|$)\h?(?=\d)~' => '$1' . $s['thinsp'],

            // Неразрывные пробел в числах
            '~(?<=\d)\h(?=\d)~' => $s['nbsp'],

            // Неразрывный пробелы между числом и Флопс
            '~(?<=\d)\h([кмгтпэзи]?)(флопс)~iu' => $s['nbsp'] . '$1Флопс',

            // Неразрывные пробелы в сокращениях "и т. д.", "и т. п.", "т. к.", "т. е.", "и др.", "до н. э.", "ю. ш.", ...
            '~(?<=\bи)\h(т\.)\h?([дп]\.)~iu' => $s['nbsp'] . '$1' . $s['nbsp'] . '$2',
            '~(?<=\b)(т\.)\h?([ке]\.)~iu' => '$1' . $s['nbsp'] . '$2',
            '~(?<=\bи)\h(?=др\.)~iu' => $s['nbsp'],
            '~(?<=\bдо)\h(н\.)\h?(э\.)~iu' => $s['nbsp'] . '$1' . $s['nbsp'] . '$2',
            '~\h([юc]\.)\h?(ш\.)~' => $s['nbsp'] . '$1' . $s['nbsp'] . '$2',
            '~\h([зв]\.)\h?(д\.)~' => $s['nbsp'] . '$1' . $s['nbsp'] . '$2',
        );

        $this->applyRules($rules);
    }
}