class NumberUtils {
	protected roundToTwoDecimals(value: number): number {
		return Math.round(value * 100) / 100;
	}

	public getPercent(value: number, total: number): number {
		if (0 === value || 0 === total) {
			return 0;
		}

		return this.roundToTwoDecimals((value / total) * 100);
	}

	// Without it, the tiny percents just invisible.
	public visualizePercent(percent: number): number {
		return percent > 0 && percent < 3 ? 3 : percent;
	}

	public formatNumber(value: number): string {
		return new Intl.NumberFormat("en-GB").format(value);
	}
}

export default NumberUtils;
