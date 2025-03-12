import * as React from "react";
import { SectionComponent } from "./sectionComponent.js";

interface ListItem {
	label: string;
	value: string;
}

interface List {
	title: string;
	icon: string;
	items: Array<ListItem>;
}

class ListComponent extends React.Component<List> {
	render() {
		const { title, icon, items } = this.props;

		return (
			<SectionComponent title={title} icon={icon}>
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
			</SectionComponent>
		);
	}
}

export { ListComponent, List };
