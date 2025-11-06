import {FormTest} from "@support/form-test";

interface Settings {
    url?: string;
}

abstract class WordpressForm extends FormTest {
    constructor(settings: Settings = {}) {
        super();

        let url = settings.url || "";

        if ("" !== url) {
            this.url = url;
        }
    }

    protected abstract getSettingName(): string;

    protected toggleFeatureSupport(isActivation: boolean): void {
        this.toggleSetting(
            "core-forms",
            this.getSettingName(),
            isActivation,
        );
    }
}

export default WordpressForm;
