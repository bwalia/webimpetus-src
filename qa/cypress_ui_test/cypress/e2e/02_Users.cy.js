describe(`Workstation users test on ${Cypress.env("TARGET_ENV")} environment`, () => {    
    const targetEnv = Cypress.env("TARGET_ENV");
    let urlStr = 'https://int-my.workstation.co.uk/#/login'

    if (targetEnv) {
      urlStr = `https://${targetEnv}-my.workstation.co.uk/#/login`
    }
  
    var login_username_str = Cypress.env('login_username')
    var login_password_str = Cypress.env('login_password')
  
    it('Verifying User actions', () => {

    // Login with valid credentials
    cy.visit(urlStr);
    cy.get('#email').clear();
    cy.get('#password').clear(); 
    cy.get('#email').type(login_username_str);
    cy.get('#password').type(login_password_str);
    cy.get('button[type="submit"]').click();    
    cy.get('select[id="uuidBusinessIdSwitcher"]').select("QA Test Workspace", {force: true})
    cy.wait(2000);

    // Open the Users module from menu
    cy.get('a[href="/users"]').click({ multiple: true });
    cy.wait(1000);

    // Creating a new User
    cy.contains('a', 'Add').click();
    cy.wait(1000);    
    cy.get('input[id="inputName"]').type("Cypress User");
    cy.get('input[id="inputEmail"]').type("cypress@user.com");
    cy.get('input[id="inputPassword4"]').type("Cypress@user123");
    cy.get('textarea[name="address"]').type("Cypress test address");
    cy.get('textarea[id="inputNotes"]').type("This is a user created by Cypress");
    cy.get('select[id="inputLanCode"]').select("English");
    cy.get('select[id="sid"]').select("Projects", {force: true});
    cy.get('button[type="submit"]').click();    

    // changing the user status
    cy.contains('a', 'Users').click();
    cy.get(`tr:contains('Cypress User') input[data-url="users/status"]`).click({ force: true });
    cy.on('window:confirm', (str) => {expect(str).to.equal('The status updated successfully!')})
    cy.on('window:confirm', () => true);

    // Editing the User created by cypress
    cy.contains('a', 'Users').click();
    cy.get(`tr:contains('Cypress User') div[class="dropdown"]`).click();
    cy.contains('a', 'Edit').click();
    cy.get('textarea[name="address"]').type(" updated");
    cy.contains('button', 'Submit').click();    
    cy.wait(2000);

    // Verifying the user is updated successfully
    cy.get('div[class="alert alert-success"]').should("contain", "Data updated Successfully!");    
    cy.get(`tr:contains('Cypress User')`).should("contain", "updated");

    // Verifying the search filter
    cy.contains('label', 'Search:').type("cypress")
    cy.wait(1000)
    cy.get(`tr:contains('Cypress User')`).should('be.visible');

    cy.contains('label', 'Search:').type("xyz")
    cy.wait(1000)
    cy.get(`tr:contains('Cypress User')`).should('not.be.visible');
    cy.contains('label', 'Search:').clear()


    })
    
})