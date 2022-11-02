<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */
declare(strict_types=1);

namespace LuxuryUnlimited\SocialLogin\Api;

interface SocialLoginInterface
{
    /**
     * Social Login 
     *
     * @api
     * @return string[]
     */
    public function login();
}
