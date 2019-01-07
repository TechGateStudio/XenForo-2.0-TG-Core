<?php

namespace TG\Core\Install\Upgrade;

use XF\Db\Schema\Alter;

class Upgrade1000113 extends AbstractUpgrade
{
    public function step1()
    {
        $table = 'xf_tgc_rebuild';
        
        $this->dropColumns($table, [ 'title', 'description']);
        
        $sm = $this->schemaManager();
        if (!$sm->columnExists($table, 'date'))
        {
            $this->schemaManager()->altetTable($table, function(Alter $table)
            {
               $table->addColumn('date', 'int')->nullable();
            });
        }
    }
}