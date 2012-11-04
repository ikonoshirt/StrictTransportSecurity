<?php
class Ikonoshirt_StrictTransportSecurity_Model_Observer
{
    const CONFIG_XML_USE_STS = 'ikonoshirt/strictTransportSecurity/is_on';
    const CONFIG_XML_MAX_AGE = 'ikonoshirt/strictTransportSecurity/max_age';
    const CONFIG_XML_INCLUDE_SUBDOMAINS = 'ikonoshirt/strictTransportSecurity/include_subdomains';

    public function controllerActionPredispatch(Varien_Event_Observer $observer)
    {
        $controllerAction = $observer->getControllerAction();
        $useSts = Mage::getStoreConfig(self::CONFIG_XML_USE_STS);
        if (!$useSts) {
            return;
        }

        // check wether secure and unsecure url are https
        $secureBaseUrl = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL);
        $unSecureBaseUrl = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL);

        /* @var $adminSession Mage_Admin_Model_Session */
        $adminSession = Mage::getSingleton('admin/session');

        $abort = false;
        if (strpos($secureBaseUrl, 'https://') !== 0) {
            $message = 'HTTP Strict Transport Security activated, but secure base url not HTTPS.';

            Mage::log($message);
            $adminSession->addError($message);

            $abort = true;
        }

        if (!strpos($unSecureBaseUrl, 'https://') !== 0) {
            $message = 'HTTP Strict Transport Security activated, but unsecure base url not HTTPS.';

            Mage::log($message);
            $adminSession->addError($message);

            $abort = true;
        }

        if ($abort) {
            return;
        }
        die('not aborted');

        /* @var $request Mage_Core_Controller_Request_Http */
        $request = $controllerAction->getRequest();

        $response = $controllerAction->getResponse();

        // check wether we use STS and if we are on https
        if ($useSts && !is_null($request->getServer('HTTPS')) && $request->getServer('HTTPS') != 'off') {
            // we are on https and use STS... send the header
            $maxAge = Mage::getStoreConfig(self::CONFIG_XML_MAX_AGE);
            $header = "Strict-Transport-Security: max-age=$maxAge";
            if (Mage::getStoreConfig(self::CONFIG_XML_INCLUDE_SUBDOMAINS)) {
                $header .= ' ; includeSubDomains';
            }
            $response->setRawHeader($header);
        } else {
            $response->setRedirect(Mage::getUrl('', array('_forced_secure' => true)));
        }
    }
}