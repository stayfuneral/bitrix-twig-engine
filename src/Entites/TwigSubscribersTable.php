<?php

namespace StayFuneral\BitrixTwig\Entites;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\SystemException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TwigSubscribersTable extends DataManager
{
    public static function getTableName()
    {
        return 'twig_subscribers';
    }

    public static function getMap()
    {
        return [
            (new StringField('CLASS_NAME'))
                ->configurePrimary()
                ->configureRequired()
                ->configureUnique()
        ];
    }

    /**
     * Добавление подписчика в БД
     *
     * @param $className класс подписчика
     *
     * @return array|int
     *
     * @throws \Exception
     */
    public static function addSubscriber($className)
    {
        $add = static::add(['CLASS_NAME' => $className]);
        return $add->isSuccess() ? $add->getId() : $add->getErrorMessages();
    }

    /**
     * Получение всех подписчиков из БД
     *
     * @return EventSubscriberInterface[]
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getSubscribers(): array
    {
        $results = [];
        $subscribers = self::getList(['select' => ['CLASS_NAME']])->fetchAll();

        foreach ($subscribers as $subscriber) {

            $subscriberObject = new $subscriber['CLASS_NAME'];

            if(!($subscriberObject instanceof EventSubscriberInterface)) {
                $message = sprintf('Class %s is not implements %s', $subscriber['CLASS_NAME'], EventSubscriberInterface::class);
                throw new SystemException($message);
            }

            $results[] = $subscriberObject;
        }

        return $results;
    }
}