describe(`Workstation Customers test on ${Cypress.env("TARGET_ENV")} environment`, () => {    
    const targetEnv = Cypress.env("TARGET_ENV");
    let urlStr = 'https://int-my.workstation.co.uk/#/login'

    if (targetEnv) {
      urlStr = `https://${targetEnv}-my.workstation.co.uk/#/login`
    }
  
    var login_username_str = Cypress.env('login_username') 
    var login_password_str = Cypress.env('login_password')
    var randomString = Cypress.env('epochTime')

    it('Verifying Customers actions', () => {

    // Login with valid credentials
    cy.visit(urlStr);
    cy.get('#email').clear();
    cy.get('#password').clear(); 
    cy.get('#email').type(login_username_str);
    cy.get('#password').type(login_password_str);
    cy.get('button[type="submit"]').click();    
    cy.get('select[id="uuidBusinessIdSwitcher"]').select("QA Test Workspace", {force: true})
    cy.wait(2000);

    // Open the Customers module from menu
    cy.get('a[href="/customers"]').click({ multiple: true });
    cy.wait(1000);

    // Creating a new Customer
    cy.contains('a', 'Add').click();
    cy.wait(1000);    
    cy.get('input[name="company_name"]').type(`Cypress Customer ${randomString}`);
    cy.get('input[name="acc_no"]').type("555454534532543");
    cy.get('input[name="contact_firstname"]').type(`Cypress ${randomString}`);
    cy.get('input[id="email"]').type("cypress@customers.com");
    cy.get('input[id="phone"]').type("0123456789");
    cy.get('input[name="address1"]').type("Cypress test address");
    cy.get('textarea[name="notes"]').type("This is a Customers created by Cypress");
    cy.get('button[type="submit"]').click();    

    // Editing the Customers created by cypress
    cy.contains('a', 'Customers').click();
    cy.get(`tr:contains('Cypress Customer ${randomString}') div[class="dropdown"]`).click();
    cy.contains('a', 'Edit').click();
    cy.get('input[name="address1"]').type(" updated");
    cy.contains('button', 'Submit').click();    
    cy.wait(2000);

    // Verifying the Customer is updated successfully
    cy.get('div[class="alert alert-success"]').should("contain", "Data updated Successfully!");    
    cy.get(`tr:contains('Cypress Customer ${randomString}')`).should("contain", "updated");

    // changing the Customers status
    cy.contains('a', 'Customers').click();
    cy.get(`tr:contains('Cypress Customer ${randomString}') div[class="dropdown"]`).click();
    cy.contains('a', 'Edit').click();
    cy.get('input[id="status"]').click();
    cy.contains('button', 'Submit').click();  
    // Verifying if the status is updated
    cy.contains('a', 'Customers').click();
    cy.get(`tr:contains('Cypress Customer ${randomString}')`).should("contain", "Active");

    // Verifying the search filter
    cy.contains('label', 'Search:').type(`${randomString}`)
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Customer ${randomString}')`).should('be.visible');

    cy.contains('label', 'Search:').type("xyz")
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Customer ${randomString}')`).should('not.be.visible');
    cy.contains('label', 'Search:').clear()


    })
    
})