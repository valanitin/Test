<?php
namespace Revered\GuestWishlist\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use \Magento\Cms\Model\BlockFactory;

/**
 * Class UpdateBlockData
 * @package ReveredTech\GuestWishlist\Setup\Patch\Data
 */
class UpdateBlockData
    implements DataPatchInterface,
    PatchRevertableInterface
{
    const BLOCK_IDENTIFIER = 'sidebar-static-block';
    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * UpdateBlockData constructor.
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        BlockFactory $blockFactory
    ) {
        $this->blockFactory = $blockFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $headerNoticeData = [
            'title' => 'Side Bar Custom Block',
            'identifier' => self::BLOCK_IDENTIFIER,
            'content' => '<p>Looking for your wishlist? <br>Sign in to pick up where you left off.</p>',
            'stores' => [0],
            'is_active' => 1,
        ];
        $headerNoticeBlock = $this->blockFactory
            ->create()
            ->load($headerNoticeData['identifier'], 'identifier');

        /**
         * Create the block if it does not exists, otherwise update the content
         */
        if (!$headerNoticeBlock->getId()) {
            $headerNoticeBlock->setData($headerNoticeData)->save();
        } else {
            $headerNoticeBlock->setContent($headerNoticeData['content'])->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        /**
         * No dependencies for this
         */
        return [];
    }

    /**
     * Delete the block
     */
    public function revert()
    {
        $headerNoticeBlock = $this->blockFactory
            ->create()
            ->load(self::BLOCK_IDENTIFIER, 'identifier');

        if($headerNoticeBlock->getId()) {
            $headerNoticeBlock->delete();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        /**
         * Aliases are useful if we change the name of the patch until then we do not need any
         */
        return [];
    }
}