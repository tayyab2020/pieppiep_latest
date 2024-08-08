May need to add this "RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]" in prestashop root folder .htaccess file so /api endpoint is accessible

Product cards adjustment to show unit price including tax in products catalogues
file path: public_html/themes/classic/templates/catalog/_partials/miniatures/product.tpl

Module (PAP calculator) frontend CSS related adjustment for calculator
file path: public_html/modules/productareapacks/views/templates/front/widget_normal.tpl

Module (PAP calculator) adjustment to show calculation for Totaal on area change in case tax is 0
file path: public_html/modules/productareapacks/views/js/front/PAPFrontProductCoreController.js

Extend PAP webservice to access it as API. Create this entity in following path
file path: public_html/modules/productareapacks/src/Entity/pap_product.php

Add following line at top of the file
include_once(_PS_MODULE_DIR_.'/productareapacks/src/Entity/pap_product.php');

Add following line in this function (getResources) under $resources array of WebserviceRequest.php
'pap_product' => ['description' => 'Product Area Pack products', 'class' => 'pap_product']

file path: public_html/classes/webservice/WebserviceRequest.php

Than login to prestashop admin panel and go to this path "Advanced Parameters > Webservice" and enable "pap_product" permissions.