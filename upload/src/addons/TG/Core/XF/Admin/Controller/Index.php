<?php
/**
 * Created by Spark108
 * Date: 06.01.2019 19:53
 * Project: TGCore
 * Author: Spark108 (https://vk.com/spark108 | https://spark108.ru)
 * Development Organization: TechGate Studio (https://spark108.ru/tgs)
 */

namespace TG\Core\XF\Admin\Controller;


class Index extends XFCP_Index
{
    public function actionIndex()
    {
        /** @var \XF\Mvc\Reply\View $reply */
        $reply = parent::actionIndex();

        $envReport = $reply->getParam('envReport');

        try
        {
            $diskFreeSpace = \TG\Core::FBytes(disk_free_space(\XF::getRootDirectory()));
        }
        catch (\Exception $e)
        {
            $diskFreeSpace = null;
        }

        try
        {
            $diskTotalSpace = \TG\Core::FBytes(disk_total_space(\XF::getRootDirectory()));
        }
        catch (\Exception $e)
        {
            $diskTotalSpace = null;
        }

        $envReport = array_merge([

            'disc_free_space' => $diskFreeSpace,
            'disc_total_space' => $diskTotalSpace

        ], $envReport);

        $reply->setParam('envReport', $envReport);

        return $reply;
    }
}