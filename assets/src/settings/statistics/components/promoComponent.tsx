import * as React from "react";
import { type Link, LinkComponent } from "./linkComponent.js";

interface Promo {
	title: string;
	icon: string;
	items: Array<string>;
	actionLink: Link;
}

class PromoComponent extends React.Component<Promo> {
	public render() {
		const { title, icon, actionLink } = this.props;
		return (
			<div className="flex flex-col items-start gap-4">
				<div className="flex gap-2 items-center">
					<p className="text-lg">{title}</p>
					<span className={`${icon} w-7 h-7 bg-yellow-500`}></span>
				</div>
				<ul className="flex flex-col gap-1">
					{this.props.items.map((item, index) => (
						<li key={index} className="flex items-center gap-2">
							<div className="w-2 h-2 bg-indigo-800 shrink-0"></div>
							<p>{item}</p>
						</li>
					))}
				</ul>
				<LinkComponent {...actionLink} />
			</div>
		);
	}
}

export { PromoComponent };
