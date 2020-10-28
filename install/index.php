<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class libringer_bookmarks extends CModule
{

	var $exclusionAdminFiles;

	public function __construct()
	{
		if (is_file(__DIR__ . '/version.php')) {
			include_once(__DIR__ . '/version.php');
			$this->MODULE_ID = str_replace("_", ".", get_class($this));;
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
			$this->MODULE_NAME = Loc::getMessage('LIBRINGER_BOOKMARKS_NAME');
			$this->MODULE_DESCRIPTION =
				Loc::getMessage('LIBRINGER_BOOKMARKS_DESCRIPTION');
			$this->PARTNER_NAME = Loc::getMessage("LIBRINGER_BOOKMARKS_PARTNER_NAME");
		} else {
			CAdminMessage::showMessage(
				Loc::getMessage('LIBRINGER_BOOKMARKS_FILE_NOT_FOUND') . ' version.php'
			);
		}
	}

	public function DoInstall()
	{

		global $APPLICATION;

		if ($this->isVersionD7()) {

			\Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
			$this->InstallDB();
			$this->InstallFiles();


		} else {

			$APPLICATION->ThrowException(Loc::getMessage('LIBRINGER_BOOKMARKS_INSTALL_ERROR'));

		}

		$APPLICATION->IncludeAdminFile(
			Loc::getMessage('LIBRINGER_BOOKMARKS_INSTALL_TITLE') . ' «' . Loc::getMessage('LIBRINGER_BOOKMARKS_NAME') . '»',
			__DIR__ . '/step.php'
		);
	}

	public function GetPath($notDocumentRoot = false)
	{
		if ($notDocumentRoot) {
			return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
		} else {
			return dirname(__DIR__);
		}

	}

	public function isVersionD7()
	{
		return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
	}

	public function InstallDB()
	{
		Loader::includeModule($this->MODULE_ID);

		if (!Application::getConnection(\Libringer\Bookmarks\BookmarksTable::getConnectionName())->isTableExists(
			Base::getInstance('\Libringer\Bookmarks\BookmarksTable')->getDbTableName()
		)
		) {
			Base::getInstance('\Libringer\Bookmarks\BookmarksTable')->createDbTable();
		}
	}

	function InstallFiles($arParams = array())
	{
		$path = $this->GetPath() . "/install/components";

		if (\Bitrix\Main\IO\Directory::isDirectoryExists($path)) {
			CopyDirFiles($path, $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);
		} else {
			throw new \Bitrix\Main\IO\InvalidPathException($path);
		}

		if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/admin')) {
			CopyDirFiles($this->GetPath() . "/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin"); //если есть файлы для копирования
			if ($dir = opendir($path)) {
				while (false !== $item = readdir($dir)) {
					if (in_array($item, $this->exclusionAdminFiles))
						continue;
					file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item,
						'<' . '? require($_SERVER["DOCUMENT_ROOT"]."' . $this->GetPath(true) . '/admin/' . $item . '");?' . '>');
				}
				closedir($dir);
			}
		}

		return true;
	}

	function UnInstallFiles()
	{
		\Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"] . '/bitrix/components/libringer/');

		if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/admin')) {
			DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . $this->GetPath() . '/install/admin/', $_SERVER["DOCUMENT_ROOT"] . '/bitrix/admin');
			if ($dir = opendir($path)) {
				while (false !== $item = readdir($dir)) {
					if (in_array($item, $this->exclusionAdminFiles))
						continue;
					\Bitrix\Main\IO\File::deleteFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item);
				}
				closedir($dir);
			}
		}
		return true;
	}


	public function DoUninstall()
	{

		global $APPLICATION;

		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();

		$this->UnInstallFiles();
		if ($request["savedata"] != "Y")
			$this->UnInstallDB();

		\Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);

		$APPLICATION->includeAdminFile(
			Loc::getMessage('LIBRINGER_BOOKMARKS_UNINSTALL_TITLE') . ' «' . Loc::getMessage('LIBRINGER_BOOKMARKS_NAME') . '»',
			__DIR__ . '/unstep.php'
		);

	}

	public function UninstallDB()
	{
		Loader::includeModule($this->MODULE_ID);
		Application::getConnection(\Libringer\Bookmarks\BookmarksTable::getConnectionName())->queryExecute(
			'DROP TABLE IF EXISTS ' . Base::getInstance('\Libringer\Bookmarks\BookmarksTable')->getDBTableName()
		);

		Option::delete($this->MODULE_ID);
	}

}