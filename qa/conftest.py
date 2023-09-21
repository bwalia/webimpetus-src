import time
import pytest 
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
import chromedriver_autoinstaller
from chromedriver_autoinstaller import install as install_chrome_driver
import os
from selenium.webdriver.support.select import Select


@pytest.fixture(scope="module")
def setup(request):
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
    driver.maximize_window()

    driver.implicitly_wait(15)

    # Clearing the cookie 
    driver.delete_all_cookies()

    targetHost = os.environ.get('TARGET_HOST')
    driver.get(targetHost)

    EMAIL = os.environ.get('QA_LOGIN_EMAIL')
    PASSWORD = os.environ.get('QA_LOGIN_PASSWORD')

    driver.find_element(By.NAME, "email").send_keys(EMAIL)
    driver.find_element(By.NAME, "password").send_keys(PASSWORD)
    time.sleep(2)
    driver.find_element(By.XPATH, "//button[@type='submit']").click()
    time.sleep(2)
    driver.find_element(By.XPATH, "//div[@class='business-uuid-selector mr-3']").click()
    dropdown = Select(driver.find_element(By.XPATH, "//select[@id='uuidBusinessIdSwitcher']"))
    time.sleep(2)
    dropdown.select_by_visible_text("Balinder Walia's company")
    time.sleep(2)
    request.module.driver = driver 
    yield driver
    time.sleep(2)
    driver.close()