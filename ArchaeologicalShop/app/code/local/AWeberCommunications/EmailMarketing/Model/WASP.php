<?php
/* AWeber Magento API Integration
 * (C) AWeber Communications, Inc
 *
 * Usage:
 *
 * You must create a 'DatabaseClass' and pass it into the constructor of AWeberWaspModule
 * The database class must implement the following methods
 *
 * PERSISTENT storage functions:
 *    public function waspGet($name)
 *    public function waspSet($name, $value)
 *
 *    public function waspCacheGet($name, $value)
 *    public function waspCacheSet($name, $value)
 *
 *    public function waspDelete($name)
 *
 * SHOPPING CART functions
 *    public function waspProducts()
 */

require_once(dirname(__FILE__) . "/AWeber.php");

class AWeberWaspModule {

    public function __construct($database_class) {
        $this->database = $database_class;
    }

    /* CONNECT : called by UI when you want to connect an
     *           aweber account to this integration.
     *  @params: authorization_code : string
     */
    public function connect($authorization_code) {
        try {
            $auth_codes = AWeberAPI::getDataFromAweberID($authorization_code);
            list($consumer_key, $consumer_secret, $access_key, $access_secret) = $auth_codes;

        } catch(AWeberAPIException $exc) {
            $this->disconnect();
            $this->database->waspSet('error', $exc->message);
            return false;
        }

        if(!isset($access_secret)) {
            $this->disconnect();
            $this->database->waspSet('error', 'authorization_code is required');
            return false;
        }

        $this->database->waspSet('is_connected'         , true);
        $this->database->waspSet('error'                , null);
        $this->database->waspSet('consumer_key'         , $consumer_key);
        $this->database->waspSet('consumer_secret'      , $consumer_secret);
        $this->database->waspSet('access_key'           , $access_key);
        $this->database->waspSet('access_secret'        , $access_secret);
        $this->database->waspSet('default_list_id'      , null);
        $this->database->waspSet('rules'                , array());
        $this->database->waspSet('subscriber_added'     , 0);
        $this->database->waspSet('subscriber_errors'    , 0);
        $this->database->waspSet('subscriber_duplicates', 0);
        $this->database->waspQueue->clear();
        return true;
    }

    /* DISCONNECT : called by UI (or this module) when you want to 
     *              break the connection and remove all api credentials
     *              from your local database
     */
    public function disconnect() {
        $this->database->waspSet('is_connected', false);
        $keys = array('consumer_key', 'consumer_secret', 'access_key', 'access_secret',
                      'default_list_id', 'rules', 'error', 'queue');

        foreach ($keys as $key) {
            $this->database->waspDelete($key);
        }

        $this->database->waspCacheSet('lists', null);
        $this->database->waspQueue->clear();
        return true;
    }

    /* PRODUCTS:  return an array of products that are available in your
     *            store
     *
     * array('product_id_1' => 'product 1 name', ...)
     */
    public function products() {
        $products = $this->database->waspProducts();
        if ($products == null) { return array(); }
        return $products;
    }

    /* RULES:  return an array of rules to determine what list to subscribe to
     *         based of the product purchased
     *
     * array('product_id_1' => LIST_ID, ...)
     */
    public function rules() {
        $rules = $this->database->waspGet('rules');
        if ($rules == null) { return array(); }
        return $rules;
    }

    /* ADD RULE:  add a new product rule, pass the unique PRODUCT_ID and a LIST_ID
     *  @params: product_id : string
     *  @params: list_id    : integer
     */
    public function addRule($product_id, $list_id) {
        $rules = $this->rules();
        $rules["$product_id"] = $list_id;
        $this->database->waspSet('rules', $rules);
    }

    /* DELETE RULE:  delete a product rule, pass the unique PRODUCT_ID
     *  @params: product_id : string
     */
    public function deleteRule($product_id) {
        $rules = $this->rules();
        unset($rules["$product_id"]);
        $this->database->waspSet('rules', $rules);
    }

    /* DEFAULT LIST:  return the default list to subscribe to
     *  @params: list_id    : integer
     */
    public function setDefaultList($list_id) {
        $this->database->waspSet('default_list_id', $list_id);
    }

