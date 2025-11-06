import type {ExpectedResult, FormSubmitionSettings,} from "@support/commands/submitForm";
import {CaptchaValue} from "@support/form-test";
import {activatePluginsForTestLifetime} from "@support/pluginsManagement";
import {deleteAllFeedbacks} from "./feedbacks";

const submitForm = (settings: FormSubmitionSettings) =>
    cy.submitForm({
        fieldValues: {
            "input.name.grunion-field": "John Doe",
        },
        ...settings,
    });

const submissionResult = {
    successful: {
        element: {
            selector: "#contact-form-success-header",
            label: "Your message has been sent",
        },
    } as ExpectedResult,
    failed: {
        element: {
            // as per Jetpack v15.2 no error message for 'spammy' submission (which we mark as) is shown
            selector: "#contact-form-success-header",
            shouldBeHidden: true,
        },
    } as ExpectedResult,
};

activatePluginsForTestLifetime(["jetpack"]);

describe("Protected contact form", () => {
    const page = "/jetpack-with-captcha/";
    const formSelector = "form.wp-block-jetpack-contact-form";

    after(() => deleteAllFeedbacks());

    context("for guests", () => {
        beforeEach(() => cy.visit(page));

        it("has captcha", () =>
            cy.assertProcaptchaExistence(true, formSelector));

        it("can not be submitted without token", () =>
            submitForm({
                formSelector: formSelector,
                expectedResult: submissionResult.failed,
            }));

        it("can not be submitted with wrong token", () =>
            submitForm({
                formSelector: formSelector,
                captchaValue: CaptchaValue.WRONG,
                expectedResult: submissionResult.failed,
            }));

        it("can be submitted with right token", () =>
            submitForm({
                formSelector: formSelector,
                captchaValue: CaptchaValue.RIGHT,
                expectedResult: submissionResult.successful,
            }));
    });

    context("for authorized", () => {
        beforeEach(() => {
            cy.login();
            cy.visit(page);
        });

        it("does not have captcha", () =>
            cy.assertProcaptchaExistence(false, formSelector));

        it("can be submitted without token", () =>
            submitForm({
                formSelector: formSelector,
                expectedResult: submissionResult.successful,
            }));
    });
});

describe("Default contact form is not affected", () => {
    const page = "/jetpack-without-captcha/";
    const formSelector = "form.wp-block-jetpack-contact-form";

    after(() => deleteAllFeedbacks());

    context("for guests", () => {
        beforeEach(() => cy.visit(page));

        it("does not have captcha", () =>
            cy.assertProcaptchaExistence(false, formSelector));

        it("can be submitted without token", () =>
            submitForm({
                formSelector: formSelector,
                expectedResult: submissionResult.successful,
            }));
    });

    context("for authorized", () => {
        beforeEach(() => {
            cy.login();
            cy.visit(page);
        });

        it("does not have captcha", () =>
            cy.assertProcaptchaExistence(false, formSelector));

        it("can be submitted without token", () =>
            submitForm({
                formSelector: formSelector,
                expectedResult: submissionResult.successful,
            }));
    });
});
