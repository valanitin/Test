<?php

namespace LuxuryUnlimited\App\Helper;

use Psr\Log\LoggerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\TransportInterface;
use Zend_Mime;
use Zend\Mime\Part;

class Sender extends \Magento\Framework\App\Helper\AbstractHelper
{
    const EMAIL_RECIPIENT_PATH = 'trans_email/ident_support/email';

    const NAME_RECIPIENT_PATH = 'trans_email/ident_support/name';

    const EMAIL_TEMPLATE = 'appdownload/general/template_number';

    /** @var \Magento\Framework\Mail\Template\TransportBuilder */
    protected $_transportBuilder;

    /** @var \Magento\Framework\Translate\Inline\StateInterface */
    protected $inlineTranslation;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var \Magento\Framework\Escaper */
    protected $_escaper;

    /** @var LoggerInterface */
    protected $logger;

    /** @var \Magento\Framework\Filesystem\Io\File */
    protected $file;

    /**
     * Sender constructor.
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Escaper $escaper
     * @param Context $context
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper,
        Context $context
    ) {
        $this->file = $file;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
        $this->logger = $logger;

        parent::__construct($context);
    }

    /**
     * @param $data
     * @return bool
     */
    public function notifyClient($data)
    {
        try {
            return $this->sendEmail(
                $this->getConfig(self::NAME_RECIPIENT_PATH),
                $this->getConfig(self::EMAIL_RECIPIENT_PATH),
                '',
                $data['email'],
                $data,
                "client"
            );
        } catch (\Exception $e) {

            $this->logger->critical($e->getMessage(), [$e->getFile(), $e->getLine()]);
            $this->inlineTranslation->resume();
            return false;
        }
    }

    /**
     * @param $fromName
     * @param $from
     * @param $toName
     * @param $to
     * @param array $data
     * @param string $type
     * @return bool
     */
    public function sendEmail($fromName, $from, $toName, $to, $data = [], $type = "client")
    {

        $this->inlineTranslation->suspend();

        try {
            $sender = [
                'name' => $fromName,
                'email' => $from
            ];

            $data = $this->getData($data, $type);
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);

            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->getConfig(self::EMAIL_TEMPLATE))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        // this is using frontend area to get the template file
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFromByScope($sender)
                ->addTo($to, $toName);

            // Attach Images to transport
            foreach ($this->getAttachments($data, $type) as $a) {
                if (isset($a["file"]) && isset($a["name"])) {

                    $transport->addAttachment($this->file->read($a["file"]), $a['name'], mime_content_type($a["file"]));
                }
            }

            $transport = $transport->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
            return true;
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), [
                $e->getFile(),
                $e->getLine()
            ]);
            $this->inlineTranslation->resume();
            return false;
        }
    }

    /**
     * @param $data
     * @param string $type
     * @return mixed
     */
    public function getData($data, $type = "client")
    {
        return $data;
    }

    /**
     * @param null $type
     * @param array $data
     * @return array
     */
    public function getAttachments($data = [], $type = "client")
    {
        return [];
    }

    /**
     * @param $path
     * @param null $storeId
     * @return mixed
     */
    public function getConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
}
