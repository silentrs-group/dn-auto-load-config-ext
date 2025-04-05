# dn-auto-load-config-ext


### Зачем это?
Для автоматического сохранения данных в конфиге для приложения.
Но изначально была задача разобраться как добавлять свои поведения

### Как пользоваться
1. Качаем пакет из папочки bundle
2. Устанавливаем
3. Перезапускаем студию
4. Выбираем форму, выбираем вкладку "Поведения"
5. Во вкладке "Логика" находим "Сохранение \ загрузка конфига"
6. Пользуемся

Задаем данные в конфиг
```php
$this->getConfig()->set("key", "value");
```

Получаем данные из конфига
```php
var_dump($this->getConfig()->get("key"));
```

### Модифицируем данные перед сохранением и загрузкой
создаем класс и реализуем интерфейс `DataModify` 
```php
class ConfigModify implements DataModify
{
    public function onRead ($data)
    {
        // данные приходят в виде строки
        
        
        // тут делаем все что нам надо до применения данных к форме
        return $data;
    }
    
    public function onWrite ($data)
    {
        // тут делаем все что нам надо перед записью файла
        return $data;
    }
}
```
В поведении выбирем наш класс `ConfigModify` если его там нет, то првоерьте количество пробелов между implements и DataModify мне впадлу делать черзе регулярки и там сильно зависимый участок от регистра, преносов и прочих всяких модификаций

