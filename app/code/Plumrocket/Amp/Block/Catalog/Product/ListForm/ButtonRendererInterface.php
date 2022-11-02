<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Catalog\Product\ListForm;

interface ButtonRendererInterface
{
    /**
     * Must return valid amp html
     *
     * @param int $productId
     * @return string
     */
    public function renderByProduct($productId);

    /**
     * AMP state name
     *
     * @param string $stateName
     * @return ButtonRendererInterface
     */
    public function setStateName($stateName);

    /**
     * Variable in state, witch send with form, and define product id for action
     *
     * @param array $statePath
     * @return ButtonRendererInterface
     */
    public function setProductIdPath(array $statePath);

    /**
     * HTML form id
     *
     * @param string $formId
     * @return ButtonRendererInterface
     */
    public function setFormId($formId);
}
