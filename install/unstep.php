<?php
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!check_bitrix_sessid()) {
	return;
}

if ($errorException = $APPLICATION->GetException()) {
	CAdminMessage::showMessage(array(
		"TYPE" => "ERROR",
		"MESSAGE" => Loc::getMessage('LIBRINGER_BOOKMARKS_UNINSTALL_FAILED'),
		"DETAILS" => $errorException->GetString(),
		"HTML" => true
	));
} else {
	CAdminMessage:showNote(
		Loc::getMessage('LIBRINGER_BOOKMARKS_UNINSTALL_SUCCESS')
	);
}
?>

<form action="<?= $APPLICATION->getCurPage(); ?>">
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID; ?>"/>
    <input type="submit" value="<?= Loc::getMessage('LIBRINGER_BOOKMARKS_RETURN_MODULES'); ?>">
</form>