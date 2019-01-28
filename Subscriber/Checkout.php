<?php

namespace SpShareBasket\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\Components\DependencyInjection\Container as DIContainer;
use Shopware\Components\Plugin\CachedConfigReader;

class Checkout implements SubscriberInterface
{
    /**
     * @var DIContainer
     */
    private $container;

    /**
     * @var string
     */
    private $pluginName;

    /**
     * @var CachedConfigReader
     */
    private $config;

    /**
     * Checkout constructor.
     *
     * @param DIContainer        $container
     * @param string             $pluginName
     * @param CachedConfigReader $config
     */
    public function __construct(DIContainer $container, string $pluginName, CachedConfigReader $config)
    {
        $this->container = $container;
        $this->pluginName = $pluginName;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Checkout' => 'onPostDispatchCheckout',
        ];
    }

    public function onPostDispatchCheckout(\Enlight_Event_EventArgs $args)
    {
        $config = $this->config->getByPluginName($this->pluginName, $this->container->get('shop'));

        /** @var \Shopware_Controllers_Frontend_Checkout $subject */
        $subject = $args->getSubject();
        $request = $subject->Request();
        $view = $subject->View();

        if ($request->has('bID')) {
            $basketID = $request->getParam('bID');

            $router = $this->container->get('router');
            $sBasketUrl = $router->assemble([
                'module' => 'frontend',
                'sViewport' => 'sharebasket',
                'action' => 'load',
                'bID' => $basketID,
            ]);

            $view->assign('spShareBasket', true);
            $view->assign('sBasketUrl', $sBasketUrl);
            $view->assign('spShareBasketEmail', $config['email']);
            $view->assign('spShareBasketFacebook', $config['facebook']);
            $view->assign('spShareBasketWhatsApp', $config['whatsapp']);
        }
    }
}
