const trashFeedbacks = () => {
    cy.visit("/wp-admin/admin.php?page=jetpack-forms-admin#/responses?status=inbox");

    // Newer Jetpack Forms admin doesn't always surface legacy contact-form
    // feedbacks here. If there are no rows to bulk-select, skip the trash
    // step rather than failing the cleanup hook.
    cy.get("body").then(($body) => {
        const $checkboxes = $body.find(
            ".dataviews-view-table-selection-checkbox .components-checkbox-control__input",
        );

        if (0 === $checkboxes.length) {
            return;
        }

        cy.wrap($checkboxes.first()).click();
        cy.get(".dataviews-bulk-actions-footer__action-buttons .components-button:contains('Trash')")
            .click();
        cy.get(".components-snackbar__content").should("be.visible");
    });
}

const emptyFeedbacksTrash = () => {
    // clear trash
    cy.visit("/wp-admin/admin.php?page=jetpack-forms-admin#/responses?status=trash");

    const deleteButton = ".jp-forms-stack.admin-ui-page__header .components-button:contains('Empty')";

    // Skip if the trash is already empty (the Empty button isn't rendered).
    cy.get("body").then(($body) => {
        if (0 === $body.find(deleteButton).length) {
            return;
        }

        cy.get(deleteButton).should("have.text", "Empty trash");
        cy.get(deleteButton).click();
        cy.get(".components-modal__content .components-button:contains('Delete')")
            .click();
        cy.get(".components-snackbar__content").should("be.visible");
    });
}

export const deleteAllFeedbacks = () => {
    cy.login();

    trashFeedbacks();
    emptyFeedbacksTrash();
};
