<?php

namespace StayFuneral\BitrixTwig\Extensions;

use Bitrix\Main\Localization\Loc;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use CBitrixComponent;
use CMain;

class DefaultExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('getMessage', 'Bitrix\Main\Localization\Loc::getMessage'),
            new TwigFunction('showComponent', [$this, 'showComponent'])
        ];
    }

    public function showComponent($name, $template = '', $params = [], CBitrixComponent $parentComponent = null, $functionParams = [], $returnResult = false)
    {
        $app = new CMain();
        $app->IncludeComponent($name, $template, $params, $parentComponent, $functionParams, $returnResult);
    }
}