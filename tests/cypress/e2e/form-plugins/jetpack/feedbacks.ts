const trashFeedbacks = () => {
    cy.visit("/wp-admin/admin.php?page=jetpack-forms-admin#/responses?status=inbox");

    // select all
    cy.get(".dataviews-view-table-selection-checkbox .components-checkbox-control__input")
        .first()
        .click();

    // remove
    cy.get(".dataviews-bulk-actions-footer__action-buttons .components-button:contains('Trash')")
        .click();

    // notification
    cy.get(".components-snackbar__content")
        .should("be.visible");
}

const emptyFeedbacksTrash = () => {
    // clear trash
    cy.visit("/wp-admin/admin.php?page=jetpack-forms-admin#/responses?status=trash");

    // delete all
    const deleteButton = ".jp-forms-stack.admin-ui-page__header .components-button:contains('Empty')";

    // wait until loaded
    cy.get(deleteButton)
        .should("have.text", "Empty trash");
    // click
    cy.get(deleteButton)
        .click();

    // confirm
    cy.get(".components-modal__content .components-button:contains('Delete')")
        .click();

    // notification
    cy.get(".components-snackbar__content")
        .should("be.visible");
}

export const deleteAllFeedbacks = () => {
    cy.login();

    trashFeedbacks();
    emptyFeedbacksTrash();
};
