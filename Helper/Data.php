<?php

namespace Sunarc\Orderattachment\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
    * Configuration paths enable module
    */
    const CONFIG_PATH_MODULE_ENABLED = 'orderattachment/general/enable';

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }

    public function getAdminTemplate()
    {
        $enabledModule = $this->_scopeConfig->getValue(self::CONFIG_PATH_MODULE_ENABLED, ScopeInterface::SCOPE_STORE);
        
        if ($enabledModule) {
            $template =  'Sunarc_Orderattachment::order/view/history.phtml';
        } else {
            $template = 'Magento_Sales::order/view/history.phtml';
        }

        return $template;
    }
    public function getFrontendTemplate()
    {
        $enabledModule = $this->_scopeConfig->getValue(self::CONFIG_PATH_MODULE_ENABLED, ScopeInterface::SCOPE_STORE);
        
        if ($enabledModule) {
            $template =  'Sunarc_Orderattachment::order/order_comments.phtml';
        } else {
            $template = 'Magento_Sales::order/order_comments.phtml';
        }

        return $template;
    }
}
