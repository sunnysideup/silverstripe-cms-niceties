<?php

use SilverStripe\Core\Environment;
use SilverStripe\Core\Flushable;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Security;

class BasicFixes implements Flushable
{
    public static function flush()
    {
        if(Security::database_is_ready() && self::table_exists('Member')) {
            if(DB::get_schema()->hasTable('Member') && ! Environment::getEnv('SS_EK_SPREEK_AFRIKAANS')) {
                DB::query('UPDATE Member SET Member.Locale = \'en_US\' WHERE Member.Locale = \'af_ZA\'');
            }
        }
    }

    protected static function table_exists($tableName)
    {
        $tables = DB::table_list();
        return in_array($tableName, $tables);
    }
}
