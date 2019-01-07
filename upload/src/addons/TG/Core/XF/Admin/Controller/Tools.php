<?php

namespace TG\Core\XF\Admin\Controller;

class Tools extends XFCP_Tools
{
    public function actionRebuild()
    {
        $reply = parent::actionRebuild();
        if (!($reply instanceof \XF\Mvc\Reply\View))
        {
            $rebuildId = $this->filter('job_id', 'str');
            if ($rebuildId)
            {
                $rebuild = $this->finder('TG\Core:Rebuild')
                    ->where('rebuild_id', $rebuildId)
                    ->fetchOne();
                    
                $rebuild->fastUpdate('date', \XF::$time);
            }
            
            return $reply;
        }
        
        $rebuilds = $this->finder('TG\Core:Rebuild')
            ->fetch();
        $reply->setParams([
            'rebuilds' => $rebuilds
        ]);
        
        return $reply;
    }
}