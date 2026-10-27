import * as React from "react";
import CaptchaUsageNumberUtils from "./captchaUsageNumberUtils.js";
import type { UsageLabels } from "#settings/statistics/config.js";
import { SectionComponent } from "#settings/statistics/components/sectionComponent.js";
import type { CaptchaUsage } from "#settings/procaptcha/procaptchaSite.js";

interface CaptchaUsageComponentProperties {
	limits: {
		verifications: number;
	};
	image: CaptchaUsage;
	pow: CaptchaUsage;
	puzzle?: CaptchaUsage;
	iconOrder?: CaptchaUsage;
	audio?: CaptchaUsage;
	labels: UsageLabels;
	numberUtils: CaptchaUsageNumberUtils;
}

interface UsageRow {
	label: string;
	colorClass: string;
	verifications: number;
}

class CaptchaUsageComponent extends React.Component<CaptchaUsageComponentProperties> {
	// Free plans can be served PoW, image and jigsaw puzzle challenges, so those always
	// show; icon order and audio are paid-plan types and only show once used.
	protected getRows(): UsageRow[] {
		const { labels, pow, image, puzzle, iconOrder, audio } = this.props;

		const rows: UsageRow[] = [
			{
				label: labels.proofOfWork,
				colorClass: "bg-green-500",
				verifications: pow.verifications,
			},
			{
				label: labels.image,
				colorClass: "bg-yellow-500",
				verifications: image.verifications,
			},
			{
				label: labels.puzzle,
				colorClass: "bg-pink",
				verifications: puzzle?.verifications ?? 0,
			},
		];

		if (iconOrder && iconOrder.verifications > 0) {
			rows.push({
				label: labels.iconOrder,
				colorClass: "bg-purple",
				verifications: iconOrder.verifications,
			});
		}

		if (audio && audio.verifications > 0) {
			rows.push({
				label: labels.audio,
				colorClass: "bg-sky-500",
				verifications: audio.verifications,
			});
		}

		return rows;
	}

	render() {
		const numberUtils = this.props.numberUtils;
		const limit = this.props.limits.verifications;
		const rows = this.getRows();
		const totalVerifications = rows.reduce(
			(sum: number, row: UsageRow): number => sum + row.verifications,
			0,
		);

		const commonPercent = numberUtils.getPercent(totalVerifications, limit);

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
								{numberUtils.formatNumber(totalVerifications)} /{" "}
								{numberUtils.formatNumber(limit)} (
								{commonPercent}%)
							</p>
						</div>

						<div className="bg-gray-light rounded overflow-hidden h-2">
							<div
								className="h-full flex"
								style={{
									width: `${numberUtils.visualizePercent(commonPercent)}%`,
								}}
							>
								{rows.map((row: UsageRow) => (
									<div
										key={row.label}
										className={`h-full ${row.colorClass}`}
										style={{
											width: `${numberUtils.visualizePercent(
												numberUtils.getPercent(
													row.verifications,
													totalVerifications,
												),
											)}%`,
										}}
									></div>
								))}
							</div>
						</div>
					</div>

					{/*Legend*/}
					<div className="flex flex-col gap-1">
						{rows.map((row: UsageRow) => (
							<div
								key={row.label}
								className="flex items-center gap-2"
							>
								<div
									className={`${row.colorClass} w-2 h-2`}
								></div>
								<p>{row.label}</p>
								<p>
									{numberUtils.formatNumber(
										row.verifications,
									)}{" "}
									(
									{numberUtils.getPercent(
										row.verifications,
										totalVerifications,
									)}
									%)
								</p>
							</div>
						))}
					</div>
				</div>
			</SectionComponent>
		);
	}
}

export { CaptchaUsageComponent, CaptchaUsageComponentProperties };
