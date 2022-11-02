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

namespace Plumrocket\Amp\Block\Adminhtml\Cms\Page\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class AmpValidationButton extends \Magento\Cms\Block\Adminhtml\Page\Edit\GenericButton implements ButtonProviderInterface
{
    const GOOGLE_TEST_AMP_URL = 'https://search.google.com/test/amp';

    /**
     * @var string
     */
    private $googleTestAmpParams = '?utm_source=gws&utm_medium=onebox&utm_campaign=suit&url=';

    /**
     * @return array
     */
    public function getButtonData()
    {
        $validationUrl = $this->getValidationUrl();

        return [
            'label' => __('AMP Validation'),
            'title' => __('Open this CMS page in AMP validator'),
            'class' => 'validate' . ($validationUrl ? '' : ' disabled'),
            'sort_order' => 30,
            'on_click' => 'window.open(\'' . $validationUrl . '\', \'_blank\')',
        ];
    }

    /**
     * @return bool|string
     */
    public function getValidationUrl()
    {
        if ($pageId = $this->getPageId()) {
            /** @var \Magento\Cms\Model\Page $model */
            $model = $this->pageRepository->getById($pageId);
            if ($model->isActive() && $model->getIdentifier()) {
                return self::GOOGLE_TEST_AMP_URL
                    . $this->getGoogleTestAmpParams()
                    . $this->context->getUrlBuilder()->getDirectUrl($model->getIdentifier())
                    . '?amp=1';
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getGoogleTestAmpParams()
    {
        return $this->googleTestAmpParams;
    }
}
