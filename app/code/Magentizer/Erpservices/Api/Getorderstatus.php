<?php

/**
 * Product type provider
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magentizer\Erpservices\Api;
/**
 * @api
 */
interface Getorderstatus {
	/**
     * Retrieve available product types
     *
     * @return []
     */
	public function getorderstatusarray();
}