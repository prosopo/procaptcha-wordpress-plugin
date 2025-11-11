import type {ExpectedResult, FormSubmitionSettings,} from "@support/commands/submitForm";
import {CaptchaValue, Message} from "@support/form-test";
import {activatePluginsForTestLifetime} from "@support/pluginsManagement";

const submitForm = (settings: FormSubmitionSettings) =>
    cy.submitForm({
        fieldValues: {
            "fl-name": "John Doe",
            "fl-email": "test@gmail.com",
            "fl-message": "Hey",
        },
        submitButtonSelector: "a.fl-button",
        ...settings,
    });

const submissionResult = {
    successful: {
        element: {
            selector: ".fl-success-msg",
            label: "Message Sent!",
        },
    } as ExpectedResult,
    failed: {
        element: {
            selector: ".fl-send-error",
            label: Message.VALIDATION_ERROR,
        },
    } as ExpectedResult,
};

activatePluginsForTestLifetime(["beaver-builder-plugin-starter-version"]);

describe("Protected contact form", () => {
    const page = "/beaver-contact-form/";
    const formSelector = ".fl-node-5pz83b20h9vw";

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
                captchaValue: CaptchaValue.INVALID,
                expectedResult: submissionResult.failed,
            }));

        it("can be submitted with right token", () =>
            submitForm({
                formSelector: formSelector,
                captchaValue: CaptchaValue.VALID,
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
    const page = "/beaver-contact-form/";
    const formSelector = ".fl-node-0q3pr1yu6754";

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
