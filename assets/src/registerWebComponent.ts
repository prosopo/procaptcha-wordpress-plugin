import WebComponentSettings from "./interfaces/webComponentSettings";
import LoggerInterface from "./interfaces/loggerInterface";

export default function (
	logger: LoggerInterface,
	settings: WebComponentSettings,
): void {
	class WebComponent extends HTMLElement {
		private isSetup: boolean;

		constructor() {
			super();

			this.isSetup = false;
		}

		public connectedCallback(): void {
			const isAlreadySetup = true === this.isSetup;

			if (
				true === isAlreadySetup &&
				false === settings.processIfReconnected
			) {
				logger.debug(
					"connectedCallback() is fired for the instance that was already setup, ignoring according to the component settings",
					{
						name: settings.name,
						element: this,
					},
				);
				return;
			}

			this.isSetup = true;

			const isDocumentReady = this.isDocumentReady(
				document.readyState,
				settings.waitWindowLoadedInsteadOfDomLoaded,
			);

			if (true === isDocumentReady) {
				logger.debug("connectedCallback() is fired, processing", {
					name: settings.name,
					wasAlreadySetup: isAlreadySetup,
					element: this,
				});

				this.process();
				return;
			}

			logger.debug(
				"connectedCallback() is fired, but document is not ready, delaying processing",
				{
					name: settings.name,
					wasAlreadySetup: isAlreadySetup,
					waitWindowLoadedInsteadOfDomLoaded:
						settings.waitWindowLoadedInsteadOfDomLoaded,
					element: this,
				},
			);

			this.delayProcess();
		}

		public processDelayed(): void {
			logger.debug("document is ready, processing delayed WebComponent", {
				name: settings.name,
				element: this,
			});

			this.process();
		}

		protected process(): void {
			settings.componentController.processElement(this);
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

		protected delayProcess(): void {
			if (true === settings.waitWindowLoadedInsteadOfDomLoaded) {
				window.addEventListener("load", this.processDelayed.bind(this));
				return;
			}

			document.addEventListener(
				"DOMContentLoaded",
				this.processDelayed.bind(this),
			);
		}
	}

	customElements.define(settings.name, WebComponent);
}
