<?php

namespace SpShareBasket\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\Theme\LessDefinition;

class TemplateRegistration implements SubscriberInterface
{
    /**
     * @var string
     */
    private $pluginDir;

    /**
     * Theme constructor.
     *
     * @param $pluginDir
     */
    public function __construct($pluginDir)
    {
        $this->pluginDir = $pluginDir;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Theme_Inheritance_Template_Directories_Collected' => 'onTemplateDirectoriesCollect',
            'Theme_Compiler_Collect_Plugin_Javascript' => 'onAddJavascriptFiles',
            'Theme_Compiler_Collect_Plugin_Less' => 'onAddLessFiles',
        ];
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     */
    public function onTemplateDirectoriesCollect(\Enlight_Event_EventArgs $args)
    {
        $dirs = $args->getReturn();
        $dirs[] = $this->pluginDir . '/Resources/views/';

        $args->setReturn($dirs);
    }

    /**
     * @return ArrayCollection
     */
    public function onAddLessFiles()
    {
        $less = new LessDefinition(
            [],
            [
                $this->pluginDir . '/Resources/views/frontend/_public/src/css/sharebasket.less',
            ],
            $this->pluginDir
        );

        return new ArrayCollection([$less]);
    }

    /**
     * @return ArrayCollection
     */
    public function onAddJavascriptFiles()
    {
        $jsFiles = [
            $this->pluginDir . '/vendor/clipboardjs/clipboard.min.js',
            $this->pluginDir . '/Resources/views/frontend/_public/src/js/jquery.sharebasket.js',
        ];

        return new ArrayCollection($jsFiles);
    }
}
