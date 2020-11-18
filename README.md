# menu_part_mixed
Компонент для вывода смешанных пунктов меню

### Установка компонента
Положить /menu_part_mixed в каталог /local/components/fatnev/

### Вызов компонента
Добавить файл с подключением, например, .left.menu_ext.php.

Вызов:

```php
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
global $APPLICATION;
?>

<?$aMenuLinksExt = $APPLICATION->IncludeComponent(
	"fatnev:menu_part_mixed",       // Компонент 
	"",
	array(
		"IBLOCK_TYPE" => "trash",     // Тип инфоблока 
		"IBLOCK_ID" => "110",         // ID инфоблока
		"DEPTH_LEVEL" => "4",
		"SHOW_SECTIONS" => "Y",
		"SHOW_ELEMENTS" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => 36000000
	),
	false
);?>

<?
$aMenuLinks = array_merge($aMenuLinksExt, $aMenuLinks);
?>

```
