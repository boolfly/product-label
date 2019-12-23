<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Product Label
 */
namespace Boolfly\ProductLabel\Model\Condition\Sql;

class Expression extends \Zend_Db_Expr
{
    /**
     * Turn expression in this object into string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_expression !== '' ? '(' . $this->_expression . ')' : '';
    }
}
