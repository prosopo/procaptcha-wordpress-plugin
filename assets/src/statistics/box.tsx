import * as React from "react";

interface Box {
	classes?: string;
	title: string;
	icon: string;
	children: React.ReactNode;
}

class BoxElement extends React.Component<Box> {
	render() {
		const { title, icon, children } = this.props;

		const classes = this.props.classes || "";

		return (
			<div
				className={`flex flex-col gap-7 rounded py-5 px-7 bg-white text-base ${classes}`}
			>
				<div className="flex items-center gap-1.5">
					<span className={`iconify w-6 h-6 ${icon}`}></span>
					<h2 className="font-medium">{title}</h2>
				</div>

				{children}
			</div>
		);
	}
}

export { BoxElement };