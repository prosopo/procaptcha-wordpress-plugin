/**
 * Public events, documented for third-party code.
 * Filled and cleared are dispatched on the widget element and bubble up to the form.
 * Reset can be dispatched on the widget, its form or any other ancestor, or the document to reset every widget.
 */
const WIDGET_EVENTS = {
	// detail: { token: string }
	filled: "prosopo-procaptcha:filled",
	cleared: "prosopo-procaptcha:cleared",
	reset: "prosopo-procaptcha:reset",
} as const;

// Dispatched alongside the public "filled" event, for code written against plugin versions before 1.24.0.
const LEGACY_FILLED_EVENT = "_prosopo-procaptcha__filled";

export { WIDGET_EVENTS, LEGACY_FILLED_EVENT };
