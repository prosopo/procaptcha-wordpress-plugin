import * as React from "react";
import type CaptchaUsageNumberUtils from "#settings/statistics/captchaUsage/captchaUsageNumberUtils.js";
import type { DailyRequests } from "#settings/procaptcha/procaptchaSite.js";
import type { InsightsLabels } from "#settings/statistics/config.js";
import { SectionComponent } from "#settings/statistics/components/sectionComponent.js";
import { fillTemplate } from "./fillTemplate.js";

interface DailyRequestsComponentProperties {
	days: DailyRequests[];
	used: number;
	limit: number;
	now: Date;
	labels: InsightsLabels;
	numberUtils: CaptchaUsageNumberUtils;
	classes?: string;
}

interface DailyRequestsState {
	hovered: number | null;
	showTable: boolean;
	width: number;
}

// Drawn at the container's real pixel width, so bar and text sizes are true
// pixels rather than scaled with the card.
const DEFAULT_WIDTH = 720;
const PLOT_HEIGHT = 160;
const AXIS_HEIGHT = 24;
const GUTTER = 44;
const BAR_MAX_WIDTH = 16;
const RADIUS = 4;
// Selective x labels: the start of each week, never every day.
const LABELLED_DAYS = [1, 8, 15, 22, 29];

const niceCeiling = (value: number): number => {
	if (value <= 0) {
		return 1;
	}
	const magnitude = 10 ** Math.floor(Math.log10(value));
	const step = [1, 1.2, 1.5, 2, 2.5, 3, 4, 5, 6, 8, 10].find(
		(candidate: number): boolean => candidate * magnitude >= value,
	);
	return (step ?? 10) * magnitude;
};

// A column with a rounded data end and a square foot on the baseline.
const columnPath = (
	x: number,
	top: number,
	width: number,
	base: number,
): string => {
	const radius = Math.min(RADIUS, width / 2, base - top);
	return [
		`M${x},${base}`,
		`V${top + radius}`,
		`Q${x},${top} ${x + radius},${top}`,
		`H${x + width - radius}`,
		`Q${x + width},${top} ${x + width},${top + radius}`,
		`V${base}`,
		"Z",
	].join(" ");
};

class DailyRequestsComponent extends React.Component<
	DailyRequestsComponentProperties,
	DailyRequestsState
