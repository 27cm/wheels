<?php
require_once 'XMLTestIterator.php';
use Wheels\Typo;

/**
 * Абстрактный класс для теста модулей.
 *
 * Конкретные классы тестов находятся в директории Module, во всех них тестируются загруженные XML-файлы из папки resources через метод testXMLFiles
 */
abstract class ModuleTest extends PHPUnit_Framework_TestCase
{
    protected $typo;

    protected function setUp()
    {
        $this->typo = new Typo();
        $this->typo->setConfigDir(TEST_DIR . DS . 'config' . DS . get_called_class());
    }

    public function XMLProvider()
    {
        $filename = preg_replace('~Test$~', '', get_called_class()) . '.xml';
        return new XMLTestIterator('resources' . DS . $filename);
    }

    /**
     * @dataProvider XMLProvider
     */
    public function testXMLFiles($input, $expected, $desc, $section)
    {
        $this->typo->setOptions($section);
        $output = (string) $this->typo->process($input);
        $this->assertEquals($expected, $output, $desc);
    }

}