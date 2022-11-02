<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Account;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Magento\Framework\ImageFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\Validation\ValidationException;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\SocialLoginFree\Model\Account\Photo\Loader;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * @since 3.0.0
 */
class Photo
{
    const PHOTO_DIRECTORY = 'pslogin' . DIRECTORY_SEPARATOR . 'photo';
    const CUSTOMER_PHOTO_ATTRIBUTE_CODE = 'pr_photo';

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var IoFile
     */
    private $ioFile;

    /**
     * @var \Magento\Framework\ImageFactory
     */
    private $imageFactory;

    /**
     * @var string
     */
    private $photoDir;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Photo\Loader
     */
    private $photoLoader;

    /**
     * @var int
     */
    private $photoSize;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Photo constructor.
     *
     * @param \Magento\Framework\Filesystem                          $filesystem
     * @param \Magento\Framework\Filesystem\Io\File                  $ioFile
     * @param \Magento\Framework\ImageFactory                        $imageFactory
     * @param \Plumrocket\SocialLoginFree\Model\Account\Photo\Loader $photoLoader
     * @param \Magento\Store\Model\StoreManagerInterface             $storeManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface      $customerReository
     * @param int                                                    $photoSize
     */
    public function __construct(
        Filesystem $filesystem,
        IoFile $ioFile,
        ImageFactory $imageFactory,
        Loader $photoLoader,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerReository,
        int $photoSize = 150
    ) {
        $this->filesystem = $filesystem;
        $this->ioFile = $ioFile;
        $this->imageFactory = $imageFactory;
        $this->photoLoader = $photoLoader;
        $this->photoSize = $photoSize;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerReository;
    }

    /**
     * Retrieve url for customer photo
     *
     * @param int $customerId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPhotoUrl($customerId): string
    {
        if (! $customerId) {
            return '';
        }

        $customer = $this->getCustomer($customerId);
        if (! $customer) {
            return '';
        }

        $photoAttribute = $customer->getCustomAttribute(self::CUSTOMER_PHOTO_ATTRIBUTE_CODE);
        if (null === $photoAttribute) {
            return '';
        }

        $fileName = $photoAttribute->getValue();
        $path = $this->getBasePhotoDir() . DIRECTORY_SEPARATOR . $fileName;

        if (! $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->isExist($path)) {
            return '';
        }

        return $this->getBasePhotoUrl() . $fileName;
    }

    /**
     * @param int    $customerId
     * @param string $fileUrl
     * @return bool
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function saveExternal(int $customerId, string $fileUrl): bool
    {
        if (! $fileUrl || ! $customerId) {
            throw new ValidationException(__('invalid params for save photo to customer'));
        }

        $tmpPath = $this->getTmpDir($customerId);
        $upload = false;

        try {
            $this->ioFile->mkdir($this->getBasePhotoDir());

            $fileContent = $this->photoLoader->load($fileUrl);

            if ($fileContent && file_put_contents($tmpPath, $fileContent) > 0) {
                /** @var \Magento\Framework\Image $image */
                $image = $this->imageFactory->create(['fileName' => $tmpPath]);
                $image->resize($this->photoSize);
                $fileExtension = image_type_to_extension($image->getImageType());
                $customerPhotoName = $this->getCustomerPhotoName($customerId, $fileExtension);

                $image->save(null, $customerPhotoName);
                $savePhoto = $this->saveCustomerPhotoName($customerId, $customerPhotoName);

                if ($savePhoto) {
                    $upload = true;
                }
            }
        } catch (\Exception $e) {
            $upload = false;
        } finally {
            if ($this->ioFile->fileExists($tmpPath)) {
                $this->ioFile->rm($tmpPath);
            }
        }

        return $upload;
    }

    /**
     * @return string
     */
    public function getBasePhotoDir(): string
    {
        if (! $this->photoDir) {
            $this->photoDir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
                                               ->getAbsolutePath(self::PHOTO_DIRECTORY);
        }
        return $this->photoDir;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBasePhotoUrl(): string
    {
        return $this->storeManager
                ->getStore()
                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . self::PHOTO_DIRECTORY . DIRECTORY_SEPARATOR;
    }

    /**
     * @param int $customerId
     * @return string
     */
    public function getTmpDir(int $customerId): string
    {
        return $this->getBasePhotoDir() . DIRECTORY_SEPARATOR . $customerId . '.tmp';
    }

    /**
     * @param int $customerId
     * @param string $fileExtension
     * @return string
     */
    private function getCustomerPhotoName(int $customerId, string $fileExtension): string
    {
        return "{$customerId}{$fileExtension}";
    }

    /**
     * @param int    $customerId
     * @param string $customerPhotoName
     * @return bool
     */
    private function saveCustomerPhotoName(int $customerId, string $customerPhotoName): bool
    {
        $savePhoto = false;
        $customer = $this->getCustomer($customerId);

        if ($customer) {
            $customer->setCustomAttribute(self::CUSTOMER_PHOTO_ATTRIBUTE_CODE, $customerPhotoName);
            try {
                $this->customerRepository->save($customer);
                $savePhoto = true;
            } catch (InputException $e) {
                $savePhoto = false;
            } catch (InputMismatchException $e) {
                $savePhoto = false;
            } catch (LocalizedException $e) {
                $savePhoto = false;
            }
        }

        return $savePhoto;
    }

    /**
     * @param $customerId
     * @return bool|\Magento\Customer\Api\Data\CustomerInterface
     */
    private function getCustomer($customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            $customer = false;
        } catch (LocalizedException $e) {
            $customer = false;
        }

        return $customer;
    }
}
