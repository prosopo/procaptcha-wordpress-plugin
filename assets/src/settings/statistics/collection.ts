class Collection {
	private data: object;

	constructor(data: object) {
		this.data = data;
	}

	public getString(key: string): string {
		return true === this.data.hasOwnProperty(key) &&
			"string" === typeof this.data[key]
			? this.data[key]
			: "";
	}

	public getNumber(key: string): number {
		return true === this.data.hasOwnProperty(key) &&
			"number" === typeof this.data[key]
			? this.data[key]
			: 0;
	}

	public getBool(key: string): boolean {
		return true === this.data.hasOwnProperty(key) &&
			"boolean" === typeof this.data[key]
			? this.data[key]
			: false;
	}

	public getArray(key: string): Array<unknown> {
		return true === this.data.hasOwnProperty(key) &&
			true === Array.isArray(this.data[key])
			? this.data[key]
			: [];
	}

	public getSubCollection(key: string): Collection {
		return true === this.data.hasOwnProperty(key) &&
			"object" === typeof this.data[key]
			? new Collection(this.data[key])
			: new Collection({});
	}
}

export default Collection;
