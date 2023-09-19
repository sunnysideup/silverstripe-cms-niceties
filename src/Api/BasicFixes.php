<?php

use SilverStripe\Core\Flushable;
use SilverStripe\ORM\DB;

class BasicFixes implements Flushable
{
    public static function flush()
    {
        DB::query('UPDATE Member SET Member.Locale = \'en_US\' WHERE Member.Locale = \'en_US\'');
    }
}
