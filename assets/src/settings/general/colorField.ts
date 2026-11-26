const HEX_COLOR = /^#(?:[0-9a-f]{3}|[0-9a-f]{6})$/i;

// What the swatch shows for the text field's value: the picker only takes
// six-digit lowercase hex, and anything that is not a hex code means the
// theme's own color.
const swatchColor = (value: string): string | null => {
	const trimmed = value.trim();

	if (!HEX_COLOR.test(trimmed)) {
		return null;
	}

	const hex =
		4 === trimmed.length
			? `#${trimmed[1]}${trimmed[1]}${trimmed[2]}${trimmed[2]}${trimmed[3]}${trimmed[3]}`
			: trimmed;

	return hex.toLowerCase();
};

const connectColorField = (field: HTMLElement): void => {
	const picker = field.querySelector("[data-color-picker]");
	const swatch = field.querySelector("[data-color-swatch]");
	const value = field.querySelector("[data-color-value]");
	const clear = field.querySelector("[data-color-clear]");

	if (
		!(picker instanceof HTMLInputElement) ||
		!(swatch instanceof HTMLElement) ||
		!(value instanceof HTMLInputElement)
	) {
		return;
	}

	const showValue = (): void => {
		const color = swatchColor(value.value);

		swatch.style.backgroundColor = color ?? "";

		if (null !== color) {
			picker.value = color;
		}
	};

	picker.addEventListener("input", () => {
		value.value = picker.value;
		showValue();
	});

	value.addEventListener("input", showValue);

	clear?.addEventListener("click", () => {
		value.value = "";
		showValue();
	});

	showValue();
};

const connectColorFields = (root: ParentNode): void => {
	root.querySelectorAll("[data-color-field]").forEach((field) => {
		if (field instanceof HTMLElement) {
			connectColorField(field);
		}
	});
};

export { connectColorFields, swatchColor };
