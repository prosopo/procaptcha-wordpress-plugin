import { WebComponent } from "#webComponent/webComponent.js";
import Logger from "#logger/logger.js";

class BeaverBuilderIntegrationComponent implements WebComponent {
	private readonly logger: Logger;

	constructor(logger: Logger) {
		this.logger = logger;
	}

	constructComponent(integrationElement: HTMLElement): void {
		const moduleElement = integrationElement.closest(".fl-module");

		if (moduleElement instanceof HTMLElement) {
			const moduleId = moduleElement.dataset["node"] || "";

			if (moduleId.length > 0) {
				const tokenFieldName = "procaptcha-response";

				this.bindAjaxRequestField(
					tokenFieldName,
					createTokenValueResolver(moduleElement, tokenFieldName),
					{
						action: /^fl_builder_.*$/,
						node_id: moduleId,
					},
				);
			} else {
				this.logger.warning("Cannot get module id", {
					moduleElement: moduleElement,
				});
			}

			return;
		}

		this.logger.warning("Cannot get module element", {
			integrationElement: integrationElement,
		});
	}

	protected bindAjaxRequestField(
		fieldName: string,
		getFieldValue: () => string,
		requestFilters: RequestFieldFilter,
	) {
		this.addAjaxRequestPrefilter((fields: URLSearchParams) => {
			if (isRequestMatching(fields, requestFilters)) {
				fields.set(fieldName, getFieldValue());
			}
		});
	}

	protected addAjaxRequestPrefilter(
		prefilterAjaxRequest: (requestFields: URLSearchParams) => void,
	): void {
		const $ = window.jQuery;

		if ("function" === typeof $) {
			$.ajaxPrefilter((options) => {
				const requestData = options.data;

				if ("string" === typeof requestData) {
					const requestFields = new URLSearchParams(requestData);

					prefilterAjaxRequest(requestFields);

					options.data = requestFields.toString();
				}
			});

			return;
		}

		this.logger.warning(
			"Cannot attach ajax prefilter: jQuery is not available",
		);
	}
}

type RequestFieldFilter = Record<string, string | RegExp>;

const isRequestMatching = (
	fields: URLSearchParams,
	filters: RequestFieldFilter,
) => {
	return Object.entries(filters).every(([filterName, filterValue]) => {
		const fieldValue = fields.get(filterName);

		return filterValue instanceof RegExp && "string" === typeof fieldValue
			? filterValue.test(fieldValue)
			: filterValue === fieldValue;
	});
};

const createTokenValueResolver = (
	element: HTMLElement,
	inputName: string,
): (() => string) => {
	let tokenEventValue = "";

	element.addEventListener("_prosopo-procaptcha__filled", (event) => {
		if (event instanceof CustomEvent) {
			tokenEventValue = event.detail.token;
		}
	});

	return () => {
		const tokenInput = element.querySelector(`input[name=${inputName}]`);

		return tokenInput instanceof HTMLInputElement
			? tokenInput.value
			: tokenEventValue;
	};
};

declare global {
	interface Window {
		jQuery?: typeof jQuery;
	}
}

export { BeaverBuilderIntegrationComponent };
