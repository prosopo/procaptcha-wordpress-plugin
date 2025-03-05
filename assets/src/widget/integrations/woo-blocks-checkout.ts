import ComponentControllerInterface from "../../interfaces/componentControllerInterface";
import LoggerInterface from "../../interfaces/loggerInterface";
import registerWebComponent from "../../registerWebComponent";
import LoggerFactory from "../../logger/loggerFactory";
import ModuleLogger from "../../logger/moduleLogger";

class WooBlocksCheckoutIntegration implements ComponentControllerInterface {
    private readonly logger: LoggerInterface;

    constructor(logger: LoggerInterface) {
        this.logger = logger;
    }

    processElement(origin: HTMLElement): void {
        const form = origin.closest("form");

        // add a stub to bypass Woo client validation, and run server,
        // otherwise it's confusing as the input is hidden.
        this.updateInputValue("default");

        form.addEventListener(
            "_prosopo-procaptcha__filled",
            (event: CustomEvent) => {
                this.updateInputValue(event.detail.token);
            },
        );
    }

    protected updateInputValue(token: string): void {
        if (
            false === window.hasOwnProperty("wp") ||
            false === window["wp"].hasOwnProperty("data")
        ) {
            this.logger.warning("window.wp.data is not available");
            return;
        }

        window["wp"].data.dispatch("wc/store/checkout").setAdditionalFields({
            "prosopo-procaptcha/prosopo_procaptcha": token,
        });
    }
}

const loggerFactory = new LoggerFactory();
const moduleLogger = new ModuleLogger();

const wooBlocksCheckout = new WooBlocksCheckoutIntegration(
    loggerFactory.makeLogger("woo-blocks-checkout", moduleLogger),
);

const webComponentRegistarLogger = loggerFactory.makeLogger(
    "web-component-registar",
    moduleLogger,
);

registerWebComponent(webComponentRegistarLogger, {
    name: "prosopo-procaptcha-woo-checkout-form",
    componentController: wooBlocksCheckout,
    processIfReconnected: false,
    waitWindowLoadedInsteadOfDomLoaded: true,
});
