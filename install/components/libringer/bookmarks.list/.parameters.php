<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


$arComponentParameters = array(
	"PARAMETERS" => array(
		"SEF_MODE" => Array(
			"news" => array(
				"NAME" => GetMessage("T_IBLOCK_SEF_PAGE_NEWS"),
				"DEFAULT" => "",
				"VARIABLES" => array(),
			),
			"section" => array(
				"NAME" => GetMessage("T_IBLOCK_SEF_PAGE_NEWS_SECTION"),
				"DEFAULT" => "",
				"VARIABLES" => array("SECTION_ID"),
			),
			"detail" => array(
				"NAME" => GetMessage("T_IBLOCK_SEF_PAGE_NEWS_DETAIL"),
				"DEFAULT" => "#ELEMENT_ID#/",
				"VARIABLES" => array("ELEMENT_ID", "SECTION_ID"),
			),
			"search" => array(
				"NAME" => GetMessage("T_IBLOCK_SEF_PAGE_SEARCH"),
				"DEFAULT" => "search/",
				"VARIABLES" => array(),
			),
			"rss" => array(
				"NAME" => GetMessage("T_IBLOCK_SEF_PAGE_RSS"),
				"DEFAULT" => "rss/",
				"VARIABLES" => array(),
			),
			"rss_section" => array(
				"NAME" => GetMessage("T_IBLOCK_SEF_PAGE_RSS_SECTION"),
				"DEFAULT" => "#SECTION_ID#/rss/",
				"VARIABLES" => array("SECTION_ID"),
			),
		),
		"AJAX_MODE" => array(),
		"COUNT" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_CONT"),
			"TYPE" => "STRING",
			"DEFAULT" => "20",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
		"CACHE_FILTER" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("BN_P_CACHE_FILTER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BN_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);
?>
