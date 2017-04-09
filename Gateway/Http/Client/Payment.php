<?php
namespace Omise\Payment\Gateway\Http\Client;

use Exception;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Omise\Payment\Model\Config\Config;

class Payment implements ClientInterface
{
    /**
     * Client request status represented to successful request step.
     *
     * @var string
     */
    const PROCESS_STATUS_SUCCESSFUL = 'successful';

    /**
     * Client request status represented to failed request step.
     *
     * @var string
     */
    const PROCESS_STATUS_FAILED = 'failed';

    /**
     * @var Omise\Payment\Model\Config\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    public function __construct(
        Config                   $config,
        ModuleListInterface      $moduleList,
        ProductMetadataInterface $productMetadata
    ) {
        $this->config          = $config;
        $this->moduleList      = $moduleList;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param  string $public_key
     * @param  string $secret_key
     *
     * @return void
     */
    protected function defineApiKeys($public_key = '', $secret_key = '')
    {
        if (! defined('OMISE_PUBLIC_KEY')) {
            define('OMISE_PUBLIC_KEY', $public_key ? $public_key : $this->config->getPublicKey());
        }

        if (! defined('OMISE_SECRET_KEY')) {
            define('OMISE_SECRET_KEY', $secret_key ? $secret_key : $this->config->getSecretKey());
        }
    }

    /**
     * Define configuration constant for Omise PHP library
     *
     * @return void
     */
    protected function defineUserAgent()
    {
        if (! defined('OMISE_USER_AGENT_SUFFIX')) {
            define(
                'OMISE_USER_AGENT_SUFFIX',
                sprintf(
                    'OmiseMagento/%s Magento/%s',
                    $this->getModuleVersion(),
                    $this->getMagentoVersion()
                )
            );
        }
    }

    /**
     * Retrieve Magento's current version
     *
     * @return string
     */
    protected function getMagentoVersion()
    {
        return $this->productMetadata->getVersion();
    }

    /**
     * Retrieve Omise module's current version
     *
     * @return string
     */
    protected function getModuleVersion()
    {
        return $this->moduleList->getOne(Config::MODULE_NAME)['setup_version'];
    }

    /**
     * @param  \Magento\Payment\Gateway\Http\TransferInterface $transferObject
     *
     * @return array
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $this->defineUserAgent();
        $this->defineApiKeys();

        try {
            $request  = \OmiseCharge::create($transferObject->getBody());

            $response = [
                'object'  => 'omise',
                'status'  => self::PROCESS_STATUS_SUCCESSFUL,
                'data'    => $request,
                'message' => null,
            ];
        } catch (Exception $e) {
            $response = [
                'object'  => 'omise',
                'status'  => self::PROCESS_STATUS_FAILED,
                'data'    => null,
                'message' => $e->getMessage(),
            ];
        }

        return $response;
    }
}