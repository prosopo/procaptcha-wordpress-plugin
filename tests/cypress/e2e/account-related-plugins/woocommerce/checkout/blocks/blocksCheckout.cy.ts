import {activatePluginsForTestLifetime} from "@support/pluginsManagement";
import {CaptchaValue} from "@support/form-test";
import {
    addProductToCart,
    deleteOrders,
    PURCHASE_PRODUCT_ID,
    toggleCheckoutProtection
} from "../classic/classicCheckout";
import {
    CHECKOUT_FORM,
    CHECKOUT_PAGE,
    checkoutFieldValues,
    checkoutSubmissionResult,
    submitCheckout
} from "./blocksCheckout";

activatePluginsForTestLifetime(["woocommerce"]);

describe("default checkout", () => {
    after(() => deleteOrders(2));

    context("for guests", () => {
        beforeEach(() => {
            addProductToCart(PURCHASE_PRODUCT_ID);

            cy.visit(CHECKOUT_PAGE);
        });

        it("should not have captcha", () =>
            cy.assertProcaptchaExistence(false, CHECKOUT_FORM));

        it("should allow submission without token", () =>
            submitCheckout({
                formSelector: CHECKOUT_FORM,
                fieldValues: checkoutFieldValues,
                expectedResult: checkoutSubmissionResult.successful,
            }));
    });

    context("for authorized", () => {
        beforeEach(() => {
            cy.login();

            addProductToCart(PURCHASE_PRODUCT_ID);

            cy.visit(CHECKOUT_PAGE);
        });

        it("should not have captcha", () =>
            cy.assertProcaptchaExistence(false, CHECKOUT_FORM));

        it("should allow submission without token", () =>
            submitCheckout({
                formSelector: CHECKOUT_FORM,
                // authorized users have billing values pre-populated.
                expectedResult: checkoutSubmissionResult.successful,
            }));
    });
});

describe("protected checkout", () => {
    before(() => {
        toggleCheckoutProtection(true);
    });

    after(() => {
        toggleCheckoutProtection(false);

        deleteOrders(2);
    });

    context("for guests", () => {
        beforeEach(() => {
            addProductToCart(PURCHASE_PRODUCT_ID);

            cy.visit(CHECKOUT_PAGE);
        });

        it("should have captcha", () =>
            cy.assertProcaptchaExistence(true, CHECKOUT_FORM));

        it("should not allow submission without token", () =>
            submitCheckout({
                formSelector: CHECKOUT_FORM,
                fieldValues: checkoutFieldValues,
                expectedResult: checkoutSubmissionResult.failed,
            }));

        it("should not allow submission with the wrong token", () =>
            submitCheckout({
                formSelector: CHECKOUT_FORM,
                fieldValues: checkoutFieldValues,
                captchaValue: CaptchaValue.INVALID,
                expectedResult: checkoutSubmissionResult.failed,
            }));

        it("should allow submission with the right token", () =>
            submitCheckout({
                formSelector: CHECKOUT_FORM,
                fieldValues: checkoutFieldValues,
                captchaValue: CaptchaValue.VALID,
                expectedResult: checkoutSubmissionResult.successful,
            }));
    });


    context("for authorized", () => {
        beforeEach(() => {
            cy.login();

            addProductToCart(PURCHASE_PRODUCT_ID);

            cy.visit(CHECKOUT_PAGE);
        });

        // Block Checkout doesn't support auth-detection (within validation),
        // so we expect the same behavior as for guests.

        it("should have captcha", () =>
            cy.assertProcaptchaExistence(true, CHECKOUT_FORM));

        it("should not allow submission without token", () =>
            submitCheckout({
                formSelector: CHECKOUT_FORM,
                // authorized users have billing values pre-populated.
                expectedResult: checkoutSubmissionResult.failed,
            }));

        it("should not allow submission with the wrong token", () =>
            submitCheckout({
                formSelector: CHECKOUT_FORM,
                // authorized users have billing values pre-populated.
                captchaValue: CaptchaValue.INVALID,
                expectedResult: checkoutSubmissionResult.failed,
            }));

        it("should allow submission with the right token", () =>
            submitCheckout({
                formSelector: CHECKOUT_FORM,
                // authorized users have billing values pre-populated.
                captchaValue: CaptchaValue.VALID,
                expectedResult: checkoutSubmissionResult.successful,
            }));
    });
});
