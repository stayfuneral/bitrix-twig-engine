<?php

use StayFuneral\BitrixTwig\Template\Engine;
use Symfony\Component\EventDispatcher\EventDispatcher;

if(!function_exists('renderTwigTemplate')) {

    function renderTwigTemplate($templateFile, $arResult, $arParams, $arLangMessages, $templateFolder, $parentTemplateFolder, CBitrixComponentTemplate $template)
    {
        $dispatcher = new EventDispatcher();

        $engine = new Engine($dispatcher);
        $engine->addComponentEpilog($templateFolder, $template);

        echo $engine->render($templateFile, $arResult, $arParams, $arLangMessages, $templateFolder, $parentTemplateFolder, $template);
    }

}