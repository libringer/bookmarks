<?php
/**
 * Created by PhpStorm.
 * User: MaxTsykarev
 * Date: 28.10.2020
 * Time: 11:09
 */
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type;
use Libringer\Bookmarks\BookmarksTable;

class BookmarksListComponent extends CBitrixComponent
{

	/**
	 * проверяет подключение необходиимых модулей
	 * @throws LoaderException
	 */
	protected function checkModules()
	{
		if (!\Bitrix\Main\Loader::includeModule('libringer.bookmarks'))
			throw new \Bitrix\Main\LoaderException(Loc::getMessage('LIBRINGER_BOOKMARKS_MODULE_NOT_INSTALLED'));
	}

	/**
	 * sql элементов. кэширование выборки
	 * @param \Bitrix\Main\UI\PageNavigation $nav Объект пагинации
	 * @param array $arSort Сортировка
	 * @return object
	 */
	function get(\Bitrix\Main\UI\PageNavigation &$nav, array $arSort = [])
	{
		$result = BookmarksTable::getList(array(
			'select' => array('ID', 'DATE', 'FAVICON', 'TITLE'),
			'order' => $arSort,
			'count_total' => true,
			'offset' => $nav->getOffset(),
			'limit' => $nav->getLimit(),
			'cache' => array(
				'ttl' => 3600,
				'cache_joins' => true,
			)
		));

		$nav->setRecordCount($result->getCount());

		return $result;
	}


	/**
	 * выполняет логику работы компонента
	 */
	public function executeComponent()
	{
		$this->checkModules();

		$arAvailableSort = [
			"DATE_ASC" => [
				"TITLE" => "Дата добавления (по возрастанию)"
			],
			"DATE_DESC" => [
				"TITLE" => "Дата добавления (по убыванию)"
			],
			"TITLE_ASC" =>[
				"TITLE" => "Заголовок страницы (по возрастанию)"
			],
			"TITLE_DESC" => [
				"TITLE" => "Заголовок страницы (по убыванию)"
			],
			"URL_ASC" => [
				"TITLE" => "URL страницы (по возрастанию)"
			],
			"URL_DESC" => [
				"TITLE" => "URL страницы (по убыванию)"
			],
		];

		$context = \Bitrix\Main\Application::getInstance()->getContext();
		$request = $context->getRequest();
		if ($strSort = $request->getQuery('sort')) {
			$explodeSort = explode("_", $strSort);
			$arSort = [$explodeSort[0] => $explodeSort[1]];
			$arAvailableSort[$strSort]["SELECTED"] = true;
		} else {
			$arSort = ["DATE" => "DESC"];
			$arAvailableSort["DATE_DESC"]["SELECTED"] = true;
		}

		$obNav = new \Bitrix\Main\UI\PageNavigation("nav-bookmarks");
		$obNav->allowAllRecords(true)
			->setPageSize(5)
			->initFromUri();

		$result = $this->get($obNav, $arSort);

		$this->arResult['pagination'] = $obNav;
		$this->arResult['items'] = $result->fetchAll();
		$this->arResult['sort'] = $arAvailableSort;


		$this->includeComponentTemplate();
	}
}

;
