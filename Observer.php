class MyCompany_MyModule_Model_Observer
{
    public function redirectBasedOnCountry(Varien_Event_Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $store = $observer->getEvent()->getStore();
        $storeId = $store->getId();

        // check if the visitor's country has been detected
        $countryCode = Mage::getSingleton('core/session')->getGeoipCountryCode();
        if ($countryCode) {
            // redirect to the appropriate store view for the visitor's country
            $storeCode = "store_view_code_for_{$countryCode}";
            $store->setCode($storeCode);
            $url = $store->getBaseUrl();
            Mage::app()->getResponse()->setRedirect($url)->sendResponse();
            exit;
        }

        // if the visitor's country hasn't been detected, then use the http_accept_language header
        // to determine their preferred language and redirect them to the appropriate store view
        $localeCode = Mage::getStoreConfig('general/locale/code', $storeId);
        $httpAcceptLanguage = $request->getServer('HTTP_ACCEPT_LANGUAGE');
        $preferredLanguage = substr($httpAcceptLanguage, 0, 2);
        if ($preferredLanguage && $preferredLanguage != $localeCode) {
            // redirect to the appropriate store view for the visitor's preferred language
            $storeCode = "store_view_code_for_{$preferredLanguage}";
            $store->setCode($storeCode);
            $url = $store->getBaseUrl();
            Mage::app()->getResponse()->setRedirect($url)->sendResponse();
            exit;
        }
    }
}
