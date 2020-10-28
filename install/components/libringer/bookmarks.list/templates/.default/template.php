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
<?php if ($arParams["URL_TEMPLATES"]["add"]): ?>
    <a href="<?php echo $arParams["SEF_FOLDER"]; ?><?php echo $arParams["URL_TEMPLATES"]["add"]; ?>">
		<?php echo GetMessage("LIBRINGER_BOOKMARKS_COMPONENT_LIST_LINK_ADD"); ?>
    </a>
<?php endif; ?>
<?php if ($arResult["items"]): ?>
    <form method="get" action="<?php echo $APPLICATION->GetCurPage(); ?>">
        Сортировка:
        <select name="sort" onchange="this.form.submit()">
			<?php foreach ($arResult["sort"] as $value => $sort): ?>
                <option value="<?php echo $value; ?>"<?php echo($sort["SELECTED"] ? ' selected' : ''); ?>>
					<?php echo $sort["TITLE"]; ?>
                </option>
			<?php endforeach; ?>
        </select>
    </form>

    <ul>
		<?php foreach ($arResult["items"] as $atItem): ?>
            <li>
                ID: <?php echo $atItem["ID"]; ?>
				<?php if ($atItem["FAVICON"]): ?>
                    <img src="<?php echo CFile::GetPath($atItem["FAVICON"]); ?>"/>
				<?php endif; ?>
                <a href="<?php echo $arParams["SEF_FOLDER"]; ?><?php echo $atItem["ID"]; ?>/"><?php echo $atItem["TITLE"]; ?></a>
            </li>
		<?php endforeach; ?>
    </ul>
<?php else: ?>
    <p><?php echo GetMessage("LIBRINGER_BOOKMARKS_COMPONENT_LIST_EMPTY"); ?></p>
<?php endif; ?>

<?php
$APPLICATION->IncludeComponent("bitrix:main.pagenavigation", "", Array(
	"NAV_OBJECT" => $arResult["pagination"],
	"SEF_MODE" => "N",
	"SHOW_COUNT" => "N"
),
	false
);
?>
