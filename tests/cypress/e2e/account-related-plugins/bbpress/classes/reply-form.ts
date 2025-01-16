import { Message } from "@support/form-test";
import { ForumForm } from "./forum-form";

class ReplyForm extends ForumForm {
	defineSettings(): void {
		super.defineSettings();

		this.url = "/forums/topic/procaptcha/";
		this.isAuthSupportedByVendor = true;
		this.selectors = {
			formWithCaptcha: ".bbp-reply-form",
			formWithoutCaptcha: ".bbp-reply-form",
			errorMessage: ".bbp-template-notice.error",
			successMessage: "",
			errorFieldMessage: "",
			captchaInput: "",
		};
		this.submitValues = {
			bbp_anonymous_name: "Tester",
			bbp_anonymous_email: "test@test.com",
			bbp_reply_content: "New",
		};
		this.messages = {
			success: "",
			fail: Message.VALIDATION_ERROR,
		};
	}

	protected checkSuccessfulSubmit(formSelector: string, userRole: string) {
		cy.url().should("include", "/forums/topic/procaptcha/#post-");
	}

	protected toggleFeatureSupport(isActivation: boolean) {
		if (false === isActivation) {
			it("removePosts", () => {
				cy.removePosts({
					postType: "reply",
					countToRemove: 4,
					onlyIfTotal: 4,
				});
			});
		}

		super.toggleFeatureSupport(isActivation);
	}
}

export { ReplyForm };
