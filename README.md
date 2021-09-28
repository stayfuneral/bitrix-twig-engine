# Bitrix Twig Engine

Пакет для возможности использования Twig в компонентах битрикс.

## Установка

Composer:

`composer require stayfuneral/bitrix-twig`

После установки в командной PHP-строке выполните следующий код:

```php
$db = Bitrix\Main\Application::getConnection();
$entity = StayFuneral\BitrixTwig\Entites\TwigSubscribersTable::getEntity();

if(!$db->isTableExists($entity->getDBTableName())) {
    $entity->createDbTable();
}
```

Далее в `init.php` добавьте следующий код:

```php
require_once '/path/to/vendor/autoload.php';

StayFuneral\BitrixTwig\TemplateEngine::register();
```

## Использование

В шаблонах компонентов используйте расширение `twig` вместо `php`, например, `template.twig`.

### Языковые файлы

Языковые файлы должны иметь такое же название и расширение, что и файлы шаблонов, например `template.twig`, при этом оставаясь php-файлами (согласен, очень странно).

Т.е. содержимое файла должно выглядеть примерно так:

```php
// /local/components/.../templates/.default/lang/ru/template.twig

<?php

$MESS['SOME_TEXT'] = 'Какой-то текст...';
```

### Доступные расширения

Пока доступны 2 расширения:

* `getMessage(message, replaceArray)` - реализация метода 
```php
Bitrix\Main\Localization\Loc::getMessage($message, $replaceArray)
```
где массив `$replaceArray` - необязательный параметр.
* `showComponent(name)` - вызов компонента. Помимо названия, можно передавать следующие необязательные параметры:
```php
/**
* @param string $template шаблон компонента
 * @param array $params параметры компонента
 * @param CBitrixComponent $parentComponent
 * @param array $functionParams
 * @param $returnResult
 */

```
### Добавление своих расширений

Добавить своё расширение можно через прослушивание события `twig.before_render` (используется компонент EventDispatcher от Symfony).

Для этого создайте слушателя события, реализующего интерфейс Symfony\Component\EventDispatcher\EventSubscriberInterface:

```php
namespace StayFuneral\Event;

use StayFuneral\BitrixTwig\Events\TwigRenderEvent;
use StayFuneral\Extensions\CustomExtension;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TwigRenderSubscriber implements EventSubscriberInterface
{

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            TwigRenderEvent::EVENT_NAME => 'onBeforeRenderTwig'
        ];
    }

    /*
     * Функция-обработчик события
     */
    public function onBeforeRenderTwig(TwigRenderEvent $event)
    {
        // добавьте ваше расширение
        $event->getTwig()->addExtension(new CustomExtension());
    }
}
```

После этого добавьте данный класс в созданную ранее таблицу в БД:

```php
use StayFuneral\BitrixTwig\Entites\TwigSubscribersTable;
use StayFuneral\Event\TwigRenderSubscriber;

TwigSubscribersTable::addSubscriber(TwigRenderSubscriber::class);
```

Теперь каждый раз при отрисовке шаблона диспетчер событий будет добавлять ваше расширение в твиг.

