import { Message } from "@support/form-test";
import { ForumForm } from "./forum-form";

class NewTopicForm extends ForumForm {
	defineSettings(): void {
		super.defineSettings();

		this.url = "/forums/forum/prosopo/";
		this.isAuthSupportedByVendor = true;
		this.selectors = {
			formWithCaptcha: ".bbp-topic-form",
			formWithoutCaptcha: ".bbp-topic-form",
			errorMessage: ".bbp-template-notice.error",
			successMessage: "",
			errorFieldMessage: "",
			captchaInput: "",
		};
		this.submitValues = {
			bbp_anonymous_name: "Tester",
			bbp_anonymous_email: "test@test.com",
			bbp_topic_title: "New",
			bbp_topic_content: "New",
		};
		this.messages = {
			success: "",
			fail: Message.VALIDATION_ERROR,
		};
	}

	protected checkSuccessfulSubmit(formSelector: string, userRole: string) {
		cy.url().should("include", "/forums/topic/");
	}

	protected toggleFeatureSupport(isActivation: boolean) {
		if (false === isActivation) {
			it("removePosts", () => {
				cy.removePosts({
					postType: "topic",
					countToRemove: 4,
					onlyIfTotal: 5,
				});
			});
		}

		super.toggleFeatureSupport(isActivation);
	}
}

export { NewTopicForm };
