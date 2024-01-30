describe(`Workstation Employees test on ${Cypress.env("TARGET_ENV")} environment`, () => {    
    const targetEnv = Cypress.env("TARGET_ENV");
    let urlStr = 'https://int-my.workstation.co.uk/#/login'

    if (targetEnv) {
      urlStr = `https://${targetEnv}-my.workstation.co.uk/#/login`
    }
  
    var login_username_str = Cypress.env('login_username');
    var login_password_str = Cypress.env('login_password');
    var randomString = Cypress.env('epochTime')

    it('Verifying Employees actions', () => {

    // Login with valid credentials
    cy.visit(urlStr);
    cy.get('#email').clear();
    cy.get('#password').clear(); 
    cy.get('#email').type(login_username_str);
    cy.get('#password').type(login_password_str);
    cy.get('button[type="submit"]').click();    
    cy.get('select[id="uuidBusinessIdSwitcher"]').select("QA Test Workspace", {force: true})
    cy.wait(2000);

    // Open the Employees module from menu
    cy.get('a[href="/employees"]').click({ multiple: true });
    cy.wait(1000);

    // Creating a new Employee
    cy.contains('a', 'Add').click();
    cy.wait(1000);    
    cy.get('input[id="first_name"]').type(`Cypress Employee ${randomString}`);
    cy.get('input[id="email"]').type("cypress@employee.com");
    cy.get('input[id="mobile"]').type("0123456789");
    cy.get('textarea[id="comments"]').type("This is a employee created by Cypress");
    cy.get('button[type="submit"]').click();    

    // Editing the employee created by cypress
    cy.contains('a', 'Employees').click();
    cy.get(`tr:contains('Cypress Employee ${randomString}') div[class="dropdown"]`).click();
    cy.contains('a', 'Edit').click();
    cy.get('textarea[id="comments"]').type(" updated");
    cy.contains('button', 'Submit').click();    
    cy.wait(2000);

    // Verifying the employee is updated successfully
    cy.get('div[class="alert alert-success"]').should("contain", "Data updated Successfully!");    
    cy.get(`tr:contains('Cypress Employee ${randomString}')`).should("contain", "updated");

    // Verifying the search filter
    cy.contains('label', 'Search:').type(`${randomString}`)
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Employee ${randomString}')`).should('be.visible');

    cy.contains('label', 'Search:').type("xyz")
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Employee ${randomString}')`).should('not.be.visible');
    cy.contains('label', 'Search:').clear()
  

    })
    
})