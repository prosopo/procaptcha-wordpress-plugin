import React from "react";

interface LinkComponentProperties {
	label: string;
	href: string;
	icon?: string;
}

class LinkComponent extends React.Component<LinkComponentProperties> {
	public render(): React.ReactNode {
		const { label, href, icon } = this.props;

		return (
			<a
				target="_blank"
				rel="noreferrer"
				href={href}
				className="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded text-white bg-blue no-underline transition cursor-pointer
    hover:bg-blue-dark"
			>
				<span>{label}</span>
				{icon && <span className={`${icon} w-6 h-6`}></span>}
			</a>
		);
	}
}

export { LinkComponentProperties, LinkComponent };
