const {defineConfig} = require("cypress");
const fs = require('fs');

module.exports = defineConfig({
    video: true,
    e2e: {
        setupNodeEvents(on, config) {
            require("cypress-terminal-report/src/installLogsPrinter")(on, {
                outputRoot: config.projectRoot + "/cypress/",
                specRoot: "cypress/e2e",
                outputTarget: {
                    "logs|html": "html",
                },
            });
            on(
                'after:spec',
                (spec, results) => {
                    if (!results || !results.video) {
                        return;
                    }

                    const failures = results.tests.some((test) =>
                        test.attempts.some((attempt) => attempt.state === 'failed')
                    );

                    if (failures) {
                        return;
                    }

                    // fixme fs.unlinkSync(results.video);
                }
            )
        },
        baseUrl: "http://procaptcha.local",
        watchForFileChanges: false, // Disable auto-run on file changes.
        defaultCommandTimeout: 6000, // default 4000.
        viewportWidth: 1280, // default 1000.
        viewportHeight: 720, // default 660.
    },
});
