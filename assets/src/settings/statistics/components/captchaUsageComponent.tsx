import * as React from "react";
import { UsageLabels } from "../config.js";
import { SectionComponent } from "./sectionComponent.js";
import NumberUtils from "../numberUtils.js";

interface CaptchaUsage {
	limits: {
		verifications: number;
	};
	image: {
		submissions: number;
		verifications: number;
		total: number;
	};
	pow: {
		submissions: number;
		verifications: number;
		total: number;
	};
	labels: UsageLabels;
	numberUtils: NumberUtils;
}

class CaptchaUsageComponent extends React.Component<CaptchaUsage> {
	render() {
		const numberUtils = this.props.numberUtils;
		const limit = this.props.limits.verifications;
		const totalSubmissions =
			this.props.image.verifications + this.props.pow.verifications;

		const commonPercent = numberUtils.getPercent(totalSubmissions, limit);
		const powPercent = numberUtils.getPercent(
			this.props.pow.verifications,
			totalSubmissions,
		);
		const imagePercent = numberUtils.getPercent(
			this.props.image.verifications,
			totalSubmissions,
		);

		const labels = this.props.labels;

		return (
			<SectionComponent
				title={labels.title}
				icon="icon-[eos-icons--quota]"
			>
				<div className="flex flex-col gap-10">
					{/*Progress bar*/}
					<div className="flex flex-col gap-2">
						<div className="flex justify-between">
							<p className="text-gray">{labels.total}</p>
							<p className="font-medium">
								{numberUtils.formatNumber(totalSubmissions)} /{" "}
								{numberUtils.formatNumber(limit)} (
								{commonPercent}%)
							</p>
						</div>

						<div className="bg-gray rounded overflow-hidden h-2">
							<div
								className="h-full flex"
								style={{
									width: `${numberUtils.visualizePercent(commonPercent)}%`,
								}}
							>
								<div
									className="h-full bg-green-500"
									style={{
										width: `${numberUtils.visualizePercent(powPercent)}%`,
									}}
								></div>
								<div
									className="h-full bg-yellow-500"
									style={{
										width: `${numberUtils.visualizePercent(imagePercent)}%`,
									}}
								></div>
							</div>
						</div>
					</div>

					{/*Legend*/}
					<div className="flex flex-col gap-1">
						<div className="flex items-center gap-2">
							<div className="bg-green-500 w-2 h-2"></div>
							<p>{labels.proofOfWork}</p>
							<p className="">
								{numberUtils.formatNumber(
									this.props.pow.verifications,
								)}{" "}
								({powPercent}%)
							</p>
						</div>

						<div className="flex items-center gap-2">
							<div className="bg-yellow-500 w-2 h-2"></div>
							<p>{labels.image}</p>
							<p className="">
								{numberUtils.formatNumber(
									this.props.image.verifications,
								)}{" "}
								({imagePercent}%)
							</p>
						</div>
					</div>
				</div>
			</SectionComponent>
		);
	}
}

export { CaptchaUsageComponent, CaptchaUsage };
