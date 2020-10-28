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
<?php if ($arResult): ?>
    <p>ID: <?php echo $arResult["ID"]; ?></p>
    <p>TITLE: <?php echo $arResult["TITLE"]; ?></p>
    <p>DATE: <?php echo $arResult["DATE"]; ?></p>
	<?php if ($arResult["DESCRIPTION"]): ?>
        <p>DESCRIPTION: <?php echo $arResult["DESCRIPTION"]; ?></p>
	<?php endif; ?>
	<?php if ($arResult["KEYWORDS"]): ?>
        <p>KEYWORDS: <?php echo $arResult["KEYWORDS"]; ?></p>
	<?php endif; ?>
    <p><a href="#"
          class="js-open-delete"><?php echo GetMessage("LIBRINGER_BOOKMARKS_COMPONENT_DETAIL_OPEN_DELETE"); ?></a></p>
    <div class="js-form-delete" style="display: none;">
        <form name="bookmarks_remove" action="<?= POST_FORM_ACTION_URI ?>" method="post">
			<?php echo bitrix_sessid_post(); ?>
            <input type="password" name="password" value=""
                   placeholder="<?php echo GetMessage("LIBRINGER_COMPONENT_DETAIL_PASS_PLACEHOLDER"); ?>"/>
            <input type="hidden" name="id" value="<?php echo $arResult["ID"]; ?>"/>
            <button type="submit" name="send_bookmarks"
                    value="send"><?php echo GetMessage("LIBRINGER_COMPONENT_REMOVE_BUTTON"); ?></button>
            <div class="js-bookmarks-remove-status"></div>
        </form>
    </div>
<?php endif; ?>