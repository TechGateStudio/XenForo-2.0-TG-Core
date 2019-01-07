<?php

namespace TG;

class Core extends \XF
{
    public static function newArrayCollection(array $array)
    {
        return new \TG\Core\Mvc\Entity\ArrayCollection($array);
    }
    
    public static function canTGAddOn($addOnOrId)
    {
        $tms = [
            'TG/', 'SXF/', 'SXF', 'West/'
        ];

        if (
            $addOnOrId instanceof \XF\AddOn\AddOn || 
            $addOnOrId instanceof \XF\Entity\AddOn
        )
        {
            $addOnOrId = $addOnOrId->addon_id;
        }

        foreach ($tms as $tm)
        {
            if (substr($addOnOrId, 0, strlen($tm)) == $tm)
            {
                return true;
            }
        }

        return false;
    }
    
    public static function getInstallManager($addOnOrId, \XF\App $app = null)
    {
        if ($addOnOrId instanceof \XF\Entity\AddOn || is_string($addOnOrId))
        {
            $addOnOrId = new \XF\AddOn\AddOn($addOnOrId);
        }
        
        if (!$app)
        {
            $app = self::app();
        }
        
        return new \TG\Core\Install\Manager($addOnOrId, $app);
    }

    public static function FBytes($bytes, $precision = 2)
    {
        $symbols = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
        $exp = floor(log($bytes)/log(1024));

        $phraseName = 'tgc_file_size_' . $symbols[$exp];
        $phrase = self::phrase($phraseName);

        if ($phrase == $phraseName)
        {
            $phrase = $symbols[$exp];
        }

        return sprintf('%.' . $precision . 'f ' . $phrase, ($bytes/pow(1024, floor($exp))));
    }
}