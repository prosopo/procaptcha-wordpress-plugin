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

// Newer Jetpack renders the success state as "Thank you for your response. ✨"
// without the legacy #contact-form-success-header id. Match the body when it
// contains that text so we work on both old and new Jetpack templates.
const successMessageSelector = "body:contains('Thank you for your response')";

const submissionResult = {
    successful: {
        element: {
            selector: successMessageSelector,
            label: "Thank you for your response",
        },
    } as ExpectedResult,
    failed: {
        element: {
            // 'spammy' submissions are rejected before Jetpack renders any
            // success template, so no "Thank you for your response" appears.
            selector: successMessageSelector,
            shouldBeMissing: true,
        },
    } as ExpectedResult,
};

activatePluginsForTestLifetime(["jetpack"]);

describe("Protected contact form", () => {
    const page = "/jetpack-with-captcha/";
    const formSelector = "form:has(input.grunion-field)";

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
    const page = "/jetpack-without-captcha/";
    const formSelector = "form:has(input.grunion-field)";

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
