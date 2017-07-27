<?php

class Svit_Menunew_Helper_Data extends Smartwave_Megamenu_Helper_Data
{


    public function getTopMenuContent($without_custom_block = false)
    {
        $menuData = $this->getMenuData();
        extract($menuData);

        $customLinks = "";
        $customBlocks = "";

        if(!$without_custom_block){
            // --- Custom Links
            $customLinks = $_block->drawCustomLinks();
            // --- Custom Blocks
            $customBlocks = $_block->drawCustomBlock();
        }
        // --- Result ---
        $menu = <<<HTML
$customLinks
$customBlocks
HTML;
        return $menu;
    }

    public function getMobileMenuContentCustom($without_custom_block = false)
    {
        $menuData = $this->getMenuData();
        extract($menuData);

        $customLinks = "";
        $customBlocks = "";

        if(!$without_custom_block){
            // --- Custom Links
            $customLinks = $_block->drawCustomLinks();
            // --- Custom Blocks
            $customBlocks = $_block->drawCustomBlock();
        }
        // --- Result ---
        $menu = <<<HTML
$customLinks
$customBlocks
HTML;
        return $menu;
    }
    public function getMobileMenuContentSideBar($without_custom_block = false)
    {
        $menuData = $this->getMenuData();
        extract($menuData);
        // --- Menu Content ---
        $mobileMenuContent = '';
        $mobileMenuContentArray = array();
       // var_dump($_categories); die();
        foreach ($_categories as $_category) {
            $mobileMenuContentArray[] = $_block->drawMegaMenuItem($_category,'mb');
        }
        if (count($mobileMenuContentArray)) {
            $mobileMenuContent = implode("\n", $mobileMenuContentArray);
            $mobileMenuContent = str_replace("http://","//",$mobileMenuContent);
            $mobileMenuContent = str_replace("https://","//",$mobileMenuContent);
        }

        // --- Result ---
        $menu = <<<HTML
$mobileMenuContent
HTML;
        return $menu;
    }
}
