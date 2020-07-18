<?php

namespace Lof\All\Cron;

use Lof\All\Helper\Data;
use Lof\All\Model\LicenseFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class CheckLicense extends Action
{
    protected $_licenseFactory;
    protected $helper;
    protected $_storeManager;

    public function __construct(Context $context,
                                LicenseFactory $licenseFactory,
                                Data $helper,
                                \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->_licenseFactory = $licenseFactory;
        $this->helper = $helper;
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
        $domain = $this->getCurrentDomain();
        $data = [
            "domain" => $domain,
            "product_sku" => $moduleName
        ];

        $curl = curl_init();

        $url = "http://lofextension.localhost/rest/V1/lofLicense/check";
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_exec($curl);
        curl_close($curl);
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
