<?php

/**
 *
 * The collection of Domain objects
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class MessagesCollection extends Core_Collection_Super
{
    protected $_domainObjectClass = 'Message';

    public function anyNew()
    {
        if (!$this->count()) {
            return false;
        }

        foreach ($this as $m) {
            if ($m->new) {
                return true;
            }
        }

        return false;
    }
}
