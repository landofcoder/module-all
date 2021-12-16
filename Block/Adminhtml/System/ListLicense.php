<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_All
 * @copyright  Copyright (c) 2017 Landofcoder (https://www.landofcoder.com/)
 * @license    https://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\All\Block\Adminhtml\System;
use Magento\Framework\App\Filesystem\DirectoryList;

class ListLicense extends \Magento\Config\Block\System\Config\Form\Field
{

    const API_URL      = 'https://landofcoder.com/api/soap/?wsdl=1';
    const SITE_URL      = 'https://landofcoder.com';
    const API_USERNAME = 'checklicense';
    const API_PASSWORD = 'n2w3z2y0kc';

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var string
     */
    protected $_key_path;

    /**
     * @var \Lof\All\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_remoteAddress;

    /**
     * @var \Lof\All\Model\License
     */
    protected $_license;

    private $_list_files = [];

    /**
     * [__construct description]
     * @param \Magento\Backend\Block\Template\Context              $context
     * @param \Magento\Framework\App\ResourceConnection            $resource
     * @param \Lof\All\Helper\Data                                 $helper
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lof\All\Helper\Data $helper,
        \Lof\All\Model\License $license,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
        )
    {
        parent::__construct($context);
        $this->_resource      = $resource;
        $this->_helper        = $helper;
        $this->_remoteAddress = $remoteAddress;
        $this->_license       = $license;
    }

    public function getListLicenseFiles() {
        if(!$this->_list_files) {
            $path = $this->_filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('code/Lof/');
            $files = glob($path . '*/*/license.xml');
            $path2 = $this->_filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath('vendor/Lof/');
            $files2 = glob($path2 . '*/*/license.xml');
            $path3 = $this->_filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath('vendor/landofcoder/');
            $files3 = glob($path3 . '*/*/license.xml');
            $path4 = $this->_filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath('vendor/lof/');
            $files4 = glob($path4 . '*/*/license.xml');
            $path5 = $this->_filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('code/Lofmp/');
            $files5 = glob($path5 . '*/*/license.xml');

            $path6 = $this->_filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath('vendor/magento2-modules/');
            $files6 = glob($path6 . '*/*/license.xml');


            if(is_array($files) && $files) {
                $this->_list_files = array_merge($this->_list_files, $files);
            }
            if(is_array($files2) && $files2) {
                $this->_list_files = array_merge($this->_list_files, $files2);
            }
            if(is_array($files3) && $files3) {
                $this->_list_files = array_merge($this->_list_files, $files3);
            }
            if(is_array($files4) && $files4) {
                $this->_list_files = array_merge($this->_list_files, $files4);
            }
            if(is_array($files5) && $files5) {
                $this->_list_files = array_merge($this->_list_files, $files5);
            }
            if(is_array($files6) && $files6) {
                $this->_list_files = array_merge($this->_list_files, $files6);
            }
        }
        return $this->_list_files;
    }
    /**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $files = $this->getListLicenseFiles();
        /*
        if (!extension_loaded('soap')) {
            $extensions = [];
            foreach ($files as $file) {
                $xmlObj = new \Magento\Framework\Simplexml\Config($file);
                $xmlData = $xmlObj->getNode();
                $sku = $xmlData->code;
                $name = $xmlData->name;

                $licenseCollection = $this->_license->getCollection();
                foreach ($licenseCollection as $klience => $vlience) {
                    if($vlience->getData('extension_code') == $sku){
                        $vlience->delete();
                    }
                }

                $licenseData = [];
                $licenseData['extension_code'] = $sku;
                $licenseData['extension_name'] = $name;
                $licenseData['status'] = 2;
                $this->_license->setData($licenseData)->save();
            }
            echo __('Please enable the SOAP extension on server, it\'s required in Magento2, check more details at <a href="http://devdocs.magento.com/guides/v2.1/install-gde/system-requirements-tech.html#required-php-extensions" target="_blank">here</a>. If you can not enable the SOAP, please skip the license message, you can active in the future. We are sorry for any inconvenience. ');
            return;
        }

        if (!extension_loaded('soap')) {
            throw new \Magento\Framework\Webapi\Exception(
                __('SOAP extension is not loaded.'),
                0,
                \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
            );
        }

        $email = $html = '';
        $products = array();
        try{
            $opts = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false
                    )
                );
            $context = stream_context_create($opts);
            $params = array('soap_version'=>SOAP_1_2,
                            'verifypeer' => false,
                            'verifyhost' => false,
                            'exceptions' => 1,
                            'stream_context'=>$context);

            $proxy = new \SoapClient(self::API_URL, $params);
            $sessionId = $proxy->login(self::API_USERNAME, self::API_PASSWORD);
            $products = $proxy->call($sessionId, 'veslicense.productlist');

        }catch(SoapFault $e){

        }
        */
        $email = $html = '';
        $list_products = $this->getProductList();
        $products = isset($list_products['products'])?$list_products['products']:[];
        $extensions = [];
        foreach ($files as $file) {
            $xmlObj = new \Magento\Framework\Simplexml\Config($file);
            $xmlData = $xmlObj->getNode();
            $sku = $xmlData->code;
            $name = $xmlData->name;
            if($email=='' && (string)($xmlData->email)){
                $email = $xmlData->email;
            }
            if($products){
                foreach($products as $_product){
                    if($sku == $_product['sku']){
                        $_product['extension_name'] = (string)$name;
                        $_product['purl'] = $xmlData->item_url;
                        $_product['item_title']     = $xmlData->item_title;
                        $_product['version']        = $xmlData->version;
                        $_product['key']            = ($xmlData->key)?$xmlData->key:'';
                        $extensions[] = $_product;
                        break;
                    }
                }
            }else {
                $_product = [];
                $_product['extension_name'] = (string)$name;
                $_product['purl']           = $xmlData->item_url;
                $_product['item_title']     = $xmlData->item_title;
                $_product['version']        = $xmlData->version;
                $_product['sku']            = $sku;
                $_product['key']            = ($xmlData->key)?$xmlData->key:'';
                $_product['pimg']           = ($xmlData->pimg)?$xmlData->pimg:'';
                $extensions[] = $_product;
            }
        }

        if ($email) {
            throw new \RuntimeException(__('Something went wrong while validating license. Please contact %1', $email));
        }

        if(!empty($extensions)){
            $connection = $this->_resource->getConnection();
            $html .= '<div class="vlicense">';
            $html .= '<h1 style="margin-bottom: 50px;text-align: center;">LOF Licenses</h1>';
            foreach ($extensions as $_extension) {
                $name = str_replace('[licenses]', '[' . str_replace(['-','_',' '], [''], $_extension['sku']) . ']', $element->getName());
                $value = $this->_helper->getConfig('general/' . str_replace(['-','_',' '], [''], $_extension['sku']),'loflicense',null);
                if(!$value && isset($_extension['license']) && $_extension['license']){
                    $value = $_extension['license'];
                }
                if(!$value && isset($_extension['key']) && $_extension['key']){
                    $value = $_extension['key'];
                }
                $value = trim($value);
                $baseUrl = $this->_storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_WEB
                    );
                $remoteAddress = $this->_remoteAddress->getRemoteAddress();
                $domain        = $this->getDomain($baseUrl);
                $response = $this->verifyLicense($value,$_extension['sku'], $domain, $remoteAddress);
                $license = isset($response["license"])?$response["license"]:false;
                /*
                $license       = $proxy->call($sessionId, 'veslicense.active', array($value, $_extension['sku'], $domain, $remoteAddress));*/
                if (!is_array($license) && $license === 1) {
                    $license = [];
                    $license['is_valid'] = 0;
                }
                if ($license === true) {
                    $license = [];
                    $license['is_valid'] = 1;
                }

                $html .= '<div class="vitem">';
                $html .= '<div class="pimg">';
                $html .= '<a href="' . $_extension['purl'] . '" target="_blank" title="' . $_extension['name'] . '"><img src="' .  $_extension['pimg'] . '"/></a>';
                $html .= '</div>';
                $html .= '<div class="pdetails">';
                $html .=  '<h1><a href="' . $_extension['purl'] . '" target="_blank" title="' . $_extension['name'] . '">' . str_replace(' for Magento 2', '', $_extension['name']) . '</a></h1>';
                $html .= '<div>';
                $html .= '<span class="plicense"><strong>License Serial</strong></span>';
                $html .= '<div><input type="text" name="' . $name . '" value="' . $value . '"/></div>';
                $html .= '<div class="pmeta">';
                if(!empty($license) && $license['is_valid']){
                    $html .= '<p><strong>Status: </strong><span class="pvalid">Valid</span></p>';
                }else{
                    $html .= '<p><strong>Status: </strong><span class="pinvalid">Invalid</span></p>';
                }
                if(!empty($license) && isset($license['description'])){
                    $html .= $license['description'];
                }
                if(!empty($license) && isset($license['created_at'])){
                    $html .= '<p><strong>Activation Date:</strong> ' . $license['created_at'] . '</p>';
                }
                if(!empty($license) && isset($license['expired_time'])){
                    $html .= '<p><strong>Expiration Date:</strong> ' . $license['expired_time'] . '</p>';
                }
                $html .= '</div>';
                $licenseCollection = $this->_license->getCollection();
                foreach ($licenseCollection as $klience => $vlience) {
                    if($vlience->getData('extension_code') == $_extension['sku']){
                        $vlience->delete();
                    }
                }
                $licenseData = [];
                if(isset($_extension['sku'])){
                    $licenseData['extension_code'] = $_extension['sku'];
                }
                if(isset($_extension['name'])){
                    $licenseData['extension_name'] = $_extension['name'];
                }
                if(empty($license) || !$license['is_valid']){
                    $licenseData['status'] = 0;
                }else{
                    $licenseData['status'] = 1;
                }
                $licenseResource = $this->_license->getResource();
                $license = $this->_license->setData($licenseData);
                $licenseResource->save($license);
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
        }else{
            $licenseCollection = $this->_license->getCollection();
            foreach ($licenseCollection as $klience => $vlience) {
                $vlience->delete();
            }
        }
        return $this->_decorateRowHtml($element, $html);
    }
    public function getProductList() {
        try{
            //Authentication rest API magento2, get access token
            $url = self::getListUrl();
            $direct_url = $url."?pc_list=true";
            $response = @file_get_contents($direct_url);
            if(!$response) {
                $key_path = $this->getKeyPath();
                $data = array("pc_list"=>true);
                $crl = curl_init();
                curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, TRUE);
                curl_setopt($crl, CURLOPT_CAPATH, $key_path);
                curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($crl, CURLOPT_URL, $url);
                curl_setopt($crl, CURLOPT_HEADER, 0);
                curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($crl, CURLOPT_POST, 1);
                curl_setopt($crl, CURLOPT_POSTFIELDS, $data);
                $response = curl_exec($crl);
                if ($response) {
                }
                else {
                    $response = @file_get_contents($url);
                    if(!$response) {
                        echo 'An error has occurred: ' . curl_error($crl);
                        return[];
                    }
                }
                curl_close($crl);
            }
            return json_decode($response, true);
        } catch(\Exception $e) {

        }
        return [];
    }

    public function verifyLicense($license_key, $extension, $domain, $ip) {
        try{
            //Authentication rest API magento2, get access token
            $url = self::getVerifyUrl();
            $direct_url = $url."?license_key=".$license_key."&extension=".$extension.'&domain='.$domain.'&ip='.$ip;
            $response = @file_get_contents($direct_url);
            if(!$response) {
                $key_path = $this->getKeyPath();
                $data = array("license_key"=>$license_key,"extension"=>$extension,"domain"=>$domain,"ip"=>$ip);
                $crl = curl_init();
                curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, TRUE);
                curl_setopt($crl, CURLOPT_CAPATH, $key_path);
                curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($crl, CURLOPT_URL, $url);
                curl_setopt($crl, CURLOPT_HEADER, 0);
                curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($crl, CURLOPT_POST, 1);
                curl_setopt($crl, CURLOPT_POSTFIELDS, $data);
                $response = curl_exec($crl);
                if ($response) {
                }
                else {
                    $url .="?license_key=".$license_key."&extension=".$extension."&domain=".$domain."&ip=".$ip;
                    $response = @file_get_contents($url);
                    if(!$response) {
                        echo 'An error has occurred: ' . curl_error($crl);
                        return[];
                    }
                }
                curl_close($crl);
            }
            return json_decode($response, true);
        } catch(\Exception $e) {

        }
        return [];
    }
    public static function getListUrl() {
        $url = ListLicense::SITE_URL;
        return $url."/license/listproducts";
    }
    public static function getVerifyUrl() {
        $url = ListLicense::SITE_URL;
        return $url."/license/verify";
    }
    public function getKeyPath(){
        if(!$this->_key_path){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
            $base_url = $directory->getRoot();
            $this->_key_path = $base_url."/veslicense/cacert.pem";
        }
        return $this->_key_path;
    }
    public function getDomain($domain) {
        $domain = strtolower($domain);
        $domain = str_replace(['www.','WWW.','https://','http://','https','http'], [''], $domain);
        if($this->endsWith($domain, '/')){
            $domain = substr_replace($domain ,"",-1);
        }
        return $domain;
    }
    public function endsWith($haystack, $needle) {
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }
}
