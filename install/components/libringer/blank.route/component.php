<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();


$arDefaultUrlTemplates404 = array(
);

$arDefaultVariableAliases404 = array();

$arDefaultVariableAliases = array();

$arComponentVariables = array(
    "SECTION_ID",
    "SECTION_CODE",
    "ELEMENT_ID",
    "ELEMENT_CODE",
);

$arComponentVariables = array_merge($arComponentVariables, $arParams['COMPONENT_VARS']);
if (!isset($arParams['RETURN'])) {
    $arParams['RETURN'] = "N";
}


if ($arParams["SEF_MODE"] == "Y") {
    $arVariables = array();

    $engine = new CComponentEngine($this);

    if (\Bitrix\Main\Loader::includeModule('iblock')) {
        $engine->addGreedyPart("#SECTION_CODE_PATH#");
        $engine->addGreedyPart("#SMART_FILTER_PATH#");
        $engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
    }

    $arUrlTemplates = CComponentEngine::makeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
    $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

    $componentPage = $engine->guessComponentPath(
            $arParams["SEF_FOLDER"], $arUrlTemplates, $arVariables
    );

    $b404 = false;
    if (!$componentPage) {
        $componentPage = "default";
        $b404 = true;
    }

    if ($b404) {
        $folder404 = str_replace("\\", "/", $arParams["SEF_FOLDER"]);
        if ($folder404 != "/")
            $folder404 = "/" . trim($folder404, "/ \t\n\r\0\x0B") . "/";
        if (substr($folder404, -1) == "/")
            $folder404 .= "index.php";

        if ($folder404 != $APPLICATION->GetCurPage(true)) {
            \Bitrix\Main\Loader::includeModule('iblock');
            \Bitrix\Iblock\Component\Tools::process404(
                    ""
                    , ($arParams["SET_STATUS_404"] === "Y")
                    , ($arParams["SET_STATUS_404"] === "Y")
                    , ($arParams["SHOW_404"] === "Y")
                    , $arParams["FILE_404"]
            );
        }
    }

    CComponentEngine::initComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);
    $arResult = array(
        "FOLDER" => $arParams["SEF_FOLDER"],
        "URL_TEMPLATES" => $arUrlTemplates,
        "VARIABLES" => $arVariables,
        "ALIASES" => $arVariableAliases,
        "COMPONENT_PAGE" => $componentPage,
    );
} else {
    $arVariables = array();

    $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases, $arParams["VARIABLE_ALIASES"]);
    CComponentEngine::initComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);



    $componentPage = "default";

    $currentPage = htmlspecialcharsbx($APPLICATION->GetCurPage()) . "?";
    $arResult = array(
        "FOLDER" => "",
        "URL_TEMPLATES" => array(
            "default" => $currentPage,
        ),
        "VARIABLES" => $arVariables,
        "ALIASES" => $arVariableAliases,
        "COMPONENT_PAGE" => $componentPage,
    );
}
if ($arParams['RETURN'] == 'Y') {
    return $arResult;
} else {
    $this->IncludeComponentTemplate($componentPage);
}


