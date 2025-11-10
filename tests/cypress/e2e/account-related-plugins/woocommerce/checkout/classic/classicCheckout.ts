import type {ExpectedResult, FormSubmitionSettings} from "@support/commands/submitForm";
import {Message} from "@support/form-test";
import {toggleProcaptchaOption} from "@support/options";

export const CHECKOUT_PAGE = "/checkout-classic/";
export const CHECKOUT_FORM = "form.woocommerce-checkout";
export const PURCHASE_PRODUCT_ID = 591;

export const submitCheckout = (settings: FormSubmitionSettings) =>
    cy.submitForm({
        fieldValues: {
            billing_first_name: "John",
            billing_last_name: "Doe",
            billing_address_1: "Street",
            billing_country: "UA",
            billing_city: "City",
            billing_state: "UA23",
            billing_postcode: "69104",
            billing_email: "test@gmail.com",
        },
        ...settings,
    });

export const checkoutSubmissionResult = {
    successful: {
        element: {
            selector: ".wc-block-order-confirmation-status",
            label: "Order received",
        },
    } as ExpectedResult,
    failed: {
        element: {
            selector: ".wc-block-components-notice-banner__content",
            label: Message.VALIDATION_ERROR,
        },
    } as ExpectedResult,
};

export const addProductToCart = (productId: number) => {
    // Do not add "?add-to-card" directly to the primary url,
    // as it causes the "Sorry, your session has expired" issue:
    // https://github.com/woocommerce/woocommerce-gateway-stripe/issues/551
    cy.visit(`/cart/?add-to-cart=${productId}`);

    cy.get(".wc-block-components-notice-banner__content").should(
        "include.text",
        "has been added to your cart",
    );
};

export const deleteOrders = (count: number) => {
    cy.login();

    cy.visit("/wp-admin/admin.php?page=wc-orders");

    for (let i = 0; i < count; i++) {
        cy.get('.type-shop_order .check-column input[type="checkbox"]')
            .eq(i)
            .check();
    }

    cy.get("#bulk-action-selector-top").select("trash");
    cy.get("#doaction").click();

    cy.visit("/wp-admin/admin.php?page=wc-orders&status=trash");

    cy.get("#delete_all").click();
}

export const toggleCheckoutProtection = (isEnabled: boolean) => toggleProcaptchaOption(
    "woocommerce",
    "is_on_checkout",
    isEnabled,
);
