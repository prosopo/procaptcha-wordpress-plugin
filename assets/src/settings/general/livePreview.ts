import { swatchColor } from "./colorField.js";

interface AppearanceFields {
	theme: string;
	shape: string;
	font: string;
	palette: ReadonlyArray<{ key: string; field: string }>;
}

type FieldValues = ReadonlyMap<string, string>;

const SAFE_FONT_FAMILY_LIST = /^[\w\s,'"-]+$/;

const PREVIEW_DELAY_MS = 400;

// The widget options for the form's current values. Blank and invalid colours
// and fonts are left out, so the preview falls back to the theme default just
// as the saved settings would.
const previewAppearance = (
	values: FieldValues,
	fields: AppearanceFields,
): Record<string, unknown> => {
	const palette: Record<string, string> = {};

	for (const { key, field } of fields.palette) {
		const color = swatchColor(values.get(field) ?? "");

		if (null !== color) {
			palette[key] = color;
		}
	}

	const font = (values.get(fields.font) ?? "").trim();

	return {
		theme: values.get(fields.theme) ?? "light",
		shape: values.get(fields.shape) ?? "rounded",
		...(0 < Object.keys(palette).length && { palette }),
		...("" !== font && SAFE_FONT_FAMILY_LIST.test(font) && { font }),
	};
};

const formValues = (form: HTMLFormElement): FieldValues => {
	const values = new Map<string, string>();

	new FormData(form).forEach((value, name) => {
		if ("string" === typeof value) {
			values.set(name, value);
		}
	});

	return values;
};

// Saved settings reach the widget through procaptchaWpAttributes; the
// appearance keys are replaced with the form's, so a cleared colour or font
// really goes away in the preview.
const baseWidgetAttributes = (): Record<string, unknown> => {
	const attributes: Record<string, unknown> = {
		...(window.procaptchaWpAttributes ?? {}),
	};

	delete attributes.palette;
	delete attributes.font;

	return attributes;
};

const connectLivePreview = (
	root: Document | ShadowRoot,
	form: HTMLFormElement,
	fields: AppearanceFields,
): void => {
	let lastRendered = JSON.stringify(
		previewAppearance(formValues(form), fields),
	);
	let widgetId: string | undefined;
	let timer: ReturnType<typeof setTimeout> | undefined;

	const rerender = async (): Promise<void> => {
		const container = root.querySelector(
			"prosopo-procaptcha-wp-widget .prosopo-procaptcha",
		);
		const procaptcha = window.procaptcha;

		if (!(container instanceof HTMLElement) || undefined === procaptcha) {
			return;
		}

		const appearance = previewAppearance(formValues(form), fields);
		const serialised = JSON.stringify(appearance);

		if (serialised === lastRendered) {
			return;
		}

		lastRendered = serialised;

		// Before the first update the id of the widget drawn on page load is
		// unknown, but it is the only widget on the settings page.
		procaptcha.remove?.(widgetId);

		const id = await procaptcha.render(container, {
			...baseWidgetAttributes(),
			...appearance,
		});

		widgetId = "string" === typeof id ? id : undefined;
	};

	// The colour picker fires on every step of a drag, and each render opens a
	// new widget session, so wait for the choice to settle.
	const schedule = (): void => {
		clearTimeout(timer);
		timer = setTimeout(() => void rerender(), PREVIEW_DELAY_MS);
	};

	form.addEventListener("input", schedule);
	form.addEventListener("change", schedule);
};

export { connectLivePreview, previewAppearance };
export type { AppearanceFields };