> {
	public state: DailyRequestsState = {
		hovered: null,
		showTable: false,
		width: DEFAULT_WIDTH,
	};

	private readonly container = React.createRef<HTMLDivElement>();
	private resizeObserver: ResizeObserver | null = null;

	public componentDidMount(): void {
		const element = this.container.current;
		if (null === element) {
			return;
		}
		this.resizeObserver = new ResizeObserver(
			(entries: ResizeObserverEntry[]): void => {
				const width = Math.round(entries[0]?.contentRect.width ?? 0);
				if (width > 0 && width !== this.state.width) {
					this.setState({ width });
				}
			},
		);
		this.resizeObserver.observe(element);
	}

	public componentWillUnmount(): void {
		this.resizeObserver?.disconnect();
	}

	protected formatDay(day: string, withWeekday = false): string {
		return new Intl.DateTimeFormat(document.documentElement.lang || "en", {
			...(withWeekday && { weekday: "short" }),
			day: "numeric",
			month: "short",
		}).format(new Date(`${day}T00:00:00`));
	}

	render() {
		const { days, used, limit, now, labels, numberUtils } = this.props;
		const { hovered, showTable, width: chartWidth } = this.state;

		const daysInMonth = new Date(
			now.getFullYear(),
			now.getMonth() + 1,
			0,
		).getDate();
		const elapsed = Math.max(1, now.getDate());
		const forecast = Math.round((used / elapsed) * daysInMonth);
		const overLimit = limit > 0 && forecast > limit;

		const byDayOfMonth = new Map<number, DailyRequests>(
			days.map((entry: DailyRequests): [number, DailyRequests] => [
				new Date(`${entry.day}T00:00:00`).getDate(),
				entry,
			]),
		);
		const yMax = niceCeiling(
			Math.max(0, ...days.map((entry: DailyRequests) => entry.requests)),
		);
		const plotWidth = chartWidth - GUTTER;
		const slot = plotWidth / daysInMonth;
		const barWidth = Math.min(BAR_MAX_WIDTH, slot - 2);
		const yOf = (value: number): number =>
			PLOT_HEIGHT - (value / yMax) * (PLOT_HEIGHT - 8);

		const hoveredEntry =
			hovered === null ? undefined : byDayOfMonth.get(hovered);
		const usedPercent = Math.min(100, numberUtils.getPercent(used, limit));

		return (
			<SectionComponent
				title={labels.dailyTitle}
				icon="icon-[material-symbols--bar-chart]"
				classes={this.props.classes || ""}
			>
				<div className="flex flex-col gap-5">
					<div className="relative" ref={this.container}>
						<svg
							viewBox={`0 0 ${chartWidth} ${PLOT_HEIGHT + AXIS_HEIGHT}`}
							width={chartWidth}
							height={PLOT_HEIGHT + AXIS_HEIGHT}
							className="block"
							role="img"
							aria-label={labels.dailyTitle}
						>
							{[0, yMax / 2, yMax].map((tick: number) => (
								<g key={tick}>
									<line
										x1={GUTTER}
										x2={chartWidth}
										y1={yOf(tick)}
										y2={yOf(tick)}
										stroke="#e5e4ee"
										strokeWidth={1}
									/>
									<text
										x={GUTTER - 8}
										y={yOf(tick) + 4}
										textAnchor="end"
										fontSize={12}
										fill="#8c8f94"
									>
										{numberUtils.formatNumber(tick)}
									</text>
								</g>
							))}

							{Array.from(
								{ length: daysInMonth },
								(_unused: unknown, index: number) => index + 1,
							).map((dayOfMonth: number) => {
								const entry = byDayOfMonth.get(dayOfMonth);
								const x =
									GUTTER +
									(dayOfMonth - 1) * slot +
									(slot - barWidth) / 2;
								const isHovered = hovered === dayOfMonth;

								return (
									<g key={dayOfMonth}>
										{entry && entry.requests > 0 && (
											<path
												d={columnPath(
													x,
													yOf(entry.requests),
													barWidth,
													PLOT_HEIGHT,
												)}
												fill={
													isHovered
														? "#332c67"
														: "#4e439f"
												}
											/>
										)}
										{LABELLED_DAYS.includes(dayOfMonth) && (
											<text
												x={x + barWidth / 2}
												y={PLOT_HEIGHT + 17}
												textAnchor="middle"
												fontSize={12}
												fill="#8c8f94"
											>
												{dayOfMonth}
											</text>
										)}
										{entry && (
											<rect
												x={
													GUTTER +
													(dayOfMonth - 1) * slot
												}
												y={0}
												width={slot}
												height={PLOT_HEIGHT}
												fill="transparent"
												tabIndex={0}
												aria-label={`${this.formatDay(entry.day)}: ${numberUtils.formatNumber(entry.requests)}`}
												onMouseEnter={() =>
													this.setState({
														hovered: dayOfMonth,
													})
												}
												onFocus={() =>
													this.setState({
														hovered: dayOfMonth,
													})
												}
												onMouseLeave={() =>
													this.setState({
														hovered: null,
													})
												}
												onBlur={() =>
													this.setState({
														hovered: null,
													})
												}
												style={{ outline: "none" }}
											/>
										)}
									</g>
								);
							})}
						</svg>

						{hoveredEntry && hovered !== null && (
							<div
								className="absolute pointer-events-none rounded bg-white shadow-md py-2 px-3 text-sm"
								style={{
									left: `${((GUTTER + (hovered - 0.5) * slot) / chartWidth) * 100}%`,
									top: 0,
									transform: "translateX(-50%)",
								}}
							>
								<p className="m-0 font-semibold text-navy">
									{numberUtils.formatNumber(
										hoveredEntry.requests,
									)}
								</p>
								<p className="m-0 text-gray">
									{this.formatDay(hoveredEntry.day)}
								</p>
							</div>
						)}
					</div>

					<div className="flex flex-col gap-2">
						<div className="flex justify-between gap-4">
							<p className="m-0">
								{fillTemplate(labels.dailyUsed, {
									used: numberUtils.formatNumber(used),
									limit: numberUtils.formatNumber(limit),
								})}
							</p>
							<p className="m-0 font-medium">
								{Math.round(usedPercent)}%
							</p>
						</div>
						<div
							className="h-2 rounded-full bg-purple-light overflow-hidden"
							role="meter"
							aria-valuemin={0}
							aria-valuemax={limit}
							aria-valuenow={used}
						>
							<div
								className={`h-full rounded-full ${overLimit ? "bg-warning" : "bg-blue"}`}
								style={{ width: `${usedPercent}%` }}
							></div>
						</div>
						<p
							className={`m-0 flex items-center gap-1.5 ${overLimit ? "text-warning-dark" : "text-gray"}`}
						>
							{overLimit && (
								<span className="icon-[material-symbols--warning-outline] w-4 h-4 shrink-0"></span>
							)}
							{fillTemplate(
								overLimit
									? labels.dailyForecastOver
									: labels.dailyForecast,
								{
									forecast:
										numberUtils.formatNumber(forecast),
									limit: numberUtils.formatNumber(limit),
								},
							)}
						</p>
					</div>

					<div className="flex flex-col items-start gap-3">
						<button
							type="button"
							onClick={() =>
								this.setState({ showTable: !showTable })
							}
							className="underline cursor-pointer bg-transparent [border:none] p-0 text-blue transition hover:text-blue-dark"
						>
							{showTable ? labels.hideTable : labels.showTable}
						</button>
						{showTable && (
							<div className="w-full max-w-md max-h-80 overflow-y-auto rounded border border-solid border-gray-light">
								<table className="w-full text-sm border-collapse">
									<thead className="sticky top-0 bg-purple-light text-navy">
										<tr>
											<th className="text-left font-semibold py-2 px-4">
												{labels.dayColumn}
											</th>
											<th className="text-right font-semibold py-2 px-4">
												{labels.requestsColumn}
											</th>
										</tr>
									</thead>
									<tbody>
										{days.map((entry: DailyRequests) => (
											<tr
												key={entry.day}
												className="border-0 border-t border-solid border-gray-light"
											>
												<td className="py-1.5 px-4">
													{this.formatDay(
														entry.day,
														true,
													)}
												</td>
												<td className="py-1.5 px-4 text-right tabular-nums">
													{numberUtils.formatNumber(
														entry.requests,
													)}
												</td>
											</tr>
										))}
									</tbody>
									<tfoot className="sticky bottom-0 bg-white">
										<tr className="border-0 border-t border-solid border-gray">
											<td className="py-2 px-4 font-semibold">
												{labels.totalRow}
											</td>
											<td className="py-2 px-4 text-right font-semibold tabular-nums">
												{numberUtils.formatNumber(
													days.reduce(
														(
															sum: number,
															entry: DailyRequests,
														): number =>
															sum +
															entry.requests,
														0,
													),
												)}
											</td>
										</tr>
									</tfoot>
								</table>
							</div>
						)}
					</div>
				</div>
			</SectionComponent>
		);
	}
}

export { DailyRequestsComponent };
