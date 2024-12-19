import WordpressForm from "./wordpress-form";
import { Message, Role } from "@support/form-test";

class CommentForm extends WordpressForm {
	protected defineSettings(): void {
		super.defineSettings();

		this.isAuthSupportedByVendor = true;
		this.selectors = {
			formWithCaptcha: ".wp-block-comments",
			formWithoutCaptcha: ".wp-block-comments",
			errorMessage: ".wp-die-message",
			successMessage: ".comment-awaiting-moderation",
			errorFieldMessage: "",
			captchaInput: "",
		};
		this.submitValues = {
			author: "Cypress",
			email: "test@test.com",
			comment: "new comment",
		};
		this.messages = {
			success:
				"Your comment is awaiting moderation. This is a preview; your comment will be visible after it has been approved.",
			fail: Message.VALIDATION_ERROR,
		};
	}

	protected getSettingName(): string {
		return "is_on_wp_comment_form";
	}

	// It's a wp_die() message, so the selector is global.
	protected getFailSubmitMessageSelector(): string {
		return this.selectors.errorMessage;
	}

	protected getSubmitValues(userRole: string): object {
		let submitValues = super.getSubmitValues(userRole);

		if (Role.USER === userRole) {
			submitValues = { ...submitValues };
			// These fields aren't present for authorized users.
			delete submitValues["email"];
			delete submitValues["author"];
		}

		return submitValues;
	}

	protected checkSuccessfulSubmit(formSelector: string, userRole: string) {
		if (Role.USER !== userRole) {
			super.checkSuccessfulSubmit(formSelector, userRole);
			return;
		}

		// Success message is not present for authorized users.
		cy.url().should("include", "#comment-");
	}

	protected afterScenario(): void {
		super.afterScenario();

		it("deleteCreatedComments", () => {
			this.integrationTest.login();

			cy.visit("/wp-admin/edit-comments.php?comment_status=all");
			cy.get("#cb-select-all-1").check();
			cy.get("#bulk-action-selector-bottom").select("trash");
			cy.get("#doaction2").click();

			cy.visit("/wp-admin/edit-comments.php?comment_status=trash");
			cy.get("#cb-select-all-1").check();
			cy.get("#bulk-action-selector-bottom").select("delete");
			cy.get("#delete_all").click();
		});
	}
}

export { CommentForm };
