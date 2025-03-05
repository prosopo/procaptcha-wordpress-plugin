import * as ReactDOM from "react-dom/client";
import {StrictMode} from "react";
import App from "./app";

class ProcaptchaStatistics extends HTMLElement {
    public connectedCallback(): void {
        if ("loading" === document.readyState) {
            document.addEventListener("DOMContentLoaded", this.setup);
            return;
        }

        this.setup();
    }

    public setup(): void {
        this.innerHTML = "";

        const root = ReactDOM.createRoot(this);

        root.render(
            <StrictMode>
                <App/>
            </StrictMode>,
        );
    }
}

customElements.define("procaptcha-statistics", ProcaptchaStatistics);
