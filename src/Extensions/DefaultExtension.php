<?php

namespace StayFuneral\BitrixTwig\Extensions;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Twig\Extension\AbstractExtension;
use CMain;
use Twig\TwigFunction;

Loc::loadMessages(__FILE__);

class DefaultExtension extends AbstractExtension
{

    protected $functions = ['showComponent'];

    public function getFunctions()
    {
        return [
            new TwigFunction('getMessage', 'Bitrix\Main\Localization\Loc::getMessage'),
            new TwigFunction('showComponent', [$this, 'showComponent']),
            new TwigFunction('dd', 'dd')
        ];
    }

    public function showComponent($name, $template = '', $params = [], \CBitrixComponent $parentComponent = null, $functionParams = [], $returnResult = false)
    {
        $app = new CMain();
        $app->IncludeComponent($name, $template, $params, $parentComponent, $functionParams, $returnResult);
    }

    public function getMessage($phrase, $search = null, $replace = null)
    {
        $messParams = [];
        if(!is_null($search) && !is_null($replace)) {
            $messParams[$search] = $replace;
        }

        return Loc::getMessage($phrase, $messParams);
    }

    public function loadMessages($file)
    {
        Loc::loadMessages($file);
    }
}