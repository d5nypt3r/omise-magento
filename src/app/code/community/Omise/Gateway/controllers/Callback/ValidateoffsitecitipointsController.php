<?php
class Omise_Gateway_Callback_ValidateoffsitecitipointsController extends Omise_Gateway_Controller_Base
{
    const PAYMENT_TITLE = 'Citi Reward Points';
    /**
     * @return Mage_Core_Controller_Varien_Action|void
     * @throws Mage_Core_Exception
     */
    public function indexAction()
    {
        return $this->validate();
    }
}
