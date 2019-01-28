<?php

namespace SpShareBasket;

use Doctrine\ORM\Tools\SchemaTool;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use SpShareBasket\Models\Baskets;

class SpShareBasket extends Plugin
{
    /**
     * @param InstallContext $context
     */
    public function install(InstallContext $context)
    {
        $this->installSchema();
    }

    /**
     * @param UpdateContext $context
     */
    public function update(UpdateContext $context)
    {
        parent::update($context);
        $this->installSchema();
    }

    /**
     * @param UninstallContext $context
     */
    public function uninstall(UninstallContext $context)
    {
        parent::uninstall($context);
        $this->uninstallSchema();
    }

    public function activate(ActivateContext $activateContext)
    {
        // on plugin activation clear the cache
        $activateContext->scheduleClearCache(ActivateContext::CACHE_LIST_ALL);
    }

    public function deactivate(DeactivateContext $deactivateContext)
    {
        // on plugin deactivation clear the cache
        $deactivateContext->scheduleClearCache(DeactivateContext::CACHE_LIST_ALL);
    }

    public static function getSubscribedEvents()
    {
        return [
            'Shopware_CronJob_ShareBasketCleanup' => 'cleanup',
        ];
    }

    /**
     * @param \Shopware_Components_Cron_CronJob $job
     *
     * @return string
     */
    public function cleanup(\Shopware_Components_Cron_CronJob $job)
    {
        $config = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName($this->getName());

        $where = 'time < DATE_SUB(NOW(), INTERVAL ' . $config['cleanup'] . ' MONTH)';

        $result =$this->container->get('db')->delete('s_plugin_sharebasket_baskets', $where);

        return 'Gelöscht: ' . $result;
    }

    /**
     * Install or update s_plugin_sharebasket_baskets table
     */
    private function installSchema()
    {
        $tool = new SchemaTool($this->container->get('models'));

        $tool->updateSchema([$this->container->get('models')->getClassMetadata(Baskets::class)], true);
    }

    /**
     * Remove s_plugin_sharebasket_baskets table
     */
    private function uninstallSchema()
    {
        $tool = new SchemaTool($this->container->get('models'));

        $tool->dropSchema([$this->container->get('models')->getClassMetadata(Baskets::class)]);
    }
}
