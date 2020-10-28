<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<form name="bookmarks_add" action="<?= POST_FORM_ACTION_URI ?>" method="post">
	<?php echo bitrix_sessid_post(); ?>
    <input type="text" name="url" value=""
           placeholder="<?php echo GetMessage("LIBRINGER_COMPONENT_ADD_PLACEHOLDER"); ?>"/>
    <input type="password" name="password" value=""
           placeholder="<?php echo GetMessage("LIBRINGER_COMPONENT_PASS_PLACEHOLDER"); ?>"/>
    <button type="submit" name="send_bookmarks"
            value="send"><?php echo GetMessage("LIBRINGER_COMPONENT_ADD_BUTTON"); ?></button>
    <div class="js-bookmarks-add-status"></div>
</form>

<a href="<?php echo $arParams["SEF_FOLDER"];?>"><?php echo GetMessage("LIBRINGER_COMPONENT_ADD_LIST_LINK"); ?></a>