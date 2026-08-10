import type {FormSubmitionSettings} from "@support/commands/submitForm";
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

// Jetpack used to render an inline #contact-form-success-header element, but
// newer versions swap the form for a thank-you page accessed via the
// ?contact-form-sent=<id>&contact-form-hash=... query params. The query
// presence is a stable, version-agnostic success signal; the DOM template
// (legacy header vs. new "Thank you for your response. ✨") is not.
const assertSubmissionSucceeded = () =>
    cy.url().should("include", "contact-form-sent=");

const assertSubmissionRejected = () =>
    cy.url().should("not.include", "contact-form-sent=");

activatePluginsForTestLifetime(["jetpack"]);

describe("Protected contact form", () => {
    const page = "/jetpack-with-captcha/";
    const formSelector = "form:has(input.grunion-field)";

    after(() => deleteAllFeedbacks());

    context("for guests", () => {
        beforeEach(() => cy.visit(page));

        it("has captcha", () =>
            cy.assertProcaptchaExistence(true, formSelector));

        it("can not be submitted without token", () => {
            submitForm({formSelector: formSelector});
            assertSubmissionRejected();
        });

        it("can not be submitted with wrong token", () => {
            submitForm({
                formSelector: formSelector,
                captchaValue: CaptchaValue.INVALID,
            });
            assertSubmissionRejected();
        });

        it("can be submitted with right token", () => {
            submitForm({
                formSelector: formSelector,
                captchaValue: CaptchaValue.VALID,
            });
            assertSubmissionSucceeded();
        });
    });

    context("for authorized", () => {
        beforeEach(() => {
            cy.login();
            cy.visit(page);
        });

        it("does not have captcha", () =>
            cy.assertProcaptchaExistence(false, formSelector));

        it("can be submitted without token", () => {
            submitForm({formSelector: formSelector});
            assertSubmissionSucceeded();
        });
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

        it("can be submitted without token", () => {
            submitForm({formSelector: formSelector});
            assertSubmissionSucceeded();
        });
    });

    context("for authorized", () => {
        beforeEach(() => {
            cy.login();
            cy.visit(page);
        });

        it("does not have captcha", () =>
            cy.assertProcaptchaExistence(false, formSelector));

        it("can be submitted without token", () => {
            submitForm({formSelector: formSelector});
            assertSubmissionSucceeded();
        });
    });
});
