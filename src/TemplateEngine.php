<?php

namespace StayFuneral\BitrixTwig;

use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\EventManager;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\IO\File;
use StayFuneral\BitrixTwig\Entites\TwigSubscribersTable;
use StayFuneral\BitrixTwig\Events\TwigEvents;
use StayFuneral\BitrixTwig\Events\TwigRenderEvent;
use StayFuneral\BitrixTwig\Extensions\DefaultExtension;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use CBitrixComponent;
use CBitrixComponentTemplate;

/**
 * @property Environment $twig
 */
class TemplateEngine
{
    public const TWIG_CACHE_FOLDER = '/bitrix/cache/twig';
    public const TWIG_DEBUG = true;

    protected FilesystemLoader $loader;
    protected HttpRequest $request;
    protected EventDispatcher $dispatcher;

    public static function register()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->addEventHandler('main', 'OnPageStart', [TwigEvents::class, 'OnPageStart']);
    }

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->setRequest();
        $this->setLoader();
        $this->setTwig();

        $this->setDispatcher($dispatcher);
        $this->dispatchEvents();
    }

    protected function setRequest(): void
    {
        $this->request = Context::getCurrent()->getRequest();
    }

    protected function setLoader(): void
    {
        $this->loader = new FilesystemLoader(Application::getDocumentRoot());
    }

    protected function setTwig(): void
    {
        $this->twig = new Environment($this->loader, $this->getEnvOptions());
    }

    /**
     * @param EventDispatcher $dispatcher
     */
    protected function setDispatcher(EventDispatcher $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    protected function addSubscribers(): void
    {
        $subscibers = TwigSubscribersTable::getSubscribers();

        foreach ($subscibers as $subscriber) {
            $this->dispatcher->addSubscriber($subscriber);
        }
    }

    protected function dispatchEvents()
    {
        $this->addSubscribers();

        $event = new TwigRenderEvent($this->twig);
        $this->dispatcher->dispatch($event, TwigRenderEvent::EVENT_NAME);
    }

    protected function getEnvOptions(): array
    {
        return [
            'cache' => Application::getDocumentRoot() . self::TWIG_CACHE_FOLDER, // если передавать относительный путь - будет ошибка!!!
            'debug' => static::TWIG_DEBUG,
            'auto_reload' => $this->canAutoReload()
        ];
    }

    protected function canAutoReload(): bool
    {
        return isset($this->request['clear_cache']) && strtoupper($this->request['clear_cache']) === 'Y';
    }

    protected function getRenderOptions($arResult, $arParams, $arLangMessages, CBitrixComponentTemplate $template, $templateFolder, $parentTemplateFolder)
    {
        return [
            'arResult' => $arResult,
            'arParams' => $arParams,
            'arLangMessages' => $arLangMessages,
            'template' => $template,
            'templateFolder' => $templateFolder,
            'parentTemplateFolder' => $parentTemplateFolder
        ];
    }

    public function addComponentEpilog($templateFolder, CBitrixComponentTemplate $template)
    {
        $componentEpilogue = $templateFolder . '/component_epilog.php';

        if(File::isFileExists(Application::getDocumentRoot() . $componentEpilogue)) {
            $component = $template->getComponent();

            if($component instanceof CBitrixComponent) {

                $componentEpilogueInfo = $this->getComponentEpilogInfo($componentEpilogue, $template);
                $component->setTemplateEpilog($componentEpilogueInfo);
            }
        }
    }

    protected function getComponentEpilogInfo($epilogFile, CBitrixComponentTemplate $template, $templateData = false): array
    {
        return [
            'epilogFile' => $epilogFile,
            'templateName' => $template->__name,
            'templateFile' => $template->__file,
            'templateFolder' => $template->__folder,
            'templateData' => $templateData
        ];
    }
    /**
     * @param $templateFile
     * @param $arResult
     * @param $arParams
     * @param $arLangMessages
     * @param $templateFolder
     * @param $parentTemplateFolder
     * @param CBitrixComponentTemplate $template
     *
     * @return string
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render($templateFile, $arResult, $arParams, $arLangMessages, $templateFolder, $parentTemplateFolder, CBitrixComponentTemplate $template): string
    {
        $this->twig->addExtension(new DefaultExtension());

        $renderOptions = $this->getRenderOptions($arResult, $arParams, $arLangMessages, $template, $templateFolder, $parentTemplateFolder);

        return $this->twig->render($templateFile, $renderOptions);
    }


}