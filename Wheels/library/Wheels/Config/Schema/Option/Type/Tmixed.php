<?php

namespace Wheels\Config\Schema\Option\Type;

use Wheels\Config\Schema\Option\Type;

/**
 * Произвольный тип.
 */
class Tmixed extends Type
{
    
    // --- Открытые методы ---

    /**
     * {@inheritdoc}
     */
    public function validate($var)
    {
        return TRUE;
    }
}
