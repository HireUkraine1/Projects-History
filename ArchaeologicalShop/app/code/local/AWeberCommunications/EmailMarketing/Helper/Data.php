<?php

/* AWeber Magento API Integration
 * (C) AWeber Communications, Inc
 *
 * Default Helper
 *
 * This class defines various UI helpers
 */

define('AWEBER_APP_ID', 'c3c7a64a');


class AWeberCommunications_EmailMarketing_Helper_Data extends Mage_Core_Helper_Abstract {

    /* The code below was written by Zachstronaut LLC and is licensed under the
     * creative commons license.
     * http://www.zachstronaut.com/posts/2009/01/20/php-relative-date-time-string.html#comments
     */
    function time_elapsed_string($ptime) {
        $etime = time() - $ptime;

        if ($etime < 1) {
            return '0 seconds';
        }

        $a = array(12 * 30 * 24 * 60 * 60  =>  'year',
                   30 * 24 * 60 * 60       =>  'month',
                   24 * 60 * 60            =>  'day',
                   60 * 60                 =>  'hour',
                   60                      =>  'minute',
                   1                       =>  'second');
        foreach ($a as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . $str . ($r > 1 ? 's' : '');
            }
        }
    }

     /* UI Function : Not Connected
      *
      * Displays the HTML for when we're not connected to aweber */
    public function not_connected($controller, $config) {
         $auth_url = 'https://auth.aweber.com/1.0/oauth/authorize_app/' . AWEBER_APP_ID;
         $disabled = "<span class='red'>&#10008;</span>";
         $error    = $config->waspGet('error');

         if (!empty($error)) {
             $error = <<<DATA
         <div class="indented">
           <div id="messages">
            <ul class="messages">
             <li class="error-msg" />$error
            <ul />
           </div>
         </div>
DATA;
        }

        $connect_url = "$auth_url?oauth_callback={$controller->getURL('emailmarketing/admin/connect')}";
        return <<<DATA
         <style type="text/css">
          .indented  { padding-left: 20px; }
          .no_margin { margin-bottom: 0em; }
         </style>

         <div class="indented">
           <h2 class="no_margin">AWeber Email Marketing</h2>
           <p>Control how your store is integrated with AWeber! <br />
              From here you can choose which list your customers are subscribed to when they purchase items from your store.<br />
           </p>
         </div>

         <br />

         <div class="indented">
           <div id="messages">
            <ul class="messages">
             <li class="error-msg" />
              You are not connected to AWeber.
            <ul />
           </div>
         </div>

         <div class="indented">
           <p class="switcher">
             <a href="$connect_url">Connect to AWeber</a>
           </p>
           <br />
         </div>

         $error
DATA;
    }

     /* UI Function:  _is_connected()
      *
      * Returns the HTML for the integration when its connected
      */
     public function is_connected($controller, $config) {
         $disconnect_url =  $controller->getURL('emailmarketing/admin/disconnect');
         $update_url = $controller->getURL('emailmarketing/admin/update');
         $form_key = Mage::getSingleton('core/session')->getFormKey();

         /* default lists */
         $lists = $config->WASP->lists();
         $products = $config->WASP->products();
         $rules = $config->WASP->rules();

         $default_list_options = $this->select_helper($lists, $config->waspGet('default_list_id'), array());
         $list_options = $this->select_helper($lists, null, array());
         $product_options = $this->select_helper($products, null, $rules);
         $uses_rules = (count($rules) > 0) ? "checked" : "";

         $enabled_default_list = <<<DATA
           <p class="switcher">
             <label for="default_list">When customers purchase items from your store, subscribe them to:</label>
             <select name=default_list onChange="document.aweber.submit()">$default_list_options</select> <br />
             <br />
             <input type="checkbox" name="use_rules" value="x" $uses_rules onClick="aweber_use_rules()"/>
             <label for="use_rules">&nbsp;I want to subscribe customers to different lists based on the products they purchased.</label>
           </p>
           <br />
DATA;
         $disabled_default_list = <<<DATA
           <div id="messages">
            <ul class="messages">
             <li class="error-msg" />You must select a list!
            <ul />
           </div>
           <p class="switcher">
             <label for="default_list">When someone makes a purchase, subscribe them to:</label>
             <select name=default_list onChange="document.aweber.submit()">$default_list_options</select>
           </p>
           <br />

DATA;

         $default_list = ($config->waspGet('default_list_id') ? $enabled_default_list : $disabled_default_list);

         $active_rules = "";
         foreach($rules as $product_id => $list_id) {

             if (!array_key_exists($list_id, $lists) or !array_key_exists($product_id, $products)) {
                 # a list or product that a rule was made for doesn't exist any more!
                 # we normally just want to remove that rule unless the lists array is
                 # empty due to a temporary api connection issue.
                 if (!empty($lists)) {
                     # unless lists is empty!
                     $config->WASP->deleteRule($product_id);
                 }
                 continue;
             }

             $button_function = "rule_$product_id";
             $remove_button = '<button type="button" class="scalable delete" onclick="aweber_form_submit(\''. $button_function . '\');" style=""><span>Delete</span></button>';

             $active_rules .= '<tr>';
             $active_rules .= ' <td>' . htmlentities($products[$product_id]) . '</td>';
             $active_rules .= ' <td>' . 'Subscribe them to'             . '</td>';
             $active_rules .= ' <td>' . htmlentities($lists[$list_id])       . '</td>';
             $active_rules .= ' <td>' . $remove_button                       . '</td>';
             $active_rules .= '</tr>';
         }
         $add_product_css = (count($rules) >= count($products) ? 'display: none;'         : '');
         $use_rules_css   = (($uses_rules !== "checked")       ? 'style="display: none;"' : '');

         // look for queue errors
         $subscribers = $config->waspQueue->errorSubscribers();
         $message = "";
         $has_errors = false;
         foreach($subscribers as $subscriber) {
             $status = $subscriber->getStatus();
             $has_errors = true;
             if ($status == 'new') {
                 $message = <<<DATA
                 <div class="indented">
                   <div id="messages">
                    <ul class="messages">
                     <li class="error-msg" />
                        It looks like the Magento background cron job is not setup. <br />
                        <a target=_new href="http://www.magentocommerce.com/wiki/1_-_installation_and_configuration/magento_installation_guide#appendixcron_job_setup">
                        Click here for Magento's instructions on setting up the cron job.</a>
                    <ul />
                   </div>
                 </div>
DATA;
             }
         }

         if ($uses_rules === "checked") {
             $rule_checkbox_confirmation = <<<DATA
                if(confirm('This will remove all of your preferences to subscribe customers to different lists based on the products they purchased.')) {
                    document.getElementById('aweber_product_rules').style.display = 'none';
                    document.aweber.submit();
                } else {
                   document.aweber.use_rules.checked = true;
                }
DATA;
         } else {
             $rule_checkbox_confirmation = "document.getElementById('aweber_product_rules').style.display = 'none';";
         }

         $output = <<<DATA
         <style type="text/css">
          .indented  { padding-left: 20px; }
          .no_margin { margin-bottom: 0em; }
         </style>

         <script type="text/javascript">
         function aweber_form_submit(name) {
             document.aweber.action.value = name;
             document.aweber.submit();
         }
         function aweber_use_rules() {
             if(document.aweber.use_rules.checked) {
                document.getElementById('aweber_product_rules').style.display = '';
             } else {
                $rule_checkbox_confirmation
             }
         }
         </script>

         <form name="aweber" method="POST" action="$update_url">
         <input type="hidden" name="form_key" value="$form_key">
         <input type="hidden" name="action">
         $message
         <div class="indented">
           <h2 class="no_margin">AWeber Email Marketing</h2>
           <p>Control how your store is integrated with AWeber! <br />
              From here you can choose which list your customers are subscribed to when they purchase items from your store.<br />
           </p>
         </div>

         <br />

         <div class="indented">
           <p class="switcher">
             You are connected to AWeber. (<a href="$disconnect_url">Disconnect</a>)
           </p>
           <br />
         </div>

          <div class="indented">
           $default_list
          </div>
DATA;

         if ((count($lists) > 1) and count($products) > 0 and $config->waspGet('default_list_id')) {
             $error_report_url = $controller->getURL('emailmarketing/admin/errorreport');
             $output .= <<<DATA
          <div class="indented" id="aweber_product_rules" $use_rules_css>
           <div class="grid">
           <div class="hor-scroll">
           <p>Subscribe customers to different lists based on the products they purchase.</p>
           <table cellspacing="0" class="data" id="productGrid_table">
           <col />
           <col width="150" />
           <col width="100" />
           <col width="50" />
           <thead>
            <tr class="headings">
             <th class="no-link"><span class="nobr">When your customers purchase</span></th>
             <th class="no-link"><span class="nobr">Action</span></th>
             <th class="no-link"><span class="nobr">List</span></th>
             <th class="no-link last"><span class="nobr">&nbsp;</span></th>
            </tr>
           </thead>
           <tbody>
           $active_rules
           <tr style="$add_product_css" >
            <td><select name="new_product">$product_options</select></td>
            <td>Subscribe them to</td>
            <td><select name="new_list" onChange="document.aweber.submit()">$list_options</select></td>
            <td>&nbsp;</td>
           </tr>
           </tbody>
          </table>
          </div>
          </div>
          </div>
          </form>
DATA;
            if($has_errors) {
                $output .= <<<DATA
              <br /> <br /> <br /> <br /> <br />
              <div class="indented">
               <a href="$error_report_url">Error Report</a>
              </div>
DATA;
               }
         }
         return $output;
    }


     /* UI Function:  error_report()
      *
      * Returns the HTML for the integration error report
      */
     public function error_report($controller, $config) {
         $update_url = $controller->getURL('emailmarketing/admin/errorUpdate');
         $return_url = $controller->getURL('emailmarketing/admin/index');
         $form_key = Mage::getSingleton('core/session')->getFormKey();

         /* default lists */
         $lists = $config->WASP->lists();
         $products = $config->WASP->products();
         $rules = $config->WASP->rules();

         $message  = "";
         $function = array_key_exists('function', $_GET) ? htmlentities($_GET['function']) : '';
         $email    = array_key_exists('email'   , $_GET) ? htmlentities($_GET['email'])    : '';

         if ($function == 'subscribed') {
             $css = "success-msg";
             $action = "subscribed";
         } else if ($function == 'failed') {
             $css = "error-msg";
             $action = "not subscribed";
         } else if ($function == 'removed') {
             $css = "success-msg";
             $action = "deleted from the queue";
         }

         if (!empty($function)) {
             $message = <<<DATA
             <div class="indented">
               <div id="messages">
                <ul class="messages">
                 <li class="$css" />$email was $action.
                <ul />
               </div>
             </div>
DATA;
         }

         $output = <<<DATA
         <style type="text/css">
          .indented  { padding-left: 20px; }
          .no_margin { margin-bottom: 0em; }
         </style>

         <script type="text/javascript">
         function aweber_form_submit(name) {
             document.aweber.action.value = name;
             document.aweber.submit();
         }
         </script>

         $message

         <form name="aweber" method="POST" action="$update_url">
         <input type="hidden" name="form_key" value="$form_key">
         <input type="hidden" name="action">

         <div class="indented">
           <h2 class="no_margin">AWeber Email Marketing</h2>
           Diagnostic Error Report - <a href="$return_url">Return to AWeber Email Marketing</a><br />
           <br />
         </div>
DATA;
 
         $subscribers = $config->waspQueue->errorSubscribers();
         $has_errors = false;
         $failed_subscribers = "";
         $ctr = 0;
         $has_unprocessed_jobs = false;
 
         foreach($subscribers as $subscriber) {
             $has_errors = true;
             $ctr++;

             $id = $subscriber->getId();
             $name = $subscriber->getName();
             $email = $subscriber->getEmail();
             $list_id = $subscriber->getListId();
             $error = $subscriber->getResult();
             $time = $subscriber->getCreatedAt();
             $date = $this->time_elapsed_string($time);
             $status = $subscriber->getStatus();
             $list_name = array_key_exists("$list_id", $lists) ? $lists[$list_id] : 'unknown list';

             if ($status == 'new') {
                $error = 'Subscriber not processed: please ensure background job is enabled!';
                $has_unprocessed_jobs = true;
             }


             $button_function = "queue_remove_$id";
             $remove_button = '<button type="button" class="scalable delete" onclick="aweber_form_submit(\''. $button_function . '\');" style=""><span>Delete</span></button>';
             $button_function = "queue_retry_$id";
             $retry_button = '<button type="button" class="scalable" onclick="aweber_form_submit(\''. $button_function . '\');" style=""><span>Retry</span></button>';

             $failed_subscribers .= '<tr>';
             $failed_subscribers .= ' <td>' . htmlentities($date)     . ' ago</td>';
             $failed_subscribers .= ' <td>' . htmlentities($name)      . '</td>';
             $failed_subscribers .= ' <td>' . htmlentities($email)     . '</td>';
             $failed_subscribers .= ' <td>' . htmlentities($list_name) . '</td>';
             $failed_subscribers .= ' <td>' . htmlentities($error) . '</td>';
             $failed_subscribers .= ' <td>' . $retry_button . "&nbsp;&nbsp;" . $remove_button . '</td>';
             $failed_subscribers .= '</tr>';
         }


         if ($has_errors) {
             $output .= <<<DATA
                 <div class="indented">
                 <div id="messages">
                  <ul class="messages">
                   <li class="error-msg" />
                    Some customers could not be subscribed to your list when they purchased items from your store.<br />
                    You can retry subscribing them from your list or delete them from the queue below.<br />
                  <ul />
                 </div>
                </div>

                 <div class="indented">
                  <div class="grid">
                  <div class="hor-scroll">
                  <table cellspacing="0" class="data" id="productGrid_table">
                  <col width="150" />
                  <col />
                  <col />
                  <col width="150" />
                  <col />
                  <col width="150" />
                  <thead>
                   <tr class="headings">
                    <th class="no-link"><span class="nobr">How long ago?</span></th>
                    <th class="no-link"><span class="nobr">Customer's Name</span></th>
                    <th class="no-link"><span class="nobr">Customer's Email Address</span></th>
                    <th class="no-link"><span class="nobr">List Name</span></th>
                    <th class="no-link"><span class="nobr">Result</span></th>
                    <th class="no-link last"><span class="nobr">&nbsp;</span></th>
                   </tr>
                  </thead>
                  <tbody>
                   $failed_subscribers
                  </tbody>
                  </table>
                  </div>
                  </div>
                 </div>
DATA;
         } else {
             $output .= <<<DATA
                <div class="indented">
                 <div id="messages">
                  <ul class="messages">
                   <li class="success-msg" />
                    There are no errors to report.
                  <ul />
                 </div>
                </div>
DATA;
         }
             
         $output .= "</form>";
         return $output;
     }

     /* UI Helper: Generate SELECT drop down list */
     public function select_helper($lists, $selected_list_id, $excluded_lists=array()) {
          $options = "";

          if(!array_key_exists($selected_list_id, $lists)) {
              $options .= "<option value=\"\">-----------------</option>";
          }

          foreach($lists as $list_id => $list_name) {
              if (array_key_exists($list_id, $excluded_lists)) {
                  continue;
              }
              $selected = ($selected_list_id == $list_id) ? "SELECTED" : "";
              $options .= "<option $selected value=\"$list_id\">$list_name</option>";
          }
          return $options;
     }
}

