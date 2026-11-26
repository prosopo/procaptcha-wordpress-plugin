const SETTINGS_FORM_ID = "prosopo-procaptcha-settings-form";

// The form is saved by the Save button at the top of the page, so the page has
// to say when the fields no longer match what is saved.
const showUnsavedChanges = (root: Document | ShadowRoot): void => {
	const form = root.getElementById(SETTINGS_FORM_ID);
	const notice = root.querySelector("[data-settings-unsaved]");

	if (
		!(form instanceof HTMLFormElement) ||
		!(notice instanceof HTMLElement)
	) {
		return;
	}

	const show = (): void => {
		notice.hidden = false;
		root.querySelector("[data-settings-saved]")?.remove();
	};

	form.addEventListener("input", show, { once: true });
	form.addEventListener("change", show, { once: true });
};

const settingsRoot =
	document.querySelector("prosopo-procaptha-wp-settings")?.shadowRoot ??
	document;

showUnsavedChanges(settingsRoot);
