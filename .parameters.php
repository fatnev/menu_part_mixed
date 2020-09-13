<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

if (!CModule::IncludeModule("iblock")) {
	return;
}

$arTypesEx = CIBlockParameters::GetIBlockTypes(array(
	"all" => " "
));
$arIBlocks = array();
$db_iblock = CIBlock::GetList(array(
	"SORT" => "ASC"
) , array(
	"SITE_ID" => $_REQUEST["site"],
	"TYPE" => ($arCurrentValues["IBLOCK_TYPE"] != "all" ? $arCurrentValues["IBLOCK_TYPE"] : "")
));

while ($arRes = $db_iblock->Fetch()) {
	$arIBlocks[$arRes["ID"]] = "[" . $arRes["ID"] . "] " . $arRes["NAME"];
}

$arComponentParameters = array(
	"GROUPS" => array() ,
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_TYPE") ,
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "content",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "Y",
		) ,
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_ID") ,
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"MULTIPLE" => "N",
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		) ,
		"DEPTH_LEVEL" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("DEPTH_LEVEL") ,
			"TYPE" => "STRING",
			"DEFAULT" => "5",
		) ,
		"SHOW_SECTIONS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SHOW_SECTIONS") ,
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		) ,
		"SHOW_ELEMENTS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SHOW_ELEMENTS") ,
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		) ,
		"CACHE_TIME" => array(
			"DEFAULT" => 36000000
		) ,
	) ,
);

