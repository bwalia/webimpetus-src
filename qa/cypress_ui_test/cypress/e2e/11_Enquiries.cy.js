describe(`Workstation Enquiries test on ${Cypress.env("TARGET_ENV")} environment`, () => {    
    const targetEnv = Cypress.env("TARGET_ENV");
    let urlStr = 'https://int-my.workstation.co.uk/#/login'

    if (targetEnv) {
      urlStr = `https://${targetEnv}-my.workstation.co.uk/#/login`
    }
  
    var login_username_str = Cypress.env('login_username');
    var login_password_str = Cypress.env('login_password');
    var randomString = Cypress.env('epochTime');

    it('Verifying Enquiries actions', () => {

    // Login with valid credentials
    cy.visit(urlStr);
    cy.get('#email').clear();
    cy.get('#password').clear(); 
    cy.get('#email').type(login_username_str);
    cy.get('#password').type(login_password_str);
    cy.get('button[type="submit"]').click();    
    cy.get('select[id="uuidBusinessIdSwitcher"]').select("QA Test Workspace", {force: true})
    cy.wait(2000);

    // Open the Enquiries module from menu
    cy.get('a[href="/enquiries"]').click({ multiple: true });
    cy.wait(1000);

    // Creating a new Enquiry
    cy.contains('a', 'Add').click();
    cy.wait(1000);    
    cy.get('input[id="name"]').type(`${randomString}`);
    cy.get('input[id="email"]').type("cypress@fakemail.com");
    cy.get('input[id="phone"]').type("0123456789");
    cy.get('textarea[id="message"]').type("This is a Enquiry created by Cypress.");
    cy.get('button[type="submit"]').click();    

    // Editing the Enquiry created by cypress
    cy.contains('a', 'Enquiries').click();
    cy.scrollTo('right');
    cy.get(`tr:contains('${randomString}') div[class="dropdown"]`).click();
    cy.contains('a', 'Edit').click();
    cy.get('textarea[id="message"]').type(" Updated now");
    cy.contains('button', 'Submit').click();    
    cy.wait(2000);
    // Verifying the Enquiry is updated successfully
    cy.get('div[class="alert alert-success"]').should("contain", "Data updated Successfully!");    
    cy.get(`tr:contains('${randomString}')`).should("contain", "Updated");

    // Verifying the search filter
    cy.contains('label', 'Filter by keyword:').type(`${randomString}{enter}`)
    cy.wait(1000)
    cy.get(`tr:contains('${randomString}')`).should('be.visible');
    var filter = cy.contains('label', 'Filter by keyword:').type('{selectall}{backspace}{enter}');

    })
    
})