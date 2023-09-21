import time
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions
from selenium.webdriver.support.wait import WebDriverWait
import chromedriver_autoinstaller
from chromedriver_autoinstaller import install 
import os
from selenium.webdriver import Keys
from selenium.webdriver import ActionChains




def test_login():
    chrome_driver_path = chromedriver_autoinstaller.install()
    chrome_service: Service = Service(executable_path=chrome_driver_path)

    chrome_options = webdriver.ChromeOptions()    
    # Add your options as needed    
    options = [
         "--headless",
         "--disable-gpu",
         "--no-sandbox",
    ]

    for option in options:
        chrome_options.add_argument(option)
    
    driver = webdriver.Chrome(options = chrome_options)

    targetHost = os.environ.get('TARGET_HOST')
    EMAIL = os.environ.get('QA_LOGIN_EMAIL')
    PASSWORD = os.environ.get('QA_LOGIN_PASSWORD')
        
    driver.get(targetHost)

    # Verify login with invalid credentials
    driver.find_element(By.NAME, "email").send_keys("abc@xyz.com")
    driver.find_element(By.NAME, "password").send_keys("invalidpass")
    driver.find_element(By.XPATH, "//button[@type='submit']").click()
    time.sleep(2)
    wait = WebDriverWait(driver, 10)
    expectedError1 = wait.until(expected_conditions.presence_of_element_located((By.XPATH, "//div[@class='alert alert-danger']"))).text
    #print(expectedError1)
    assert "Wrong email or password!" in expectedError1

    # Verify login with valid email and invalid password
    emailElement = driver.find_element(By.NAME, "email")
    emailElement.send_keys(Keys.END)
    length = len(emailElement.get_attribute("value"))
    emailElement.send_keys(Keys.BACKSPACE * length)
    emailElement.send_keys(EMAIL)

    passElement = driver.find_element(By.NAME, "password")
    passElement.send_keys(Keys.END)
    length = len(passElement.get_attribute("value"))
    passElement.send_keys(Keys.BACKSPACE * length)
    passElement.send_keys("invalidpass")
    driver.find_element(By.XPATH, "//button[@type='submit']").click()
    time.sleep(2)
    wait = WebDriverWait(driver, 10)
    expectedError2 = wait.until(expected_conditions.presence_of_element_located((By.XPATH, "//div[@class='alert alert-danger']"))).text
    #print(expectedError2)
    assert "Wrong email or password!" in expectedError2


    # Verify login with invalid email and valid password
    emailElement = driver.find_element(By.NAME, "email")
    emailElement.send_keys(Keys.END)
    length = len(emailElement.get_attribute("value"))
    emailElement.send_keys(Keys.BACKSPACE * length)
    emailElement.send_keys(2132446878)

    passElement = driver.find_element(By.NAME, "password")
    passElement.send_keys(Keys.END)
    length = len(passElement.get_attribute("value"))
    passElement.send_keys(Keys.BACKSPACE * length)
    passElement.send_keys(PASSWORD)
    driver.find_element(By.XPATH, "//button[@type='submit']").click()
    time.sleep(2)
    wait = WebDriverWait(driver, 10)
    expectedError3 = wait.until(expected_conditions.presence_of_element_located((By.ID, "email-error"))).text
    #print(expectedError3)
    assert "Please enter valid email" in expectedError3

    # Verify login with empty email and valid password
    emailElement = driver.find_element(By.NAME, "email")
    emailElement.send_keys(Keys.END)
    length = len(emailElement.get_attribute("value"))
    emailElement.send_keys(Keys.BACKSPACE * length)

    passElement = driver.find_element(By.NAME, "password")
    passElement.send_keys(Keys.END)
    length = len(passElement.get_attribute("value"))
    passElement.send_keys(Keys.BACKSPACE * length)
    passElement.send_keys(PASSWORD)
    driver.find_element(By.XPATH, "//button[@type='submit']").click()
    time.sleep(2)
    wait = WebDriverWait(driver, 10)
    expectedError4 = wait.until(expected_conditions.presence_of_element_located((By.ID, "email-error"))).text
    #print(expectedError4)
    assert "Please enter valid email" in expectedError4

     # Verify login with valid email and empty password
    emailElement = driver.find_element(By.NAME, "email")
    emailElement.send_keys(Keys.END)
    length = len(emailElement.get_attribute("value"))
    emailElement.send_keys(Keys.BACKSPACE * length)
    emailElement.send_keys(EMAIL)

    passElement = driver.find_element(By.NAME, "password")
    passElement.send_keys(Keys.END)
    length = len(passElement.get_attribute("value"))
    passElement.send_keys(Keys.BACKSPACE * length)
    driver.find_element(By.XPATH, "//button[@type='submit']").click()
    time.sleep(2)
    wait = WebDriverWait(driver, 10)
    expectedError5 = wait.until(expected_conditions.presence_of_element_located((By.ID, "password-error"))).text
    #print(expectedError5)
    assert "Please enter your password" in expectedError5

     # Verify login with both empty credentials
    emailElement = driver.find_element(By.NAME, "email")
    emailElement.send_keys(Keys.END)
    length = len(emailElement.get_attribute("value"))
    emailElement.send_keys(Keys.BACKSPACE * length)

    passElement = driver.find_element(By.NAME, "password")
    passElement.send_keys(Keys.END)
    length = len(passElement.get_attribute("value"))
    passElement.send_keys(Keys.BACKSPACE * length)
    driver.find_element(By.XPATH, "//button[@type='submit']").click()
    time.sleep(2)
    wait = WebDriverWait(driver, 10)
    expectedError6 = wait.until(expected_conditions.presence_of_element_located((By.XPATH, "//div[@class='alert alert-danger']"))).text
    #print(expectedError6)
    assert "Wrong email or password!" in expectedError6

    # Verify login with both valid credentials
    emailElement = driver.find_element(By.NAME, "email")
    emailElement.send_keys(Keys.END)
    length = len(emailElement.get_attribute("value"))
    emailElement.send_keys(Keys.BACKSPACE * length)
    emailElement.send_keys(EMAIL)

    passElement = driver.find_element(By.NAME, "password")
    passElement.send_keys(Keys.END)
    length = len(passElement.get_attribute("value"))
    passElement.send_keys(Keys.BACKSPACE * length)
    passElement.send_keys(PASSWORD)

    driver.find_element(By.XPATH, "//button[@type='submit']").click()
    time.sleep(2)
    wait = WebDriverWait(driver, 10)
    expectedRes = wait.until(expected_conditions.presence_of_element_located((By.XPATH, "//h3[@class='f_s_25 f_w_700 dark_text mr_30']"))).text
    # print(expectedRes)
    assert "Dashboard" in expectedRes

    # Verifying the Log out
    action = ActionChains(driver)
    obj1 = driver.find_element(By.XPATH, "//div[@class='profile_info']")
    action.move_to_element(obj1).perform()
    obj2 = driver.find_element(By.XPATH, "//a[normalize-space()='Log Out']")
    action.move_to_element(obj2).click().perform()
    logoutMessage = driver.find_element(By.CLASS_NAME, "alert-success").text
    print(logoutMessage)
    assert "Logged out successfully!" in logoutMessage
    driver.close()
