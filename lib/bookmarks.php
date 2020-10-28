<?php
/**
 * Created by PhpStorm.
 * User: MaxTsykarev
 * Date: 27.10.2020
 * Time: 13:27
 */

namespace Libringer\Bookmarks;

use Bitrix\Main\Entity;
use Bitrix\Main\Type;

class BookmarksTable extends Entity\DataManager
{
	public static function getUfId()
	{
		return 'LIBRINGER_BOOKMARKS';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true
			)),
			new Entity\DateField('DATE', array(
				'required' => true
			)),
			new Entity\IntegerField('FAVICON'),
			new Entity\StringField('URL', array(
				'required' => true
			)),
			new Entity\StringField('TITLE', array(
				'required' => true
			)),
			new Entity\StringField('DESCRIPTION', array(
				'column_name' => 'META_DESCRIPTION'
			)),
			new Entity\StringField('KEYWORDS', array(
				'column_name' => 'META_KEYWORDS'
			)),
			new Entity\StringField('PASSWORD', array(
				'required' => 'META_KEYWORDS'
			))
		);
	}

	public static function onBeforeDelete(Entity\Event $event)
	{
		$primary = $event->getParameter("primary");
		$rs = static::GetByID($primary["ID"]);
		if ($ar = $rs->Fetch()) {
			if (intval($ar['FAVICON']) > 0) {
				\CFile::Delete($ar['FAVICON']);
			}
		}
	}
}