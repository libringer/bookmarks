#Модуль закладок

###Пример добавления на страницу 

```php
<?php
$APPLICATION->IncludeComponent(
	"libringer:blank.route", "", array(
	"SEF_FOLDER" => "/bookmarkers/",
	"SEF_MODE" => "Y",
	"SET_STATUS_404" => "Y",
	"SEF_URL_TEMPLATES" => array(
		"default" => "",
		"add" => "form/",
		"detail" => "#id#/"
	)
), false, array(
		"HIDE_ICONS" => "Y"
	)
);
?>
```

Для корректной работы ЧПУ в urlrewrite.php должно быть правило
```php
array(
		'CONDITION' => '#^/bookmarkers/#',
		'RULE' => '',
		'ID' => '',
		'PATH' => '/bookmarkers/index.php',
		'SORT' => 100,
)
```

При таком вызове компонента url страниц: 

 - /bookmarkers/ - Список закладок
 - /bookmarkers/form/ - Страница с формой добавения
 - /bookmarkers/#id#/ - Страница с детальной информацией о закладке, где #id# закладки

