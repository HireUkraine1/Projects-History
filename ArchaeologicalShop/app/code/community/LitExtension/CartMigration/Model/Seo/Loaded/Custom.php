<?php

/**
 * @project: CartMigration
 * @author : LitExtension
 * @url    : http://litextension.com
 * @email  : litextension@gmail.com
 */
class LitExtension_CartMigration_Model_Seo_Loaded_Custom {

    public function getCategoriesExtQuery($cart, $categories) {
        return false;
    }

    public function getCategoriesExtRelQuery($cart, $categories, $categoriesExt) {
        return false;
    }

    public function convertCategorySeo($cart, $category, $categoriesExt) {
        $result = array();
        $cat_tag = $cart->getRowValueFromListByField($categoriesExt['object']['categories_description'], 'categories_id', $category['categories_id'], 'categories_head_title_tag');
        $path_name = $this->_convertUrl($cat_tag);
        $path_id = $category['categories_id'];
        $parent_id = $category['parent_id'];
        if ($parent_id) {
            //$parent_name = $cart->getRowValueFromListByField($categoriesExt['object']['seo_categories_description'], 'categories_id', $parent_id, 'categories_name');
            //$path_name_1 = $this->_convertUrl($parent_name);
            //$path_name = $path_name_1 . '-' . $path_name;
            $path_id = $parent_id . '_' . $path_id;
        }
        $notice = $cart->getNotice();
        $path_url = $path_name . "-c-" . $path_id . ".html";
        foreach ($notice['config']['languages'] as $lang_id => $store_id) {
            $result[] = array(
                'store_id' => $store_id,
                'request_path' => $path_url
            );
        }
        return $result;
    }

    public function getProductsExtQuery($cart, $products) {
        return false;
    }

    public function getProductsExtRelQuery($cart, $products, $productsExt) {
        return false;
    }

    public function convertProductSeo($cart, $product, $productsExt) {
        $result = array();
        $path_id = $product['products_id'];
        $product_tag = $cart->getRowValueFromListByField($productsExt['object']['products_description'], 'products_id', $product['products_id'], 'products_head_title_tag');
        $path_name = $this->_convertUrl($product_tag);
        $path = $path_name . '-p-' . $path_id . ".html";
        $notice = $cart->getNotice();
        foreach ($notice['config']['languages'] as $lang_id => $store_id) {
			$result[] = array(
				'store_id' => $store_id,
				'request_path' => $path
			);
        }
        return $result;
    }

    protected function _convertUrl($text) {
        $array = explode(" ", $text);
	$output = array_slice($array, 0, 6);
	$text = implode(" ", $output);
        $no_special = preg_replace("/[^\s-\w]+/", "", $text);
        $no_single = preg_replace('/(\b\w{1,3}\b)/','',$no_special);
        $url = str_replace(' ', '-', htmlspecialchars($no_single));
        $string = preg_replace('/-{2,}/', '-', $url);
        $string = trim($string, "-");
        $string = strtolower($string);
        return $string;
    }

}
