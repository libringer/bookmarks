<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("LIBRINGER_BOOKMARKS_COMPONENT_ADD_NAME"),
	"DESCRIPTION" => GetMessage("LIBRINGER_BOOKMARKS_COMPONENT_ADD_DESCRIPTION"),
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "libringer_bookmarks",
		"CHILD" => array(
			"ID" => "news",
			"NAME" => GetMessage("LIBRINGER_BOOKMARKS_BRANCH_COMPONENTS"),
			"SORT" => 10,
		),
	),
);

?>