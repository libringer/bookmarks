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

class BookmarksDetailComponent extends CBitrixComponent
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
	 * проверяет наличие id
	 * @return boolean
	 */
	protected function checkId()
	{
		if (!$this->arParams['ID'] || !is_numeric($this->arParams['ID'])) {
			$this->set404();
		}

		return true;
	}

	/**
	 * установка 404
	 */
	protected function set404()
	{
		\Bitrix\Iblock\Component\Tools::process404(
			Loc::getMessage('LIBRINGER_BOOKMARKS_COMPONENT_DETAIL_NOT_FOUND'),
			true,
			true,
			true,
			false
		);
	}

	/**
	 * sql элементов
	 * @param integer $id ID элемента
	 * @return object
	 */
	function get($id)
	{
		$result = BookmarksTable::getList(array(
			'select' => array('ID', 'DATE', 'FAVICON', 'TITLE', 'DESCRIPTION', 'KEYWORDS'),
			'filter' => array('ID' => $id),
			'limit' => 1,
			'cache' => array(
				'ttl' => 3600,
				'cache_joins' => true,
			)
		));

		return $result;
	}


	public function executeComponent()
	{
		global $APPLICATION;
		$context = \Bitrix\Main\Application::getInstance()->getContext();
		$request = $context->getRequest();

		$this->checkModules();
		$this->checkId();

		$result = $this->get($this->arParams['ID']);

		$this->arResult = $result->fetch();

		if (!$this->arResult["ID"]) {
			$this->set404();
		}

		if ($request->get("send_bookmarks") == "send") {
			$this->arResult["data"] = array(
				'id' => htmlspecialchars($request->get("id")),
				'password' => htmlspecialchars($request->get("password"))
			);
			if (!strlen($this->arResult["data"]['id']) ||
				!strlen($this->arResult["data"]['password'])
			) {
				$this->arResult["error"] = true;
				if (!strlen($this->arResult["data"]['id'])) {
					$this->arResult['msg'][] = Loc::getMessage('LIBRINGER_BOOKMARKS_MODULE_NOT_ID');
				}

				if (!strlen($this->arResult["data"]['password'])) {
					$this->arResult['msg'][] = Loc::getMessage('LIBRINGER_BOOKMARKS_MODULE_NOT_PASS');
				}

			} else {
				$id = $this->arResult["data"]["id"];
				$password = md5($this->arResult["data"]["password"]);
				$resMarks = BookmarksTable::getList(array(
					'select' => array('ID'),
					'filter' => array('ID' => $id, 'PASSWORD' => $password),
					'order' => array('ID' => 'DESC'),
					'limit' => 1,
				))->fetch();

				if (isset($resMarks["ID"])) {
					$bookmark = BookmarksTable::delete($resMarks["ID"]);
					BookmarksTable::getEntity()->cleanCache();
					$this->arResult['status'] = true;
					$this->arResult['href'] = $this->arParams["SEF_FOLDER"];
				} else {
					$this->arResult['status'] = false;
					$this->arResult['msg'][] = Loc::getMessage('LIBRINGER_BOOKMARKS_MODULE_REMOVE_ERROR');
				}

			}

			$APPLICATION->RestartBuffer();
			echo json_encode($this->arResult);
			require_once($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_after.php");
			die();
		}


		$this->includeComponentTemplate();
	}

}

;
