class Collection {
	private data: Record<string, unknown>;

	constructor(data: Record<string, unknown>) {
		this.data = data;
	}

	public getString(key: string): string {
		return true === this.data.hasOwnProperty(key) &&
			"string" === typeof this.data[key]
			? (this.data[key] as string)
			: "";
	}

	public getNumber(key: string): number {
		return true === this.data.hasOwnProperty(key) &&
			"number" === typeof this.data[key]
			? (this.data[key] as number)
			: 0;
	}

	public getBool(key: string): boolean {
		return true === this.data.hasOwnProperty(key) &&
			"boolean" === typeof this.data[key]
			? (this.data[key] as boolean)
			: false;
	}

	public getArray(key: string): Array<unknown> {
		return true === this.data.hasOwnProperty(key) &&
			true === Array.isArray(this.data[key])
			? (this.data[key] as Array<unknown>)
			: [];
	}

	public getSubCollection(key: string): Collection {
		return true === this.data.hasOwnProperty(key) &&
			"object" === typeof this.data[key]
			? new Collection(this.data[key] as Record<string, unknown>)
			: new Collection({});
	}
}

export default Collection;
