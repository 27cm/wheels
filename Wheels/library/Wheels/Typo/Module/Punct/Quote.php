<?php

namespace Wheels\Typo\Module\Punct;

use Wheels\Typo;
use Wheels\Typo\Module;
use Wheels\Typo\Exception;

/**
 * Кавычки.
 *
 * Расставляет кавычки в тексте.
 *
 * @link http://en.wikipedia.org/wiki/Quotation_mark
 */
class Quote extends Module
{
    /**
     * @see \Wheels\Typo\Module::$default_options
     */
    protected $default_options = array(
        /**
         * Открывающая кавычка.
         *
         * @example 'ldquo' (американский английский)
         * @example 'lsquo' (британский английский)
         * @example 'laquo' (русский)
         *
         * @var string
         */
        'quote-open' => 'laquo',

        /**
         * Закрывающая кавычка.
         *
         * @example 'rdquo' (американский английский)
         * @example 'rsquo' (британский английский)
         * @example 'raquo' (русский)
         *
         * @var string
         */
        'quote-close' => 'raquo',

        /**
         * Внутренняя открывающая кавычка.
         *
         * @example 'lsquo' (американский английский)
         * @example 'ldquo' (британский английский)
         * @example 'bdquo' (русский)
         *
         * @var string
         */
        'subquote-open' => 'bdquo',

        /**
         * Внутренняя закрывающая кавычка.
         *
         * @example 'rsquo' (американский английский)
         * @example 'rdquo' (британский английский)
         * @example 'ldquo' (русский)
         *
         * @var string
         */
        'subquote-close' => 'ldquo',
    );

    /**
     * @see \Wheels\Typo\Module::$order
     */
    static public $order = array(
        'A' => 0,
        'B' => 30,
        'C' => 0,
        'D' => 0,
        'E' => 0,
        'F' => 0,
    );


    // --- Открытые методы класса ---

    /**
     * @see \Typo\Module::validateOption()
     */
    public function validateOption($name, &$value)
    {
        switch($name)
        {
            case 'quote-open' :
            case 'quote-close' :
            case 'subquote-open' :
            case 'subquote-close' :
                if(!array_key_exists($value, Typo::$chars['chr']))
                    return self::throwException(Exception::E_OPTION_VALUE, "Неизвестный символ '&{$value};' (параметр '$name')");
            break;

            default : Module::validateOption($name, $value);
        }
    }


    // --- Защищенные методы класса ---

    /**
     * Стадия B.
     *
     * Применяет правила для расстановки кавычек в тексте.
     *
     * @return void
     */
    protected function stageB()
    {
        $c =& Typo::$chars['chr'];

        $q1 = array(
            'open'  => $c[$this->options['quote-open']],
            'close' => $c[$this->options['quote-close']],
        );
        $q2 = array(
            'open'  => $c[$this->options['subquote-open']],
            'close' => $c[$this->options['subquote-close']],
        );

        $rules = array(
            // Открывающая кавычка
            '~((?:^|\(|\h){t}*)(\"+)(?={t}*\S)~iu' => function ($m) use($q1) {
                return $m[1] . str_repeat($q1['open'], mb_strlen($m[2]));
            },

            // Закрывающая кавычка
			'~((?:{a}|{b}|[?!:.)]|' . $c['hellip'] . '){t}*)(\"+)(?={t}*(?:\h|[?!:;,.)]|' . $c['hellip'] . '|$))~u' => function ($m) use($q1) {
                return $m[1] . str_repeat($q1['close'], mb_strlen($m[2]));
            },

            // Закрывающая кавычка особые случаи
            '~([a-zа-яё0-9]|\.|' . $c['hellip'] . '|\!|\?|\>|\)|\:)((\"|\\\"|\&laquo\;)+)(\<[^\>]+\>)(\.|\&hellip\;|\;|\:|\?|\!|\,|\)|\<\/|$| )~iu' => function($m) use($q1) {
                return $m[1] . str_repeat($q1['close'], mb_substr_count($m[2],"\"") + mb_substr_count($m[2],"&laquo;")) . $m[4]. $m[5];
            },
            '~([a-zа-яё0-9]|\.|' . $c['hellip'] . '|\!|\?|\>|\)|\:)(\s+)((\"|\\\")+)(\s+)(\.|\&hellip\;|\;|\:|\?|\!|\,|\)|\<\/|$| )~iu' => function($m) use($q1) {
                return $m[1] .$m[2]. str_repeat($q1['close'], mb_substr_count($m[3],"\"") + mb_substr_count($m[3],"&laquo;")) . $m[5]. $m[6];
            },
        );

        $this->applyRules($rules);

        $level = 0;
		$offset = 0;
        $stack = array();

		while(true)
		{
            $p = $this->text->strpos($q1, $offset);

			if($p === false)
                break;

            list($pos, $str) = array_values($p);
            $offset = $pos + mb_strlen($str);

			if($str == $q1['open'])
			{
				if($level % 2)
                    $stack[] = array($q2['open'], $pos, mb_strlen($str));
				$level++;
			}
			else
			{
				$level--;
				if($level % 2)
                    $stack[] = array($q2['close'], $pos, mb_strlen($str));
			}

			if($level == 0)
			{
                $delta = 0;
                foreach($stack as $data)
                {
                    $this->text->substr_replace($data[0], $data[1] + $delta, $data[2]);
                    $delta += mb_strlen($data[0]) - $data[2];
                }
                $offset += $delta;
			}
		}
    }
}