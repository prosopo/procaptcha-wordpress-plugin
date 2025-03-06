import * as React from "react";
import { BoxElement } from "./box";

interface InfoBox {
	title: string;
	icon: string;
	items: Array<InfoBoxItem>;
}

interface InfoBoxItem {
	label: string;
	value: string;
}

class InfoBoxElement extends React.Component<InfoBox> {
	render() {
		const { title, icon, items } = this.props;

		return (
			<BoxElement title={title} icon={icon}>
				<div className="grid grid-cols-2">
					{items.map((item, index) => (
						<React.Fragment key={index}>
							<p className="py-3 border-solid border-b border-gray/50">
								{item.label}
							</p>
							<p className="py-3 border-solid border-b border-gray/50 font-medium">
								{item.value}
							</p>
						</React.Fragment>
					))}
				</div>
			</BoxElement>
		);
	}
}

export { InfoBox, InfoBoxElement };
