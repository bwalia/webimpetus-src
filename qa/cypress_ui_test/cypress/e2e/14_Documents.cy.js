describe(`Workstation Documents test on ${Cypress.env("TARGET_ENV")} environment`, () => {    
    const targetEnv = Cypress.env("TARGET_ENV");
    let urlStr = 'https://test-my.workstation.co.uk/#/login'

    if (targetEnv) {
      urlStr = `https://${targetEnv}-my.workstation.co.uk/#/login`
    }
  
    var login_username_str = Cypress.env('login_username');
    var login_password_str = Cypress.env('login_password');
    var randomString = Cypress.env('epochTime')

    it('Verifying Documents actions', () => {

    // Login with valid credentials
    cy.visit(urlStr);
    cy.get('#email').clear();
    cy.get('#password').clear(); 
    cy.get('#email').type(login_username_str);
    cy.get('#password').type(login_password_str);
    cy.get('button[type="submit"]').click();    
    cy.get('select[id="uuidBusinessIdSwitcher"]').select("QA Test Workspace", {force: true})
    cy.wait(2000);

    // Open the Documents module from menu
    cy.get('a[href="/documents"]').click({ multiple: true });
    cy.wait(1000);

    // Adding a new Document
    cy.contains('a', 'Add').click();
    cy.wait(1000);    
    cy.get('select[id="client_id"]').select(`Cypress Customer ${randomString}`, {force: true})
    cy.get('input[type=file]').selectFile('./cypress/testfile.txt')
    cy.get('input[id="document_date"]').type("01/09/2024");
    cy.get('select[name="category_id"]').select(`Cypress Category ${randomString}`, {force: true})
    cy.get('textarea[id="metadata"]').type("This document is added by Cypress");
    cy.get('button[type="submit"]').click();  
    // Verifying a new document is added successfully
    cy.get('div[class="alert alert-success"]').should("contain", "Data updated Successfully!");    
  
    // Verifying that the file is uploaded successfully
    cy.get('a[href="/documents"]').click({ multiple: true });
    cy.get(`tr:contains('testfile.txt')`).should('be.visible');

    // Editing the document added by cypress
    cy.contains('a', 'Documents').click();
    cy.scrollTo('right');
    cy.get(`tr:contains('testfile.txt') div[class="dropdown"]`).click();
    cy.contains('a', 'Edit').click();
    cy.get('input[id="document_date"]').type("05/09/2024");
    cy.contains('button', 'Submit').click();    
    cy.wait(2000);
    // Verifying the document is updated successfully
    cy.get('div[class="alert alert-success"]').should("contain", "Data updated Successfully!");    
    cy.get(`tr:contains('testfile.txt')`).should("contain", "05/09/2024");

    // Verifying the search filter
    cy.contains('label', 'Search:').type('testfile.txt')
    cy.wait(1000)
    cy.get(`tr:contains('testfile.txt')`).should('be.visible');

    cy.contains('label', 'Search:').type("xyz")
    cy.wait(1000)
    cy.get(`tr:contains('testfile.txt')`).should('not.be.visible');
    cy.contains('label', 'Search:').clear()  

    })
    
})