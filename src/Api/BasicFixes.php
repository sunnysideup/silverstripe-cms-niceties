<?php

use SilverStripe\Core\Environment;
use SilverStripe\Core\Flushable;
use SilverStripe\ORM\DB;

class BasicFixes implements Flushable
{
    public static function flush()
    {
        if(DB::get_schema()->hasTable('Member') && ! Environment::getEnv('SS_EK_SPREEK_AFRIKAANS')) {
            DB::query('UPDATE Member SET Member.Locale = \'en_US\' WHERE Member.Locale = \'af_ZA\'');
        }
    }
}
