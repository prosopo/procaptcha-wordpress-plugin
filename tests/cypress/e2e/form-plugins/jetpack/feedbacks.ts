// Use the WordPress REST API to clean up Jetpack contact-form feedbacks.
// Going through the admin UI is brittle: newer Jetpack ships a dataviews-based
// responses screen whose checkbox/trash markup lags an async fetch, and the
// legacy "feedback" post type sometimes isn't surfaced at all. Hitting the
// REST endpoints directly bypasses every UI selector.
export const deleteAllFeedbacks = () => {
    cy.login();

    const restRequest = (
        method: "GET" | "DELETE",
        path: string,
    ): Cypress.Chainable<Cypress.Response<unknown>> =>
        cy.request({
            method,
            url: "/wp-json/wp/v2/feedback" + path,
            failOnStatusCode: false,
        });

    const deleteInStatus = (status: "publish" | "trash") => {
        restRequest("GET", `?status=${status}&per_page=100&_fields=id`).then(
            (res) => {
                if (200 !== res.status || !Array.isArray(res.body)) {
                    return;
                }
                (res.body as Array<{ id: number }>).forEach(({ id }) => {
                    restRequest("DELETE", `/${id}?force=true`);
                });
            },
        );
    };

    deleteInStatus("publish");
    deleteInStatus("trash");
};
