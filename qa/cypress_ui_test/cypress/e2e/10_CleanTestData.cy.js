describe(`Workstation Clean Test data on ${Cypress.env("TARGET_ENV")} environment`, () => {    
    const targetEnv = Cypress.env("TARGET_ENV");
    let urlStr = 'https://int-my.workstation.co.uk/#/login'

    if (targetEnv) {
      urlStr = `https://${targetEnv}-my.workstation.co.uk/#/login`
    }
  
    var login_username_str = Cypress.env('login_username');
    var login_password_str = Cypress.env('login_password');
  
    it('Cleaning Cypress Test data', () => {

    // Login with valid credentials
    cy.visit(urlStr);
    cy.get('#email').clear();
    cy.get('#password').clear(); 
    cy.get('#email').type(login_username_str);
    cy.get('#password').type(login_password_str);
    cy.get('button[type="submit"]').click();    
    cy.get('select[id="uuidBusinessIdSwitcher"]').select("QA Test Workspace", {force: true})
    cy.wait(2000);

    // Deleting the User created by cypress
    cy.contains('a', 'Users').click();
    cy.get(`tr:contains('Cypress User') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");    

    // Deleting the Employee created by cypress
    cy.contains('a', 'Employees').click();
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Employee') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");   
    
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
    
    // Deleting the Project created by cypress
    cy.contains('a', 'Projects').click();
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Project') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");    

    // Deleting the Sprint created by cypress
    cy.contains('a', 'Sprints').click();
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Sprint') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");  
    
    
    // Deleting the Category created by cypress
    cy.contains('a', 'Categories').click();
    cy.wait(1000)
    cy.get(`tr:contains('Cypress Category') div[class="dropdown"]`).click();
    cy.contains('a', 'Delete').click();
    cy.on('window:confirm', (str) => {expect(str).to.equal('Are you sure want to delete?')});
    cy.on('window:confirm', () => true);
    cy.wait(2000);
    cy.get('div[class="alert alert-success"]').should("contain", "Data deleted Successfully!");     
    })
    
})