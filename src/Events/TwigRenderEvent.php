<?php

namespace StayFuneral\BitrixTwig\Events;

use Symfony\Contracts\EventDispatcher\Event;
use Twig\Environment;

class TwigRenderEvent extends Event
{
    public const EVENT_NAME = 'twig.before_render';

    protected Environment $twig;

    public function __construct(Environment &$twig)
    {
        $this->twig = $twig;
    }

    /**
     * @return Environment
     */
    public function getTwig(): Environment
    {
        return $this->twig;
    }
}