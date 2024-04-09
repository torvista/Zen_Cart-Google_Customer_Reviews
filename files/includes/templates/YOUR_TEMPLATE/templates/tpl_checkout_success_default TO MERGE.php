<?php // Google Customer Reviews
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=checkout_success.
 * Displays confirmation details after order has been successfully processed.
 *
 * @copyright Copyright 2003-2022 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: DrByte 2020 Dec 25 Modified in v1.5.8-alpha $
 */
?>
<div class="centerColumn" id="checkoutSuccess">
<!--bof -gift certificate- send or spend box-->
<?php
// only show when there is a GV balance
  if ($customer_has_gv_balance ) {
?>
<div id="sendSpendWrapper">
<?php require($template->get_template_dir('tpl_modules_send_or_spend.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_send_or_spend.php'); ?>
</div>
<?php
  }
?>
<!--eof -gift certificate- send or spend box-->

<h1 id="checkoutSuccessHeading"><?php echo HEADING_TITLE; ?></h1>
<div id="checkoutSuccessOrderNumber"><?php echo TEXT_YOUR_ORDER_NUMBER . $zv_orders_id; ?></div>
<?php if (DEFINE_CHECKOUT_SUCCESS_STATUS >= 1 and DEFINE_CHECKOUT_SUCCESS_STATUS <= 2) { ?>
<div id="checkoutSuccessMainContent" class="content">
<?php
/**
 * require the html_defined text for checkout success
 */
  require($define_page);
?>
</div>
<?php } ?>
<!-- bof payment-method-alerts -->
<?php
if (isset($additional_payment_messages) && $additional_payment_messages != '') {
?>
  <div class="content">
  <?php echo $additional_payment_messages; ?>
  </div>
<?php
}
?>
<!-- eof payment-method-alerts -->

<div id="checkoutSuccessLogoff">
<?php
  if (isset($_SESSION['customer_guest_id'])) {
    echo TEXT_CHECKOUT_LOGOFF_GUEST;
  } elseif (isset($_SESSION['customer_id'])) {
    echo TEXT_CHECKOUT_LOGOFF_CUSTOMER;
  }
?>
</div>
<div class="buttonRow forward">
    <a href="<?php echo zen_href_link(FILENAME_CONTACT_US, '', 'SSL'); ?>" id="linkContactUs"><?php echo zen_image_button(BUTTON_IMAGE_CONTACT_US , BUTTON_CONTACT_US_TEXT); ?></a>
    <a href="<?php echo zen_href_link(FILENAME_ACCOUNT, '', 'SSL'); ?>" id="linkMyAccount"><?php echo zen_image_button(BUTTON_IMAGE_MY_ORDERS , BUTTON_MY_ORDERS_TEXT); ?></a>
    <a href="<?php echo zen_href_link(FILENAME_LOGOFF, '', 'SSL'); ?>" id="linkLogoff"><?php echo zen_image_button(BUTTON_IMAGE_LOG_OFF , BUTTON_LOG_OFF_ALT); ?></a>
</div>

<div id="checkoutSuccessContactLink"><?php echo TEXT_CONTACT_STORE_OWNER;?></div>

<br class="clearBoth">

<!-- bof order details -->
<?php
require($template->get_template_dir('tpl_account_history_info_default.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_account_history_info_default.php');
?>
<!-- eof order details -->

<br class="clearBoth">
<!--bof -product notifications box-->
<?php
/**
 * The following creates a list of checkboxes for the customer to select if they wish to be included in product-notification
 * announcements related to products they've just purchased.
 **/
    if ($flag_show_products_notification == true) {
?>
<fieldset id="csNotifications">
<legend><?php echo TEXT_NOTIFY_PRODUCTS; ?></legend>
<?php echo zen_draw_form('order', zen_href_link(FILENAME_CHECKOUT_SUCCESS, 'action=update', 'SSL')); ?>

<?php foreach ($notificationsArray as $notifications) { ?>
<?php echo zen_draw_checkbox_field('notify[]', $notifications['products_id'], true, 'id="notify-' . $notifications['counter'] . '"') ;?>
<label class="checkboxLabel" for="<?php echo 'notify-' . $notifications['counter']; ?>"><?php echo $notifications['products_name']; ?></label>
<br>
<?php } ?>
<div class="buttonRow forward"><?php echo zen_image_submit(BUTTON_IMAGE_UPDATE, BUTTON_UPDATE_ALT); ?></div>
<?php echo '</form>'; ?>
</fieldset>
<?php
    }
?>
<!--eof -product notifications box-->

<h3 id="checkoutSuccessThanks" class="centeredContent"><?php echo TEXT_THANKS_FOR_SHOPPING; ?></h3>
</div>

<?php //Google Customer Reviews
//insert your Google Merchant Center ID here
define('GOOGLE_MERCHANT_CENTER_ID', '12345678');
//add modify your language
switch ($_SESSION['languages_code']){
    case 'es':
        $gcr_lang = 'es';
        break;
    default:
        $gcr_lang = 'en-GB';
}
//Estimated Delivery Date
//add modify your delivery times
//two days for Spain
if ($order->delivery['country']['iso_code_2'] === 'ES') {
    $estimated_delivery_date = date('Y-m-d', time() + 2880);
//two weeks for ex-Spain
} else {
    //two weeks
    $estimated_delivery_date = date('Y-m-d', time() + 604800*2);
}
//list of products to insert into javascript
$gtins = '';
foreach ($order->products as $product) {
    //barcode/ISBN/EAN
    $gtin = '';
    //Products Options Stock Manager
    if (function_exists('is_pos_product') && is_pos_product($product['id'])) {
        $gtin_info = $db->Execute(
            'SELECT pos_mpn, pos_ean FROM ' . TABLE_PRODUCTS_OPTIONS_STOCK . ' WHERE pos_model ="' . $product['model'] . '" LIMIT 1',
        );
        if (!$gtin_info->EOF) {
            if ($gtin_info->fields['pos_ean'] !== '' && $gtin_info->fields['pos_ean'] !== 'no') {
                $gtin = $gtin_info->fields['pos_ean'];
                $gtins .= '{"gtin":"' . $gtin . '"},';
                continue;
            }
        }
    }
    //Stock by Attributes to go here...

    //Simple products
    $gtin = zen_products_lookup($product['id'], 'products_ean');
    if ($gtin === '' || $gtin === 'no') {
        continue;
        } else {
        $gtins .= '{"gtin":"' . $gtin . '"},';
    }
}
$gtins = rtrim($gtins, ',');
if ($gtins === '') {
  echo "<!-- GCR skipped as no valid GTINs (EANS) -->\n";
} else { ?>
<!-- BEGIN GCR Opt-in Module Code -->
<script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer></script>
<script>
    window.renderOptIn = function() {
        window.gapi.load('surveyoptin', function() {
            window.gapi.surveyoptin.render(
                {
                    // REQUIRED
                    "merchant_id": <?= (int)GOOGLE_MERCHANT_CENTER_ID ?>,
                    "order_id": "<?= $order->info['order_id'] ?>",
                    "email": "<?= $order->customer['email_address'] ?>",
                    "delivery_country": "<?= $order->delivery['country']['iso_code_2'] ?>",
                    "estimated_delivery_date": "<?= $estimated_delivery_date ?>",
                    // OPTIONAL
                    "products": [<?= $gtins ?>],
                    "opt_in_style": "CENTER_DIALOG"
                });
        });
    }
</script>
<!-- END GCR Opt-in Module Code -->
<!-- BEGIN GCR Language Code en-GB, es -->
<script>
    window.___gcfg = {
        lang: '<?= $gcr_lang ?>'
    };
</script>
    <!-- END GCR Code -->
<?php } ?>
