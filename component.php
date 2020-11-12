<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arResult = array();

if(!function_exists("menuArray")) {
	function menuArray(&$res, &$menuItems, $arParent, $depthLevel)
	{
		foreach($arParent as $item) {
			$isParent = ($item['IS_SECTION'] && isset($menuItems[$item['ID']]));
			$res[] = array(
				$item['NAME'],
				$item['LINK'],
				array(),
				array(
					'FROM_IBLOCK' => true,
					'IS_PARENT' => $isParent,
					'DEPTH_LEVEL' => $depthLevel
				)
			);
			if($isParent) {
				menuArray($res, $menuItems, $menuItems[$item['ID']], $depthLevel + 1);
			}
		}
	}
}

if(!isset($arParams['CACHE_TIME'])) {
	$arParams['CACHE_TIME'] = 36000000;
}

$arParams['DEPTH_LEVEL'] = intval($arParams['DEPTH_LEVEL']);
if($arParams['DEPTH_LEVEL'] <= 0) {
	$arParams['DEPTH_LEVEL'] = 1;
}

if(empty($arParams['SHOW_SECTIONS'])){
	$arParams['SHOW_SECTIONS'] = "Y";
}
if(empty($arParams['SHOW_ELEMENTS'])){
	$arParams['SHOW_ELEMENTS'] = "Y";
}

if(empty($arParams['IBLOCK_ID']) && empty($arParams['IBLOCK_CODE'])){
	ShowError("Error in gnvs:menu.mixed. Set IBLOCK_ID or IBLOCK_CODE.");
	return $arResult;
}

if($this->StartResultCache(false, $USER->GetGroups()))
{
	CModule::IncludeModule('iblock');
	$menuItems = array(
		"ROOT" => array()
	);

	// Получаем разделы
	if($arParams['SHOW_SECTIONS'] == "Y")
	{
		$arOrder = array(
			'SORT' => 'ASC'
		);
		$arFilter = array(
			'GLOBAL_ACTIVE' => 'Y',
			'ACTIVE' => 'Y',
			'<=DEPTH_LEVEL' => $arParams['DEPTH_LEVEL']
		);
		if(!empty($arParams['IBLOCK_ID'])){
			$arFilter['IBLOCK_ID'] = $arParams['IBLOCK_ID'];
		}
		if(!empty($arParams['IBLOCK_CODE'])){
			$arFilter['IBLOCK_CODE'] = $arParams['IBLOCK_CODE'];
		}

		$arSelect = array(
			'ID',
			'DEPTH_LEVEL',
			'NAME',
			'SECTION_PAGE_URL',
			'IBLOCK_SECTION_ID'
		);

		$rsSections = CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect);

		$arSectionId = array();
		while($arSection = $rsSections->GetNext()) {
			$arSection['IS_SECTION'] = 1;
			$arSection['LINK'] = $arSection['SECTION_PAGE_URL'];
			if($arSection['IBLOCK_SECTION_ID']) {
				$menuItems[$arSection['IBLOCK_SECTION_ID']][] = $arSection;
			} else {
				$menuItems['ROOT'][] = $arSection;
			}
			$arSectionId[] = $arSection['ID'];
		}
	}

	// Получаем элементы разделов
	if($arParams['SHOW_ELEMENTS'] == "Y")
	{
		$arOrder = array(
			'SORT' => 'ASC'
		);
		$arFilter = array(
			'ACTIVE' => 'Y',
			array(
				'LOGIC' => 'OR',
				array(
					'SECTION_ID' => $arSectionId
				),
				array(
					'SECTION_ID' => false
				)
			)
		);
		if(!empty($arParams['IBLOCK_ID'])){
			$arFilter['IBLOCK_ID'] = $arParams['IBLOCK_ID'];
		}
		if(!empty($arParams['IBLOCK_CODE'])){
			$arFilter['IBLOCK_CODE'] = $arParams['IBLOCK_CODE'];
		}

		$arSelect = array(
			'ID',
			'NAME',
			'DETAIL_PAGE_URL',
			'IBLOCK_SECTION_ID'
		);
		$res = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);

		while($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();
			$arFields['IS_SECTION'] = 0;
			$arFields['LINK'] = $arFields['DETAIL_PAGE_URL'];

			if($arFields['IBLOCK_SECTION_ID']) {
				$menuItems[$arFields['IBLOCK_SECTION_ID']][] = $arFields;
			} else {
				$menuItems['ROOT'][] = $arFields;
			}
		}
	}

	if($arParams['SHOW_SECTIONS'] == "Y" || $arParams['SHOW_ELEMENTS'] == "Y"){
		menuArray($arResult, $menuItems, $menuItems['ROOT'], 1);
	}

	$this->EndResultCache();
}

return $arResult;

