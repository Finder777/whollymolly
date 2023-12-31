<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ImportExport\Controller\Adminhtml\History;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\ImportExport\Model\Import;

/**
 * Download history controller
 */
class Download extends \Magento\ImportExport\Controller\Adminhtml\History implements HttpGetActionInterface
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        parent::__construct(
            $context
        );
        $this->fileFactory = $fileFactory;
        $this->resultRawFactory = $resultRawFactory;
    }

    /**
     * Download backup action
     *
     * @return void|\Magento\Backend\App\Action
     */
    public function execute()
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $fileName = basename($this->getRequest()->getParam('filename'));

        /** @var \Magento\ImportExport\Helper\Report $reportHelper */
        $reportHelper = $this->_objectManager->get(\Magento\ImportExport\Helper\Report::class);

        if (!$reportHelper->importFileExists($fileName)) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/history');
            return $resultRedirect;
        }

        return $this->fileFactory->create(
            $fileName,
            ['type' => 'filename', 'value' => Import::IMPORT_HISTORY_DIR . $fileName],
            DirectoryList::VAR_IMPORT_EXPORT,
            'application/octet-stream',
            $reportHelper->getReportSize($fileName)
        );
    }
}
