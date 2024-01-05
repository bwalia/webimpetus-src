describe(`Workstation login test on ${Cypress.env("TARGET_ENV")} environment`, () => {    
    const targetEnv = Cypress.env("TARGET_ENV");
    let urlStr = 'https://int-my.workstation.co.uk/#/login'

    if (targetEnv) {
      urlStr = `https://${targetEnv}-my.workstation.co.uk/#/login`
    }
  
  
    var login_username_str = Cypress.env('login_username')
    var login_password_str = Cypress.env('login_password')
  
    it('Login with invalid email and invalid password', () => {

      cy.visit(urlStr);
      cy.get('#email').type('test@example.com');
      cy.get('#password').type('fakepass');
      cy.get('button[type="submit"]').click();
      cy.get('div[class="alert alert-danger"]').should("contain", "Wrong email or password!");
    })  
    
    it('Login with invalid email and valid password', () => {
      cy.visit(urlStr);
      cy.get('#email').clear();
      cy.get('#password').clear();  
      cy.get('#email').type('abc');
      cy.get('#password').type(login_password_str);
      cy.get('button[type="submit"]').click();
      cy.get('label[id="email-error"]').should("contain", "Please enter valid email");    
    })
    
    it('Login with valid email and invalid password', () => {
      cy.visit(urlStr);
      cy.get('#email').clear();
      cy.get('#password').clear();
      cy.get('#email').type(login_username_str);
      cy.get('#password').type('fakepass');
      cy.get('button[type="submit"]').click();
      cy.get('div[class="alert alert-danger"]').should("contain", "Wrong email or password!");
    })
    
    it('Login with valid email and empty password', () => {
      cy.visit(urlStr);
      cy.get('#email').clear();
      cy.get('#password').clear();
      cy.get('#email').type(login_username_str);
      cy.get('button[type="submit"]').click();
      cy.get('label[id="password-error"]').should("contain", "Please enter your password");
    
    })
    
    it('Login with empty email and valid password', () => {
      cy.visit(urlStr);
      cy.get('#email').clear();
      cy.get('#password').clear();
      cy.get('#password').type(login_password_str);
      cy.get('button[type="submit"]').click();
      cy.get('label[id="email-error"]').should("contain", "Please enter valid email");        
    })
    
    it('Login with empty email and empty password', () => {
      cy.visit(urlStr);
      cy.get('#email').clear();
      cy.get('#password').clear();
      cy.get('button[type="submit"]').click();
      cy.get('label[id="email-error"]').should("contain", "Please enter valid email");  
      cy.get('label[id="password-error"]').should("contain", "Please enter your password");  
    })
    
    it('Login with valid credentials and Verify Logout', () => {
      cy.visit(urlStr);
      cy.get('#email').clear();
      cy.get('#password').clear(); 
      cy.get('#email').type(login_username_str);
      cy.get('#password').type(login_password_str);
      cy.get('button[type="submit"]').click();
      cy.get('a[href="/dashboard"]').should('be.visible');


      // Verifying Log out
      cy.get('div[class="profile_info"]').realHover('mouse')
      cy.wait(2000)
      cy.get('a[href="/home/logout"]').click()
      cy.get('div[class="alert alert-success"]').should("contain", "Logged out successfully!");
    })
    
  })
  
  