import { UserConfig } from "vite";
import path from "path";
import deepmerge from "deepmerge";
import fs from "fs";
import checker from "vite-plugin-checker";

class ViteBase {
	private readonly pluginFileRelativePath =
		"../prosopo-procaptcha/prosopo-procaptcha.php";
	private readonly defaultConfig: UserConfig;

	constructor() {
		const pluginVersion = this.getPluginVersion();
		this.defaultConfig = {
			root: ".",
			base: "",
			plugins: [
				checker({
					typescript: true,
				}),
			],
			build: {
				outDir: path.resolve(
					__dirname,
					`../prosopo-procaptcha/dist/${pluginVersion}`,
				),
				emptyOutDir: true,
				rollupOptions: {
					output: {
						entryFileNames: `[name].min.js`,
						assetFileNames: `[name].min[extname]`,
					},
				},
			},
			resolve: {
				alias: {
					"#integration": path.resolve(
						__dirname,
						"./src/integration",
					),
					"#integrations": path.resolve(
						__dirname,
						"./src/integrations",
					),
					"#logger": path.resolve(__dirname, "./src/logger"),
					"#settings": path.resolve(__dirname, "./src/settings"),
					"#webComponent": path.resolve(
						__dirname,
						"./src/webComponent",
					),
				},
			},
		};
	}

	public makeViteConfig(
		outputSubdirectoryName: string,
		customSettings: UserConfig,
	): UserConfig {
		const config = deepmerge(this.defaultConfig, customSettings);

		config.build.outDir = path.resolve(
			config.build?.outDir || "",
			outputSubdirectoryName,
		);

		return config;
	}

	protected getPluginVersion(): string {
		const readmeFilePath = path.resolve(
			__dirname,
			this.pluginFileRelativePath,
		);
		const readmeContent = fs.readFileSync(readmeFilePath, "utf-8");

		const versionMatch = readmeContent.match(/Version:\s*(\d+\.\d+\.\d+)/);

		if (!versionMatch)
			throw new Error(
				`Could not find plugin version in the readme file: ${readmeFilePath}`,
			);

		return versionMatch[1];
	}
}

const viteBase = new ViteBase();

const makeViteConfig = viteBase.makeViteConfig.bind(viteBase);

export { makeViteConfig };
