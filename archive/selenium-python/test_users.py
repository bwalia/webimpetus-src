import time
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions
from selenium.webdriver.support.wait import WebDriverWait
from selenium.webdriver import Keys
from selenium.webdriver.support.select import Select



def test_addUsers(setup, request):
    driver = request.module.driver
    wait = WebDriverWait(driver, 15)

    def wait_for_element(by, selector):
      element = wait.until(expected_conditions.presence_of_element_located((by, selector)))
      return element

    # Create new user
    wait_for_element(By.XPATH, "//a[@href='/users']").click()
    time.sleep(2)
    wait_for_element(By.LINK_TEXT, "Add").click()

      # Verifying that the name field is mandatory
    wait_for_element(By.CSS_SELECTOR, "button[type='submit']").click()
    nameFieldRequirement = wait_for_element(By.XPATH, "//div[@class='form-control-feedback']").text
    print(nameFieldRequirement)
    assert "This field is required" in nameFieldRequirement

    wait_for_element(By.NAME, "name").send_keys("Selenium")

      # Verifying that the email field is mandatory
    wait_for_element(By.CSS_SELECTOR, "button[type='submit']").click()
    emailFieldRequirement = wait_for_element(By.XPATH, "//div[@class='form-control-feedback']").text
    print(emailFieldRequirement)
    assert "This field is required" in emailFieldRequirement

    wait_for_element(By.NAME, "email").send_keys("selenium@test.com")

      # Verifying that the password field is mandatory
    wait_for_element(By.CSS_SELECTOR, "button[type='submit']").click()
    passFieldRequirement = wait_for_element(By.XPATH, "//div[@class='form-control-feedback']").text
    print(passFieldRequirement)
    assert "This field is required" in passFieldRequirement
    wait_for_element(By.NAME, "password").send_keys("Testpassword123")
    time.sleep(4)
    wait_for_element(By.NAME, "address").send_keys("This is test address")
    wait_for_element(By.NAME, "notes").send_keys("This is test notes")
    
    languageDropdown = Select(driver.find_element(By.NAME, "language_code"))
    time.sleep(2)
    languageDropdown.select_by_visible_text("English")

    roleDropdown = Select(driver.find_element(By.NAME, "role"))
    time.sleep(2)
    roleDropdown.select_by_visible_text("Default Role")

    wait_for_element(By.CSS_SELECTOR, "button[type='submit']").click()
    #driver.get_screenshot_as_file("UserCreated.png")
    userMessage = wait_for_element(By.CLASS_NAME, "alert-success").text
    print(userMessage)
    assert "Successfully!" in userMessage



def test_usersActions(setup, request):
    driver = request.module.driver
    wait = WebDriverWait(driver, 15)

    def wait_for_element(by, selector):
      element = wait.until(expected_conditions.presence_of_element_located((by, selector)))
      return element

    # Verifying user status update
    time.sleep(2)
    wait_for_element(By.XPATH, "//a[@href='/users']").click()
    wait_for_element(By.XPATH, "//tr[contains(., 'Selenium')]//span[@class='slider round']").click()
    time.sleep(2)
    status_alert = driver.switch_to.alert
    alertText = status_alert.text
    print(alertText)
    assert "The status updated successfully!" in alertText
    status_alert.accept()


    # Verifying Actions 
       # Edit the user
    time.sleep(2)
    driver.execute_script("arguments[0].scrollIntoView();",  wait_for_element(By.XPATH, "//tr[contains(., 'Selenium')]"))
    drop_Down = driver.find_element(By.XPATH, "//tr[contains(., 'Selenium')]//span[@id='dropdownMenuButton']")
    drop_Down.click()
    time.sleep(2)
    edit_button = wait_for_element(By.LINK_TEXT, "Edit")
    edit_button.click()
    time.sleep(2)

    wait_for_element(By.NAME, "address").send_keys(" Updated.")
    wait_for_element(By.CSS_SELECTOR, "button[type='submit']").click()

    edit_success_alert = wait_for_element(By.CSS_SELECTOR, ".alert").text
    print(edit_success_alert)
    assert "Data updated Successfully!" in edit_success_alert
    
      # Double check by verifying the changes are there 
    wait_for_element(By.XPATH, "//td[contains(text(),'Selenium')]").click()
    time.sleep(2)
    updated_text = wait_for_element(By.NAME, "address").text
    print(updated_text)
    assert "Updated" in updated_text

      # Delete a User
    wait_for_element(By.XPATH, "//a[@href='/users']").click()
    time.sleep(2)
    dropDown = driver.find_element(By.XPATH, "//tr[contains(., 'Selenium')]//span[@id='dropdownMenuButton']")
    dropDown.click()
    time.sleep(2)
    delete_button = wait_for_element(By.LINK_TEXT, "Delete")
    delete_button.click()

    time.sleep(4)
    alert = driver.switch_to.alert
    alert.accept()
    time.sleep(2)
    delete_success_alert = wait_for_element(By.CSS_SELECTOR, ".alert").text
    print(delete_success_alert)
    assert "deleted Successfully!" in delete_success_alert