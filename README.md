# Zen Cart - Google Customer Reviews

## Function
Google Customer Reviews shows a pop-up on the checkout_sucess page asking if they can send a post-purchase enquiry to post a review.


IMPORTANT: Google Customer Reviews only works with products that have a GTIN (barcode, ISBN, EAN etc.). It does not accept MPN.

So, you need to have added such a field in your database...which is a usual step when implementing Google Merchant Center, so you should do that first: 
https://github.com/lat9/gpsf

Google Customer Reviews:  
https://support.google.com/merchants/answer/7106244?hl=en&sjid=14045442446867807935-EU&visit_id=638482730858912221-1270039360&ref_topic=7105160&rd=1

## Installation
Add the section of code to the end of includes/templates/YOUR/TEMPLATE/templates/tpl_checkout_success_default.php