    /* NOTIFICATION:
     *
     * @params ad_tracking : string required
     * @params email : string required
     * @params name : string optional
     * @params products: indexed (not associative) array of product-ids purchased
     */
    public function purchaseNotification($ad_tracking, $name, $email, $products=null) {
        if (!$this->database->waspGet('is_connected')) {
            /* app is not connected, just return true so the order continues to be processed */
            return true;
        }
        $list_id = $this->_getListId($products);
        $this->database->waspQueue->add($ad_tracking, $name, $email, $list_id);
    }


    /* get list id:  determines the list id to subscribe to based on the
     *               products purchased, product rules, and default list id.
     */
    protected function _getListId($purchased_products) {
        /* find list id:  we take the 'FIRST' match on a rule, or the default
         * $purchased_products must be an indexed array, not associative!
         */
        $rules = $this->rules();
        $intersect_keys = array_intersect(array_keys($rules), $purchased_products);
        $product_id = array_shift($intersect_keys);
        return isset($product_id) ? $rules[$product_id] : $this->database->waspGet('default_list_id');
    }

    /* lists: return the list of lists from the API
     *        This is cached for performance reasons
     */
    public function lists() {
        $lists = $this->database->waspCacheGet('lists');
        if (!isset($lists) or empty($lists)) {
            $lists           = array();
            $consumer_key    = $this->database->waspGet('consumer_key');
            $consumer_secret = $this->database->waspGet('consumer_secret');
            $access_key      = $this->database->waspGet('access_key');
            $access_secret   = $this->database->waspGet('access_secret');
            $aweber          = new AWeberAPI($consumer_key, $consumer_secret);

            try {
                $account = $aweber->getAccount($access_key, $access_secret);
                foreach($account->lists as $list) {
                    $lists[$list->id] = $list->name;
                }
            } catch(AWeberAPIException $exc) {

                if($exc->type == 'UnauthorizedError') {
                    /* we are disconnected!, update the db */
                    $this->disconnect();
                }
                $this->database->waspSet('error', $exc->message);
                $this->_incrementCounter('errors');
                $lists = array();
            }
            $this->database->waspCacheSet('lists', $lists);
        }
        return $lists;
    }

    /* addSubscriber :  does the work of adding a subscriber to a list via the API.
     */
    public function addSubscriber($ad_tracking, $name, $email, $list_id) {
        $consumer_key    = $this->database->waspGet('consumer_key');
        $consumer_secret = $this->database->waspGet('consumer_secret');
        $access_key      = $this->database->waspGet('access_key');
        $access_secret   = $this->database->waspGet('access_secret');
        $aweber = new AWeberAPI($consumer_key, $consumer_secret);

        try {
            $account = $aweber->getAccount($access_key, $access_secret);
            $account_id = $account->id;
            $url = "/accounts/{$account_id}/lists/{$list_id}/subscribers";
            $subscribers = $account->loadFromUrl($url);

            /* create a subscriber */
            $params = array(
                'email' => $email,
                'ad_tracking' => $ad_tracking,
                'name' => $name,
            );
            $subscribers->create($params);

        } catch(AWeberAPIException $exc) {

            if($exc->type == 'UnauthorizedError') {
                /* we are disconnected!, update the db */
                $this->disconnect();
                $this->database->waspSet('error', $exc->message);
                $this->_incrementCounter('errors');
                return false;
            }
            if($exc->message == 'email: Subscriber already subscribed.') {
                $this->database->waspDelete('error');
                $this->_incrementCounter('duplicates');
                return true;
            }
            $this->database->waspSet('error', $exc->message);
            $this->_incrementCounter('errors');
            return false;
        }

        $this->database->waspDelete('error');
        $this->_incrementCounter('added');
        return true;
    }


    /* increments add subscriber stats counters
     *  @param 'type' (string), valid values: 'added', 'errors', or 'duplicates'
     */
    protected function _incrementCounter($type) {
        $value = $this->database->waspGet("subscriber_$type");
        if (!isset($value)) { $value = 0; }
        $value++;
        $this->database->waspSet("subscriber_$type", $value);
    }
}
