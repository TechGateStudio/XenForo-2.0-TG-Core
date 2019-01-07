<?php

namespace TG\Core\Install\Upgrade;

class Upgrade1000214 extends AbstractUpgrade
{
    public function step1()
    {
        $table = 'xf_tgc_rebuild';
        $data = $this->manager->getMySQLData()[$table];

        if (isset($data['query']))
        {
            $query = str_ireplace('[table]', $table, $data['query']);
            $this->db()->query($query);
        }
    }
}