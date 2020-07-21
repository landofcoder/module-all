<?php

namespace Lof\All\Cron;

use Lof\All\Helper\Data;
use Lof\All\Model\LicenseFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Lof\All\Block\Adminhtml\System\ListLicense;

class CheckLicense extends Action
{
    protected $_licenseFactory;
    protected $_helper;
    protected $_storeManager;

    public function __construct(Context $context,
                                LicenseFactory $licenseFactory,
                                Data $helper,
                                \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->_licenseFactory = $licenseFactory;
        $this->_helper = $helper;
        $this->_storeManager=$storeManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $model = $this->_licenseFactory->create();
        $dataModule = $model->getCollection()->getData();
        foreach ($dataModule as $item) {
            $moduleName = $item['extension_code'];
            $this->checkLicense($moduleName);
        }
    }

    public function checkLicense($moduleName)
    {
        //$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        //$logger = new \Zend\Log\Logger();
        //$logger->addWriter($writer);
        //$logger->info('text message: '.$moduleName);

        $domain = $this->getCurrentDomain();
        $data = [
            "domain" => $domain,
            "product_sku" => $moduleName
        ];
        $api_url = ListLicense::getApiCheckUrl();
        $response = $this->_helper->makePostRequest($api_url, $data);
        // $curl = curl_init();

        // $url = "http://bicomart.demo4coder.com/rest/V1/lofLicense/check";
        // curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        // curl_exec($curl);
        // curl_close($curl);
    }

    public function getCurrentDomain(){
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $url= trim($url, '/');
        if (!preg_match('#^http(s)?://#', $url)) {
            $url = 'http://' . $url;
        }
        $urlParts = parse_url($url);
        $domain = preg_replace('/^www\./', '', $urlParts['host']);
        return $domain;
    }
}
