<?php
namespace Sunarc\Orderattachment\Controller\Adminhtml\Fileupload;

use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;

class Fileupload extends \Magento\Backend\App\Action
{
    protected $filesystem;
     protected $fileUploader;
    protected $orderHistoryFactory;
    protected $session;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Filesystem $filesystem,
        UploaderFactory $fileUploader,
        \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory,
        \Magento\Backend\Model\Session $session
    ) {
        $this->filesystem = $filesystem;
        $this->fileUploader = $fileUploader;
        $this->orderHistoryFactory = $orderHistoryFactory;
        $this->session = $session;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        return parent::__construct($context);
    }

    public function execute()
    {
        // print_r($this->getRequest()->getFiles());
       // this folder will be created inside "pub/media" folder
        $yourFolderName = 'order_attachment/';
        
        // "upload_custom_file" is the HTML input file name
        $yourInputFileName = 'attachment';
        
        try {
            $file = $this->getRequest()->getFiles($yourInputFileName);
            $fileName = ($file && array_key_exists('name', $file)) ? $file['name'] : null;
        
            if ($file && $fileName) {
                $target = $this->mediaDirectory->getAbsolutePath($yourFolderName);
        
                /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
                $uploader = $this->fileUploader->create(['fileId' => $yourInputFileName]);
                // set allowed file extensions
                $uploader->setAllowedExtensions(['jpg', 'pdf', 'doc', 'png', 'zip']);
                // allow folder creation
                $uploader->setAllowCreateFolders(true);
                
                // rename file name if already exists
                $uploader->setAllowRenameFiles(true);
                
                // upload file in the specified folder
                $result = $uploader->save($target);
        
                //echo '<pre>'; print_r($result); exit;
                $fileName = $uploader->getUploadedFileName();
                $this->session->setFileName($fileName);
                if ($result['file']) {
                    $this->messageManager->addSuccess(__('File has been successfully uploaded.'));
                }
                
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }
}
