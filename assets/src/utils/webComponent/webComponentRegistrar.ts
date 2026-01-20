import { WebComponentSettings } from "./webComponentSettings.js";
import type { WebComponent } from "./webComponent.js";
import type Logger from "../logger/logger.js";

class WebComponentRegistrar {
	public constructor(private readonly logger: Logger) {}

	public registerWebComponent(
		webComponent: WebComponent,
		webComponentSettings: WebComponentSettings,
	): void {
		const WebComponentClass = this.createWebComponentClass(
			webComponent,
			this.logger,
			webComponentSettings,
		);

		customElements.define(webComponentSettings.name, WebComponentClass);
	}

	protected createWebComponentClass(
		webComponent: WebComponent,
		logger: Logger,
		componentSettings: WebComponentSettings,
	): typeof HTMLElement {
		class WebComponentClass extends HTMLElement {
			private isSetup: boolean;

			constructor() {
				super();

				this.isSetup = false;
			}

			public connectedCallback(): void {
				const isAlreadySetup = true === this.isSetup;

				if (
					true === isAlreadySetup &&
					false === componentSettings.processIfReconnected
				) {
					logger.debug(
						"connectedCallback() is fired for the instance that was already setup, ignoring according to the component settings",
						{
							name: componentSettings.name,
							element: this,
						},
					);
					return;
				}

				this.isSetup = true;

				const isDocumentReady = this.isDocumentReady(
					document.readyState,
					componentSettings.waitWindowLoadedInsteadOfDomLoaded,
				);

				if (true === isDocumentReady) {
					logger.debug("connectedCallback() is fired, processing", {
						name: componentSettings.name,
						wasAlreadySetup: isAlreadySetup,
						element: this,
					});

					this.constructElement();
					return;
				}

				logger.debug(
					"connectedCallback() is fired, but document is not ready, delaying processing",
					{
						name: componentSettings.name,
						wasAlreadySetup: isAlreadySetup,
						waitWindowLoadedInsteadOfDomLoaded:
							componentSettings.waitWindowLoadedInsteadOfDomLoaded,
						element: this,
					},
				);

				this.scheduleElementConstruction();
			}

			public constructScheduledElement(): void {
				logger.debug(
					"document is ready, running scheduled WebComponent construction",
					{
						name: componentSettings.name,
						element: this,
					},
				);

				this.constructElement();
			}

			protected constructElement(): void {
				webComponent.constructComponent(this);
			}

			protected isDocumentReady(
				currentState: string,
				withWindowLoaded: boolean,
			): boolean {
				return true === withWindowLoaded
					? this.isDocumentLoaded(currentState)
					: this.isDocumentInteractive(currentState);
			}

			protected isDocumentLoaded(currentState: string): boolean {
				return "complete" === currentState;
			}

			protected isDocumentInteractive(currentState: string): boolean {
				const interactiveStates = ["interactive", "complete"];

				return true === interactiveStates.includes(currentState);
			}

			protected scheduleElementConstruction(): void {
				if (
					true ===
					componentSettings.waitWindowLoadedInsteadOfDomLoaded
				) {
					window.addEventListener(
						"load",
						this.constructScheduledElement.bind(this),
					);
					return;
				}

				document.addEventListener(
					"DOMContentLoaded",
					this.constructScheduledElement.bind(this),
				);
			}
		}

		return WebComponentClass;
	}
}

export { WebComponentRegistrar };
