// Translated labels carry named placeholders ("{used} of {limit}") because
// word order differs between languages.
export const fillTemplate = (
	template: string,
	values: Record<string, string>,
): string =>
	template.replace(
		/\{(\w+)\}/g,
		(match: string, key: string): string => values[key] ?? match,
	);
