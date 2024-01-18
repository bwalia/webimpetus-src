describe(`Workstation Contacts test on ${Cypress.env("TARGET_ENV")} environment`, () => {    
    const targetEnv = Cypress.env("TARGET_ENV");
    let urlStr = 'https://int-my.workstation.co.uk/#/login'

    if (targetEnv) {
      urlStr = `https://${targetEnv}-my.workstation.co.uk/#/login`
    }
  
    var login_username_str = Cypress.env('login_username') 
    var login_password_str = Cypress.env('login_password') 
  
    it('Verifying Contacts actions', () => {

    // Login with valid credentials
    cy.visit(urlStr);
    cy.get('#email').clear();
    cy.get('#password').clear(); 
    cy.get('#email').type(login_username_str);
    cy.get('#password').type(login_password_str);
    cy.get('button[type="submit"]').click();    
    cy.get('select[id="uuidBusinessIdSwitcher"]').select("QA Test Workspace", {force: true});
    cy.wait(2000);

    // Open the Contacts module from menu
    cy.get('a[href="/contacts"]').click({ multiple: true });
    cy.wait(1000);

    // Creating a new Contact
    cy.contains('a', 'Add').click();
    cy.wait(1000);
    cy.get('select[id="client_id"]').select("Cypress Customer", {force: true});
    cy.get('input[name="first_name"]').type("Cypress");
    cy.get('input[id="email"]').type("cypress@contacts.com");
    cy.get('input[id="mobile"]').type("0123456789");
    cy.get('select[id="contact_type"]').select("Test category", {force: true});
    cy.get('textarea[name="comments"]').type("This is a Contact created by Cypress");
    cy.get('button[type="submit"]').click();    

    // Editing the Contact created by cypress
    // cy.contains('a', 'Contacts').click();
    // cy.get(`tr:contains('Cypress') div[class="dropdown"]`).click();
    // cy.contains('a', 'Edit').click();
    // cy.get('input[name="title"]').type("Test");
    // cy.contains('button', 'Submit').click();    
    // cy.wait(2000);

    // // Verifying the Contacts is updated successfully
    // cy.get('div[class="alert alert-success"]').should("contain", "Data updated Successfully!");    
    // cy.get(`tr:contains('Cypress Contact')`).should("contain", "Test");


    // Verifying the search filter
    cy.contains('label', 'Search:').type("cypress")
    cy.wait(1000)
    cy.get(`tr:contains('Cypress')`).should('be.visible');

    cy.contains('label', 'Search:').type("xyz")
    cy.wait(1000)
    cy.get(`tr:contains('Cypress')`).should('not.be.visible');
    cy.contains('label', 'Search:').clear()

    // Deleting the Contact created by cypress
    cy.contains('a', 'Contacts').click();
    cy.get(`tr:contains('Cypress') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");    

    // Deleting the Customer created by cypress
    cy.contains('a', 'Customers').click();
    cy.get(`tr:contains('Cypress Customer') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");    

    })
    
})