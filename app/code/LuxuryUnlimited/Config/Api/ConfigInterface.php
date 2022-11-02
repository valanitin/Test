<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */
declare(strict_types=1);

namespace LuxuryUnlimited\Config\Api;

interface ConfigInterface
{
    /**
     * Get Config Data
     *
     * @api
     * @return string[]
     */
    public function getConfig();

    /**
     * Update Config Data
     *
     * @api
     * @return string[]
     */
    public function updateConfig();
}
