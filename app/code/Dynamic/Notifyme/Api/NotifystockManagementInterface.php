<?php
namespace Dynamic\Notifyme\Api;

/**
 * Interface NotifystockManagementInterface
 *
 * @package Dynamic\Notifyme\Api
 */
interface NotifystockManagementInterface
{
    /**
     *  Notifyme form.
     *
     * @param mixed $notifymeForm
     * 
     * @return \Dynamic\Notifyme\Api\NotifystockInterface
     */
    public function submitForm($notifymeForm);
}