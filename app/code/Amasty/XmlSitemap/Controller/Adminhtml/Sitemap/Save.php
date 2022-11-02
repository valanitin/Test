<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


declare(strict_types=1);

namespace Amasty\XmlSitemap\Controller\Adminhtml\Sitemap;

use Magento\Backend\App\Action;
use Amasty\XmlSitemap\Api\SitemapRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_XmlSitemap::sitemap';

    /**
     * @var SitemapRepositoryInterface
     */
    private $sitemapRepository;

    /**
     * @var \Amasty\XmlSitemap\Model\SitemapFactory
     */
    private $sitemapFactory;

    public function __construct(
        Action\Context $context,
        \Amasty\XmlSitemap\Model\SitemapFactory $sitemapFactory,
        SitemapRepositoryInterface $sitemapRepository
    ) {
        parent::__construct($context);
        $this->sitemapRepository = $sitemapRepository;
        $this->sitemapFactory = $sitemapFactory;
    }

    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            $id = (int)$this->getRequest()->getParam('id');
            try {
                /** @var \Amasty\XmlSitemap\Model\Sitemap $model */
                $profile = $this->sitemapRepository->getById($id);
            } catch (NoSuchEntityException $exception) {
                $profile = $this->sitemapFactory->create();
            }

            $this->normalizeArray('exclude_product_type', $data);

            $profile->setData($data);

            try {
                $this->sitemapRepository->save($profile);
                $profileId = $profile->getId();

                $this->messageManager->addSuccessMessage(__('Sitemap was successfully saved'));
                $this->_session->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $profileId]);
                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_session->setFormData($data);
                $this->_redirect('*/*/edit', ['id' => $id]);

                return;
            }
        }
        $this->messageManager->addErrorMessage(__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    /**
     * @param string $key
     * @param array|null $data
     */
    private function normalizeArray(string $key, ?array &$data)
    {
        if (isset($data[$key]) && is_array($data[$key])) {
            $data[$key] = implode(',', $data[$key]);
        } else {
            $data[$key] = '';
        }
    }
}
