<?php

use StayFuneral\BitrixTwig\TemplateEngine;
use Symfony\Component\EventDispatcher\EventDispatcher;

if(!function_exists('renderTwigTemplate')) {

    function renderTwigTemplate($templateFile, $arResult, $arParams, $arLangMessages, $templateFolder, $parentTemplateFolder, CBitrixComponentTemplate $template)
    {
        $dispatcher = new EventDispatcher();

        $engine = new TemplateEngine($dispatcher);
        $engine->addComponentEpilog($templateFolder, $template);

        echo $engine->render($templateFile, $arResult, $arParams, $arLangMessages, $templateFolder, $parentTemplateFolder, $template);
    }

}