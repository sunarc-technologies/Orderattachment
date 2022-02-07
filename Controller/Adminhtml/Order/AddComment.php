<?php
namespace Sunarc\Orderattachment\Controller\Adminhtml\Order;

class AddComment extends \Magento\Sales\Controller\Adminhtml\Order\AddComment
{
    protected $session;

     /**
      * Add order comment action
      *
      * @return \Magento\Framework\Controller\ResultInterface
      */
    public function execute()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                $data = $this->getRequest()->getPost('history');
                
                if (empty($data['comment']) && $data['status'] == $order->getDataByKey('status')) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('The comment is missing. Enter and try again.')
                    );
                }

                $order->setStatus($data['status']);
                $notify = $data['is_customer_notified'] ?? false;
                $visible = $data['is_visible_on_front'] ?? false;

                if ($notify && !$this->_authorization->isAllowed(self::ADMIN_SALES_EMAIL_RESOURCE)) {
                    $notify = false;
                }
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $fileName = $objectManager->get('Magento\Backend\Model\Session')->getFileName();
                $history = $order->addStatusHistoryComment($data['comment'], $data['status']);
                $history->setIsVisibleOnFront($visible);
                $history->setIsCustomerNotified($notify);
                $history->setAttachment($fileName);
                $history->save();

                // $this->session->setHistoryId($history->getId());
                $comment = trim(strip_tags($data['comment']));

                $order->save();
                /** @var OrderCommentSender $orderCommentSender */
                $orderCommentSender = $this->_objectManager
                    ->create(\Magento\Sales\Model\Order\Email\Sender\OrderCommentSender::class);

                $orderCommentSender->send($order, $notify, $comment);

                return $this->resultPageFactory->create();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $response = ['error' => true, 'message' => $e->getMessage()];
            } catch (\Exception $e) {
                $response = ['error' => true, 'message' => __('We cannot add order history.')];
            }
            if (is_array($response)) {
                $resultJson = $this->resultJsonFactory->create();
                $resultJson->setData($response);
                return $resultJson;
            }
        }
        return $this->resultRedirectFactory->create()->setPath('sales/*/');
    }
}
