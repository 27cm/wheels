<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Config
 * @subpackage Wheels\Config\Schema\Option\Type
 */

namespace Wheels\Config\Schema\Option\Type;

use Wheels\Config\Schema\Option\Type;

/**
 * Массив элементов заданного типа.
 */
class Tarray extends Type
{
    /**
     * Тип элементов массива.
     *
     * @var \Wheels\Config\Schema\Option\Type
     */
    protected $_type;


    // --- Конструктор ---

    /**
     * Создание типа - массива элементов заданного типа.
     *
     * @param mixed $type Тип элементов массива. Если не задан, то используется тип {@link \Wheels\Config\Schema\Option\Type\Tmixed}.
     *
     * @uses \Wheels\Config\Schema\Option\Type::create()
     */
    public function __construct($type = NULL)
    {
        if($type instanceof Type)
            $this->_type = $type;
        elseif(is_null($type))
            $this->_type = static::create('mixed');
        else
            $this->_type = static::create($type);
    }


    // --- Открытые методы ---

    /**
     * {@inheritDoc}
     */
    public function validate($var)
    {
        if(is_array($var))
        {
            foreach($var as $value)
            {
                if(!$this->_type->validate($value))
                    return FALSE;
            }
            return TRUE;
        }

        return FALSE;
    }
}
