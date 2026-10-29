import { WIDGET_EVENTS } from "#integrations/procaptcha/widgetEvents.js";

/**
 * Tokens can be verified only once, so forms that submit without a page reload
 * need a fresh token for the next attempt (e.g. after another field failed validation).
 * Resets the widgets after the submission response of each AJAX-based form plugin.
 *
 * Not covered, as their forms are re-rendered after the response:
 * Gravity Forms, Divi, Spectra (hides the form).
 */

type JQueryHandler = (event: JQuery.TriggeredEvent, ...args: unknown[]) => void;

const requestReset = (target: unknown): void => {
	// jQuery collections.
	if (
		null !== target &&
		"object" === typeof target &&
		"jquery" in target &&
		0 in target
	) {
		target = target[0];
	}

	if (target instanceof Element) {
		target.dispatchEvent(new Event(WIDGET_EVENTS.reset));
	}
};

const getRequestFields = (data: unknown): URLSearchParams | FormData | null => {
	if ("string" === typeof data) {
		return new URLSearchParams(data);
	}

	return data instanceof FormData ? data : null;
};

const getFieldString = (
	fields: URLSearchParams | FormData,
	name: string,
): string => {
	const value = fields.get(name);

	return "string" === typeof value ? value : "";
};

const getFormByHiddenInput = (name: string, value: string): Element | null => {
	return "" !== value
		? document
				.querySelector(
					`input[name="${CSS.escape(name)}"][value="${CSS.escape(value)}"]`,
				)
				?.closest("form") || null
		: null;
};

/**
 * Plugins without own events: matched by the AJAX action of the submission request.
 */
const resetAfterAjaxRequest = (fields: URLSearchParams | FormData): void => {
	const action = getFieldString(fields, "action");

	switch (action) {
		case "elementor_pro_forms_send_form":
			requestReset(
				getFormByHiddenInput(
					"form_id",
					getFieldString(fields, "form_id"),
				),
			);
			break;
		case "everest_forms_ajax_form_submission":
			requestReset(
				getFormByHiddenInput(
					"everest_forms[id]",
					getFieldString(fields, "everest_forms[id]"),
				),
			);
			break;
		case "fl_builder_email":
		case "fl_builder_subscribe_form_submit":
		case "fl_builder_login_form_submit": {
			const nodeId = getFieldString(fields, "node_id");

			if ("" !== nodeId) {
				requestReset(
					document.querySelector(
						`.fl-module[data-node="${CSS.escape(nodeId)}"]`,
					),
				);
			}
			break;
		}
	}
};

const bindJQueryEvents = ($: JQueryStatic): void => {
	const $document = $(document);
	const resetEventTarget: JQueryHandler = (event) =>
		requestReset(event.target);

	// Triggered on the form.
	$document.on("wpformsAjaxSubmitFailed", resetEventTarget);

	// Failure is triggered on the form, success on the body: both pass {form}.
	$document.on(
		"fluentform_submission_success fluentform_submission_failed",
		(event, data) => {
			const form =
				null !== data && "object" === typeof data && "form" in data
					? data.form
					: event.target;

			requestReset(form);
		},
	);

	$document.on("frmFormErrors", (_event, form) => requestReset(form));

	$document.on("nfFormSubmitResponse", (_event, data) => {
		const formId =
			null !== data && "object" === typeof data && "id" in data
				? String(data.id)
				: "";

		if ("" !== formId) {
			requestReset(document.getElementById(`nf-form-${formId}-cont`));
		}
	});

	$document.on(
		"jet-form-builder/ajax/on-success jet-form-builder/ajax/processing-error",
		(_event, _response, $form) => requestReset($form),
	);
	$document.on(
		"jet-form-builder/ajax/on-fail",
		(_event, _xhr, _status, _error, $form) => requestReset($form),
	);

	$document.on(
		"user_registration_frontend_after_ajax_complete",
		(_event, _response, _type, $form) => requestReset($form),
	);
	// AJAX login: passes the submit button. On success the page redirects.
	$document.on("user_registration_after_login_failed", (_event, $button) =>
		requestReset(
			null !== $button &&
				"object" === typeof $button &&
				"closest" in $button &&
				"function" === typeof $button.closest
				? $button.closest("form")
				: null,
		),
	);

	$(document.body).on("checkout_error", () =>
		requestReset(document.querySelector("form.checkout")),
	);

	$document.on("ajaxComplete", (_event, _xhr, settings) => {
		const fields = getRequestFields(settings?.data);

		if (null !== fields) {
			resetAfterAjaxRequest(fields);
		}
	});
};

// Blocksy has no events: the form's 'subscribe-loading' class is removed once the response is received.
const observeBlocksyNewsletterForms = (): void => {
	const forms = document.querySelectorAll(".ct-newsletter-subscribe-form");

	if (0 === forms.length) {
		return;
	}

	const observer = new MutationObserver((mutations) => {
		for (const mutation of mutations) {
			const form = mutation.target;

			if (
				form instanceof Element &&
				true === mutation.oldValue?.includes("subscribe-loading") &&
				false === form.classList.contains("subscribe-loading")
			) {
				requestReset(form);
			}
		}
	});

	forms.forEach((form) =>
		observer.observe(form, {
			attributes: true,
			attributeFilter: ["class"],
			attributeOldValue: true,
		}),
	);
};

const resetWidgetsAfterAjaxSubmissions = (): void => {
	// Native event, dispatched on the form after every response.
	document.addEventListener("wpcf7submit", (event) =>
		requestReset(event.target),
	);

	if ("function" === typeof window.jQuery) {
		bindJQueryEvents(window.jQuery);
	}

	observeBlocksyNewsletterForms();
};

export { resetWidgetsAfterAjaxSubmissions };
