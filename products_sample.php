<?php
class Company_Products_IndexController extends Mage_Core_Controller_Front_Action {

    function sendJson($array) {

        header('Content-Type: application/json');
        echo json_encode($array, true);
        die;
    }

    public function indexAction() {

        $request = Mage::app() -> getRequest();
        $api_key = $request -> getHeader('api_key');

        if (strlen($api_key) && $api_key == '123456789') {

            $params = $request -> getParams();
            $searchstring = ($params['search'] != '') ? $params['search'] : "";
            $pageSize = ($params['pageSize'] != '') ? $params['pageSize'] : 10;
            $page = ($params['page'] != '') ? $params['page'] : 1;
            $_productCollection = Mage::getModel('catalog/product') -> getCollection() -> addAttributeToSelect('*') -> addAttributeToFilter('name', array(
                'like' => '%'.$searchstring.
                '%'
            )) -> setPageSize($pageSize) -> setCurPage($page) -> load();

            $products = $results = array();
            $key = 0;

            foreach($_productCollection as $_product) {

                $productMediaConfig = Mage::getModel('catalog/product_media_config');
                $ImageUrl = $productMediaConfig -> getMediaUrl($_product -> getImage());
                $price = 0;
                $price = $_product -> getFinalPrice();
                $price = ($price == 0) ? $_product -> getPrice() : $price;
                $results[$key]['Id'] = $_product -> getId();
                $results[$key]['Name'] = $_product -> getName();
                $results[$key]['Url'] = $_product -> getProductUrl();
                $results[$key]['Price'] = number_format((float) $price, 2, '.', '');
                $results[$key]['Description'] = $_product -> getDescription();
                $results[$key]['ImageUrl'] = $ImageUrl;
                $key++;
            }

            if (count($_productCollection)) {

                $products['status'] = 200;
                $products['products'] = $results;
                $products['msg'] = 'Products information.';
            } else {

                $products['status'] = 300;
                $products['products'] = $results;
                $products['msg'] = 'No Product information.';
            }
        } else {

            $products['status'] = 500;
            $products['msg'] = 'Invalid key.';
        }

        $this -> sendJson($products);
    }
}
