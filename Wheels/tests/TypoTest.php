<?php

use Wheels\Typo\Exception;
use Wheels\Typo;


class TypoTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Типограф.
     * 
     * @var \Typo
     */
    protected $typo;

    protected function setUp()
    {
        $this->typo = new Typo();
    }

    /**
     * Проверяем значения свойств по умолчанию.
     */
    public function testDefaultOptions()
    {
        $this->assertEquals($this->typo->getOption('charset'), 'utf-8',  '', 0, 10, false, true);
        $this->assertEquals($this->typo->getOption('encoding'), Typo::MODE_NONE);
        $this->assertEquals($this->typo->getOption('html-in-enabled'), true);
        $this->assertEquals($this->typo->getOption('html-out-enabled'), true);

        // todo раскомментить, когда будут эти опции
        //$this->assertEquals($this->typo->getOption('html-doctype'), Typo::DOCTYPE_HTML5);
        //$this->assertEquals($this->typo->getOption('nl2br'), true);
        $this->assertEquals($this->typo->getOption('e-convert'), false);
    }

    /**
     * Установка неизвестного параметра.
     */
    public function testSetUnknownOption()
    {
        $this->setExpectedException('Exception', 'Несуществующий параметр');
        $this->typo->setOption('unknown', 'value');
    }
    
    /**
     * Получение неизвестного параметра.
     */
    public function testGetUnknownOption() 
    {
        $this->setExpectedException('Exception', 'Несуществующий параметр');
        $this->typo->getOption('unknown');
    }
    
    
    /**
     * Установка неправильной кодировки.
     */
    public function testSetUnknownCharset() 
    {
        $this->setExpectedException('Exception', 'Неизвестная кодировка', Exception::E_OPTION_VALUE);
        $this->typo->setOption('charset', 'unknown');
    }

    /**
     * Установка существуеющей кодировки.
     * 
     * @dataProvider charsets
     */
    public function testSetExistingCharset($charset) 
    {
        $this->typo->setOption('charset', $charset);
        $this->assertEquals($this->typo->getOption('charset'), $charset, '', 0, 10, false, true);
    }

    /**
     * Установка неправильного параметра кодировки.
     */
    public function testSetUnknownEncoding() 
    {
        $this->setExpectedException('Exception', 'Неизвестный режим кодирования спецсимволов', Exception::E_OPTION_VALUE);
        $this->typo->setOption('encoding', 'MODE_UNKNOWN');
    }

    /**
     * Установка неправильного значения параметра modules.
     * Пытаемся установить нестроковое значение.
     */
    public function testSetNotStringModules()
    {
        $this->setExpectedException('Exception', "Значение параметра 'modules' должно быть строкой или массивом строк", Exception::E_OPTION_VALUE);
        $this->typo->setOption('modules',  true);
    }
    /**
     * Установка неправильного значения параметра modules.
     * Пытаемся установить массив нестроковых значений.
     */
    public function testSetNotArrayModules()
    {
        $this->setExpectedException('Exception', "Значение параметра 'modules' должно быть строкой или массивом строк", Exception::E_OPTION_VALUE);
        $this->typo->setOption('modules', array('url', array()));;
    }

    /**
     * Установка  параметра modules.
     */
    public function testSetModules()
    {
        $this->typo->setOption('modules',  'html');
        $this->typo->setOption('modules', array('html', 'url'));
        $this->typo->setOption('modules', array('html','url','punct/quote'));
    }

    /**
     * Проверка восстановления опций по умолчанию.
     */
    public function testSetDefaultOptions()
    {
        $this->typo->setOption('modules', array('punct/quote'));
        $before = $this->typo->getOption('modules');

        $this->typo->setDefaultOptions();

        $after = $this->typo->getOption('modules');

        $this->assertNotEquals($before,$after);
    }
    /**
     * Добавление/удаление модуля
     */
    public function testAddRemoveModule()
    {
        // todo спросить добавляется ли модуль в массив опций после addModule (то есть как потом получить модуль)
//        $this->typo->addModule('punct/quote');
//        $module = $this->typo->getOption('modules')['punct/quote'];
    }


    /**
     * Установка существующего параметра кодировки.
     * 
     * @dataProvider encodings
     */
    public function testSetExistingEncodings($encoding) 
    {
        $this->typo->setOption('encoding', $encoding);
        $this->assertEquals($this->typo->getOption('encoding'), $encoding, '', 0, 10, false, true);
    }
    
    /**
     * Кодировки.
     */
    public function charsets() 
    {
        return array(
            array('UTF-8'),
            array('CP1251'),
            array('KOI8-R'),
            array('IBM866'),
            array('ISO-8859-5'),
            array('MAC')
        );
    }
    
    /**
     * Режими кодирования спецсимволов.
     */
    public function encodings() 
    {
        return array(
            array(Typo::MODE_NONE),
            array(Typo::MODE_NAMES),
            array(Typo::MODE_CODES),
            array(Typo::MODE_HEX_CODES)
        );
    }
}