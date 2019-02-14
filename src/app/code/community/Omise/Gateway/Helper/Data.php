<?php
class Omise_Gateway_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function formatPrice($currency, $amount) {

        switch (strtoupper($currency)) {
            case 'THB':
                $amount = "฿" . number_format(($amount / 100), 2);
                if (preg_match('/\.00$/', $amount)) {
                    $amount = substr($amount, 0, -3);
                }
                break;

            case 'SGD':
                $amount = "S$" . number_format(($amount / 100), 2);
                if (preg_match('/\.00$/', $amount)) {
                    $amount = substr($amount, 0, -3);
                }
                break;

            case 'JPY':
                $amount = number_format($amount) . "円";
                break;
        }

        return $amount;

    }

    public function internetBankingName($code) {
        $banks = Mage::getSingleton('omise_gateway/config')->getInternetBankingBanks();
        return $this->__(array_key_exists($code, $banks) ? $banks[$code] : 'Unknown bank');
    }


}