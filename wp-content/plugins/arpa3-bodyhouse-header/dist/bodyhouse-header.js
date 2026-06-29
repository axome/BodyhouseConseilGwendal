jQuery(document).ready(function() {

    const $headerAccountWidget = jQuery('#header_user_info');
    const $headerCartWidget = jQuery('.shopping_cart');
    const $headerShopWidget = jQuery('.header__actions__item--shop');
    const $headerCustomerLink = jQuery('.link-wrapper');

    fetch('/module/arpa3_bodyhouse_header/customer')
        .then((resp) => resp.json())
        .then((resp) => {

            if (resp.success === false) { 
                return;
            }

            if ($headerShopWidget.length && resp.selected_shop != null) {
                $headerShopWidget.find('.header__actions__item__label').text(resp.selected_shop);
            }
            
            if ($headerAccountWidget.length && resp.customer != null) {
                $headerAccountWidget.find('.header__actions__item__label').text(resp.customer);
                $headerAccountWidget.find('#sub_user').show();
            }

            if ($headerCartWidget.length) {
                $headerCartWidget.find('.ajax_cart_quantity').text(resp.cart.total);
            }

            if(resp.customer != null) {
                $headerCustomerLink.addClass('logged');
            }
        })
        .catch((error) => {
            console.error("Error:", error);
        });
});