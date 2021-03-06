<?php
/**
 * Database.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License, which
 * is bundled with this package in the file LICENSE.txt.
 *
 * It is also available on the Internet at the following URL:
 * https://docs.nickolasburr.com/magento/extensions/1.x/magegcs/LICENSE.txt
 *
 * @package        NickolasBurr_GoogleCloudStorage
 * @copyright      Copyright (C) 2018 Nickolas Burr <nickolasburr@gmail.com>
 * @license        MIT License
 */

class NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Database extends Mage_Core_Model_File_Storage_Database
{
    /**
     * Load object data by filename.
     *
     * @param string $filePath
     * @return NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Database|Mage_Core_Model_File_Storage_Database
     * @see Mage_Core_Model_File_Storage_Database::loadByFilename
     */
    public function loadByFilename($filePath)
    {
        /* Current backend storage code. */
        $storageCode = (int) Mage::helper('core/file_storage')->getCurrentStorageCode();

        if ($storageCode !== NickolasBurr_GoogleCloudStorage_Helper_Dict::STORAGE_MEDIA_GCS) {
            return parent::loadByFilename($filePath);
        }

        /** @var NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Bucket $backend */
        $backend = Mage::getModel(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_MODEL_CORE_FILE_STORAGE_BUCKET);
        $backend->loadByFilename($filePath);

        if ($backend->getData('id')) {
            $this->setData('id', $backend->getData('id'));
            $this->setData('filename', $backend->getData('filename'));
            $this->setData('content', $backend->getData('content'));
        }

        return $this;
    }

    /**
     * Return directory listing.
     *
     * @param string $directory
     * @return mixed
     * @see Mage_Core_Model_File_Storage_Database::getDirectoryFiles
     */
    public function getDirectoryFiles($directory)
    {
        /** @var string $relativePath */
        $relativePath = Mage::helper('core/file_storage_database')->getMediaRelativePath($directory);

        try {
            return $this->_getResource()->getDirectoryFiles($relativePath);
        } catch (\Exception $e) {
            return array();
        }
    }

    /**
     * Get object ID value.
     *
     * @return int|string
     */
    public function getId()
    {
        /* Current backend storage code. */
        $storageCode = (int) Mage::helper('core/file_storage')->getCurrentStorageCode();

        if ($storageCode !== NickolasBurr_GoogleCloudStorage_Helper_Dict::STORAGE_MEDIA_GCS) {
            return parent::getId();
        }

        return $this->getData('id');
    }

    /**
     * Delete file from bucket.
     *
     * @param string $filePath
     * @return NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Database
     */
    public function deleteFile($filePath)
    {
        /** @var NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Bucket $backend */
        $backend = Mage::getModel(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_MODEL_CORE_FILE_STORAGE_BUCKET);

        /* Delete file object from bucket. */
        $backend->deleteFile($filePath);
        parent::deleteFile($filePath);

        return $this;
    }
}
