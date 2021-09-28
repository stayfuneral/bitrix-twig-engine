<?php

namespace StayFuneral\BitrixTwig\Events;

class TwigEvents
{
    public static function OnPageStart()
    {
        global $arCustomTemplateEngines;
        $arCustomTemplateEngines['twig'] = [
            'templateExt' => ['twig', 'html.twig'],
            'function' => 'renderTwigTemplate'
        ];
    }
}