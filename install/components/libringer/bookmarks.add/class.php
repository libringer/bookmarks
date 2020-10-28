<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Libringer\Bookmarks\BookmarksTable;

class BookmarksAddComponent extends CBitrixComponent
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
	 * парсинг favicon
	 * @param string $html HTML строка
	 * @return string
	 */
	protected function parseFavicon($html)
	{
		$favicon = '';
		$pattern = '/((<link[^>]+rel=.(icon|shortcut icon|alternate icon)[^>]+>))/i';
		if (@preg_match($pattern, $html, $matchTag)) {
			$pattern = '/href=(\'|\")(.*?)\1/i';
			if (isset($matchTag[1]) and @preg_match($pattern, $matchTag[1], $matchUrl)) {
				if (isset($matchUrl[2])) {
					$favicon = trim($matchUrl[2]);
				}
			}
		}

		return $favicon;
	}

	/**
	 * парсинг meta
	 * @param string $html HTML строка
	 * @return array
	 */
	protected function parseMeta($html)
	{
		$arMeta = [];
		$requiredMeta = ['title', 'description', 'keywords'];
		foreach ($requiredMeta as $nameMeta) {
			switch ($nameMeta) {
				case 'title':
					if (preg_match('|<title.*?>(.*)</title>|si', $html, $match)) {
						$arMeta[$nameMeta] = $match[1];
					}
					break;
				default:
					if (preg_match('~<meta[ \t]*http\-equiv=["\']' . str_replace('-', '\-', $nameMeta) .
						'["\'][ \t]*content=["\'](.*?)["\'].*?>~si', $html, $match)) {
						$arMeta[$nameMeta] = $match[1];
					} elseif (preg_match('~<meta[ \t]*name=["\']' . str_replace('-', '\-', $nameMeta) .
						'["\'][ \t]*content=["\'](.*?)["\'].*?>~si', $html, $match)) {
						$arMeta[$nameMeta] = $match[1];
					}

					break;
			}
		}
		return $arMeta;
	}


	/**
	 * выполняет логику работы компонента
	 */
	public function executeComponent()
	{
		global $APPLICATION;
		$this->checkModules();

		$context = \Bitrix\Main\Application::getInstance()->getContext();
		$request = $context->getRequest();
		$this->arResult["status"] = false;
		$this->arResult["errors"] = array();

		if ($request->get("send_bookmarks") == "send") {
			$this->arResult["data"] = array(
				'url' => htmlspecialchars($request->get("url")),
				'password' => htmlspecialchars($request->get("password"))
			);
			if (!strlen($this->arResult["data"]['url']) ||
				!strlen($this->arResult["data"]['password'])
			) {
				$this->arResult["error"] = true;
				if(!strlen($this->arResult["data"]['url'])) {
					$this->arResult['msg'][] = Loc::getMessage('LIBRINGER_BOOKMARKS_MODULE_NOT_URL');
				}

				if(!strlen($this->arResult["data"]['password'])) {
					$this->arResult['msg'][] = Loc::getMessage('LIBRINGER_BOOKMARKS_MODULE_NOT_PASS');
				}

			} else {
				$url = $this->arResult["data"]["url"];
				$password = md5($this->arResult["data"]["password"]);
				$resMarks = BookmarksTable::getList(array(
					'select' => array('ID'),
					'filter' => array('URL' => $url),
					'order' => array('ID' => 'DESC'),
					'limit' => 1,
				))->fetch();

				if (!isset($resMarks["ID"])) {
					$domain = parse_url($url, PHP_URL_HOST);
					$scheme = parse_url($url, PHP_URL_SCHEME) ?? 'http';
					$grab = new \Libringer\Bookmarks\Grab();
					$html = $grab->load($url);

					$this->arResult['favicon_link'] = $scheme . '://' . $domain . $this->parseFavicon($html);
					if ($this->arResult['favicon_link']) {
						$arFavicon = \CFile::MakeFileArray($this->arResult['favicon_link']);
						$arFavicon["del"] = "URL_del";
						$arFavicon["MODULE_ID"] = "bookmarks";
						$this->arResult['favicon'] = \CFile::SaveFile($arFavicon, "bookmarks");
					}
					$this->arResult['meta'] = $this->parseMeta($html);

					if ($this->arResult['meta'] && $this->arResult['meta']['title']) {
						$bookmark = BookmarksTable::add(array(
							'TITLE' => $this->arResult['meta']['title'],
							'URL' => $url,
							'FAVICON' => $this->arResult['favicon'],
							'DATE' => new \Bitrix\Main\Type\DateTime(),
							'DESCRIPTION' => $this->arResult['meta']['description'] ?? '',
							'KEYWORDS' => $this->arResult['meta']['keywords'] ?? '',
							'PASSWORD' => $password
						));

						//очистим кэш
						BookmarksTable::getEntity()->cleanCache();

						if ($bookmark->isSuccess()) {
							$this->arResult['status'] = true;
							$id = $bookmark->getId();
							$this->arResult['href'] = $this->arParams['SEF_FOLDER'] . $id . '/';
						} else {
							$this->arResult['status'] = false;
							$error = $bookmark->getErrorMessages();
							$this->arResult['msg'][] = Loc::getMessage('LIBRINGER_BOOKMARKS_MODULE_ADD_ERROR') . ' <pre>' . var_export($error, true) . '</pre>';
						}
					}
				} else {
					$this->arResult['status'] = false;
					$this->arResult['msg'][] = Loc::getMessage('LIBRINGER_BOOKMARKS_MODULE_URL_EXIST');
				}

			}

			$APPLICATION->RestartBuffer();
			echo json_encode($this->arResult);
			require_once($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_after.php");
			die();
		}

		$this->IncludeComponentTemplate();
	}
}