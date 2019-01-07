<?php

namespace TG\Core\Install\Upgrade;

class Upgrade1000111 extends AbstractUpgrade
{
    public function step1()
    {
        $this->importTable('xf_tgc_rebuild');
    }
}