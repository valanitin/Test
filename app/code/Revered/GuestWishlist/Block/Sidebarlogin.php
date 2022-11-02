<?php
namespace Revered\GuestWishlist\Block;
class Sidebarlogin extends \Magento\Framework\View\Element\Template
{
  /**
   * @var \Magento\Store\Model\StoreManagerInterface
   */
  protected $_storeManager;
  /**
   * @var \Magento\Cms\Model\BlockFactory
   */
  protected $_blockFactory;
  /**
   * @var \Magento\Cms\Model\Template\FilterProvider
   */
  protected $filterProvider;
  /**
   * Sidebarlogin constructor.
   * @param \Magento\Framework\View\Element\Template\Context $context
   * @param \Magento\Store\Model\StoreManagerInterface $storeManager
   * @param \Magento\Cms\Model\BlockFactory $blockFactory
   * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
   * @param array $data
   */
  public function __construct(
      \Magento\Framework\View\Element\Template\Context $context,
      \Magento\Store\Model\StoreManagerInterface $storeManager,
      \Magento\Cms\Model\BlockFactory $blockFactory,
      \Magento\Cms\Model\Template\FilterProvider $filterProvider,
      array $data = []
  ) {
      parent::__construct(
          $context,
          $data
      );
      $this->_storeManager = $storeManager;
      $this->_blockFactory = $blockFactory;
      $this->filterProvider = $filterProvider;
  }
  public function getstaticBlockContent($blockId)
  {
      $html = '';
      if ($blockId) {
          $storeId = $this->_storeManager->getStore()->getId();
          /** @var \Magento\Cms\Model\Block $block */
          $block = $this->_blockFactory->create();
          $block->setStoreId($storeId)->load($blockId);
          $html = $this->filterProvider->getBlockFilter()
                       ->setStoreId($storeId)
                       ->filter($block->getContent());
      }
      return $html;
  }
}