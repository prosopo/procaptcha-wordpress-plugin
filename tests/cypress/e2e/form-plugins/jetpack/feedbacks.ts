const trashFeedbacks = () => {
    cy.visit("/wp-admin/admin.php?page=jetpack-forms-admin#/responses?status=inbox");

    // select all
    cy.get(".dataviews-view-table-selection-checkbox .components-checkbox-control__input")
        .first()
        .click();

    // remove
    cy.get(".dataviews-bulk-actions-footer__action-buttons .components-button:nth-child(4)")
        .click();

    // notification
    cy.get(".components-snackbar__content")
        .should("exist");
}

const emptyFeedbacksTrash = () => {
    // clear trash
    cy.visit("/wp-admin/admin.php?page=jetpack-forms-admin#/responses?status=trash");

    // delete all
    cy.get(".jp-forms__layout-header-actions .components-button:nth-child(2)")
        .click();

    // confirm
    cy.get(".components-modal__content .components-button:nth-child(2)")
        .click();

    // notification
    cy.get(".components-snackbar__content")
        .should("exist");
}

export const deleteAllFeedbacks = () => {
    cy.login();

    trashFeedbacks();
    emptyFeedbacksTrash();
};
