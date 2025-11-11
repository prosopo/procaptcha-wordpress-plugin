import type {ExpectedResult, FormSubmitionSettings} from "@support/commands/submitForm";
import {Message} from "@support/form-test";
import {checkoutSubmissionResult as classicSubmissionResult} from "../classic/classicCheckout";

export const CHECKOUT_PAGE = "/checkout/";
export const CHECKOUT_FORM = ".wc-block-checkout__form";

export const checkoutFieldValues = {
    address_1: "Street",
    city: "City",
    country: "UA",
    email: "test@gmail.com",
    first_name: "John",
    last_name: "Doe",
    postcode: "69104",
    state: "UA23",
};

export const checkoutSubmissionResult = {
    successful: classicSubmissionResult.successful,
    failed: {
        element: {
            selector: ".wc-block-components-notice-banner.is-error",
            label: Message.VALIDATION_ERROR,
        },
    } as ExpectedResult,
};

const INTERCEPT_REQUEST_TIMEOUT_MS = 30_000;

export const submitCheckout = (settings: FormSubmitionSettings) => {
    // always ensure the checkout data is loaded -
    // otherwise, ".setBillingAddress()" will have no impact
    cy.get(".wc-block-components-product-price").should("exist");

    const captchaValue = settings.captchaValue || "";

    getWpData((wpData) => {
        const fieldValues = settings.fieldValues || {};

        // optionally, as authorized users have billing fields pre-populated.
        if (Object.keys(fieldValues).length > 0) {
            setInputValues(fieldValues, wpData);
        }

        if (captchaValue.length > 0) {
            setCaptchaValue(captchaValue, wpData);
        }

        cy.get(
            "button.wc-block-components-checkout-place-order-button",
        ).click();
    });
};

const getWpData = (callback: (wpData) => void) => cy.window()
    .should("have.property", "wp")
    .then((wp) => {
        cy.wrap(wp)
            .its("data")
            .should("exist")
            .then((wpData) => callback(wpData));
    });

const setInputValues = (inputValues: object, wpData) => {
    cy.intercept("POST", "/wp-json/wc/store/v1/batch*").as("cartData");

    wpData.dispatch("wc/store/cart").setBillingAddress(inputValues);

    cy.wait("@cartData", {timeout: INTERCEPT_REQUEST_TIMEOUT_MS});
};

const setCaptchaValue = (captchaValue: string, wpData) =>
    wpData.dispatch("wc/store/checkout").setAdditionalFields({
        "prosopo-procaptcha/prosopo_procaptcha": captchaValue,
    });
