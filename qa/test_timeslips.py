import time
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions
from selenium.webdriver.support.wait import WebDriverWait
from selenium.webdriver import Keys
from selenium.webdriver.support.select import Select
from selenium.common.exceptions import NoSuchElementException, TimeoutException
from datetime import date



def test_timeslipsAdd(setup, request):
    driver = request.module.driver
    wait = WebDriverWait(driver, 15)

    def wait_for_element(by, selector):
      element = wait.until(expected_conditions.presence_of_element_located((by, selector)))
      return element

    # Creating a new timeslips.
    wait_for_element(By.XPATH, "//a[@href='/timeslips']").click()
    time.sleep(4)
    wait_for_element(By.LINK_TEXT, "Add").click()
    wait_for_element(By.ID, "select2-task_name-container").click()
    driver.execute_script("arguments[0].scrollIntoView();",  wait_for_element(By.XPATH, "//li[contains(text(),'Selenium Test')]"))
    wait_for_element(By.XPATH, "//li[contains(text(),'Selenium Test')]").click()

    wait_for_element(By.ID, "select2-employee_name-container").click()
    wait_for_element(By.XPATH, "//li[contains(text(),'Dixa Jangid')]").click()
    wait_for_element(By.ID, "slip_start_date").send_keys("09/11/2023")
    wait_for_element(By.XPATH, "//div[@class='form-group col-md-3']").click()
    wait_for_element(By.XPATH, "//button[@class='btn btn-info set-current-time']").click()

    wait_for_element(By.ID, "slip_end_date").send_keys("08/11/2023")
    wait_for_element(By.XPATH, "//div[@class='form-group col-md-3']").click()
    wait_for_element(By.XPATH, "//button[@class='btn btn-info set-current-time']").click()
    time.sleep(4)

    # Verifying test for data error when used a end date earlier than start date
    wait_for_element(By.CSS_SELECTOR, ".form-row:nth-child(7) .btn").click()
    time.sleep(2)
    date_error = wait_for_element(By.ID, "end_date_error").text
    assert "should be greater than" in date_error

    # Verifying the slip description works as mandatory
    element =  wait_for_element(By.ID, "slip_end_date")
    element.click()
    element.send_keys(Keys.END)
    length = len(element.get_attribute("value"))
    element.send_keys(Keys.BACKSPACE * length)
    element.send_keys("10/11/2023")
    wait_for_element(By.CSS_SELECTOR, ".form-row:nth-child(7) .btn").click()
    time.sleep(3)
    wait_for_element(By.XPATH, "//button[@type='submit']").click()
    time.sleep(2)
    slip_description_err = wait_for_element(By.ID, "slip_description-error").text
    assert "This field is required" in slip_description_err

    # Submitting the form
    wait_for_element(By.NAME, "slip_description").send_keys("This is a test description.")
    wait_for_element(By.NAME, "slip_rate").send_keys("0.00")
    wait_for_element(By.NAME, "slip_timer_accumulated_seconds").send_keys("0")
    wait_for_element(By.XPATH, "//button[@type='submit']").click()
    time.sleep(4)


def test_timeslipsFilter(setup, request):
    driver = request.module.driver
    wait = WebDriverWait(driver, 15)

    def wait_for_element(by, selector):
      element = wait.until(expected_conditions.presence_of_element_located((by, selector)))
      return element

    wait_for_element(By.XPATH, "//a[@href='/timeslips']").click()

    
    # Removing default filters
    monthDropdown = Select(wait_for_element(By.NAME, "list_monthpicker"))
    monthDropdown.select_by_value("none")
    yearDropdown = Select(wait_for_element(By.NAME, "list_yearpicker"))
    yearDropdown.select_by_value("none")
    time.sleep(2)

# Verify filter by task name and employee name
    
    wait_for_element(By.NAME, "filter").send_keys("Selenium Test")
    time.sleep(4)

    # Verifying placeholder holds the actual input value after results are loaded
    filter_value= wait_for_element(By.NAME, "filter").get_attribute("value")
    assert "Selenium Test" in filter_value
    print(filter_value)
    # Verifying the result contains the expected one
    timeslip_result= wait_for_element(By.XPATH, "//td[contains(text(),'Selenium Test')]").text
    print (timeslip_result)
    # Verifying using backspace in the filter placeholder
    wait_for_element(By.NAME, "filter").click()
    wait_for_element(By.NAME, "filter").send_keys(Keys.END)
    length = len(wait_for_element(By.NAME, "filter").get_attribute("value"))
    wait_for_element(By.NAME, "filter").send_keys(Keys.BACKSPACE * length)
    time.sleep(4)
    filter_value_after_backspace = wait_for_element(By.NAME, "filter").get_attribute("value")
    assert "" in filter_value_after_backspace
    print(filter_value_after_backspace)

# Verify filter by week, month and year
    ## Verify with year
    yearDropdown = Select(wait_for_element(By.NAME, "list_yearpicker"))
    yearDropdown.select_by_value("2020")
    time.sleep(4) 
      # Confirming the input is stable
    yearPicker_value= wait_for_element(By.NAME, "list_yearpicker").get_attribute("value")
    print("Selected year:", yearPicker_value)
    assert "2020" in yearPicker_value
    yearPicker_result= wait_for_element(By.XPATH, "//td[contains(text(),'01/15/2020')]").text
    print (yearPicker_result)

      # Verifying the results by change the filter input
    year_Dropdown = Select(wait_for_element(By.NAME, "list_yearpicker"))
    year_Dropdown.select_by_value("2023")
    try:
        time.sleep(2)
        year23_result= wait_for_element(By.XPATH, "//td[contains(text(),'01/15/2020')]").text
    except (NoSuchElementException, TimeoutException):
        year23_result = None

    if year23_result:
        print("Year search included irrelevant result:", year23_result)
    else:
        print("No irrelevant Year data found in the search results.")

      # Undo year change to continue test
    year_dropdown = Select(wait_for_element(By.NAME, "list_yearpicker"))
    year_dropdown.select_by_value("2020")



    ## Verify with month
    time.sleep(2)
    monthDropdown = Select(wait_for_element(By.NAME, "list_monthpicker"))
    monthDropdown.select_by_value("1")
    time.sleep(4)
       # Confirming the input is stable
    monthPicker_value= wait_for_element(By.NAME, "list_monthpicker").get_attribute("value")
    print("Selected month:", monthPicker_value)
    assert "1" in monthPicker_value
       # Verifying the results
    monthPicker_result= wait_for_element(By.XPATH, "//td[contains(text(),'01/15/2020')]").text
    print (monthPicker_result)

      # Verifying the results by change the filter input
    month_Dropdown = Select(wait_for_element(By.NAME, "list_monthpicker"))
    month_Dropdown.select_by_value("11")
    try:
        time.sleep(2)
        month11_result= wait_for_element(By.XPATH, "//td[contains(text(),'01/15/2020')]").text
    except (NoSuchElementException, TimeoutException):
        month11_result = None

    if month11_result:
        print("Month search included irrelevant result:", month11_result)
    else:
        print("No irrelevant month data found in the search results.")

      # Undo month change to continue test
    month_dropdown = Select(wait_for_element(By.NAME, "list_monthpicker"))
    month_dropdown.select_by_value("1")


    ## Verify with week
    weekDropdown = Select(wait_for_element(By.NAME, "list_week"))
    weekDropdown.select_by_value("3")
       # Confirming the input is stable
    weekPicker_value= wait_for_element(By.NAME, "list_week").get_attribute("value")
    print("Selected week:", weekPicker_value)
    assert "3" in weekPicker_value
       # Verifying the results
    week3_result= wait_for_element(By.XPATH, "//td[contains(text(),'3')]").text
    print (week3_result)
      # Verifying the results by change the filter input
    week_Dropdown = Select(wait_for_element(By.NAME, "list_week"))
    week_Dropdown.select_by_value("13")
    try:
        time.sleep(2)
        week13_result = wait_for_element(By.XPATH, "//td[contains(text(),'3')]").text
    except (NoSuchElementException,TimeoutException):
        week13_result = None

    if week13_result:
        print("Week search included irrelevant result:", week13_result)
    else:
        print("No irrelevant week data found in the search results.")

    
 # Verifying the reset button

    # fetching the current year and month
    todays_date = date.today()
    current_month = str(todays_date.month)
    current_year = str(todays_date.year)
    print(current_month)
    print(current_year)

    wait_for_element(By.XPATH, "//a[@href='/timeslips?reset=1']").click()
    time.sleep(4)
    year_Picker_value= wait_for_element(By.NAME, "list_yearpicker").get_attribute("value")
    assert current_year in year_Picker_value
    month_Picker_value= wait_for_element(By.NAME, "list_monthpicker").get_attribute("value")
    assert current_month in month_Picker_value
    week_Picker_value= wait_for_element(By.NAME, "list_week").get_attribute("value")
    assert "none" in week_Picker_value
    filter_Text_value= wait_for_element(By.NAME, "filter").get_attribute("value")
    assert "" in filter_Text_value




def test_timeslipsActions(setup, request):
    driver = request.module.driver
    wait = WebDriverWait(driver, 15)

    def wait_for_element(by, selector):
      element = wait.until(expected_conditions.presence_of_element_located((by, selector)))
      return element
    
    wait_for_element(By.XPATH, "//a[@href='/timeslips']").click()
    Select(wait_for_element(By.NAME, "list_monthpicker")).select_by_value("none")
    Select(wait_for_element(By.NAME, "list_yearpicker")).select_by_value("none")
    time.sleep(2)


 # Verifying Clone action
    time.sleep(2)
    driver.execute_script("arguments[0].scrollIntoView();",  wait_for_element(By.XPATH, "//tr[contains(., 'Selenium Test')]"))
    drop_down = driver.find_element(By.XPATH, "//tr[contains(., 'Selenium Test')]//span[@id='dropdownMenuButton']")
    drop_down.click()
    time.sleep(2)
    clone_button = wait_for_element(By.LINK_TEXT, "Clone")
    time.sleep(2)
    clone_button.click()
    time.sleep(2)
    clone_success_alert = wait_for_element(By.CSS_SELECTOR, ".alert").text
    print(clone_success_alert)
    assert "Data cloned Successfully!" in clone_success_alert

    # Verifying Edit action
    wait_for_element(By.XPATH, "//a[@href='/timeslips']").click()
    time.sleep(2)
    driver.execute_script("arguments[0].scrollIntoView();",  wait_for_element(By.XPATH, "//tr[contains(., 'Selenium Test')]"))
    drop_Down = driver.find_element(By.XPATH, "//tr[contains(., 'Selenium Test')]//span[@id='dropdownMenuButton']")
    drop_Down.click()
    time.sleep(2)
    edit_button = wait_for_element(By.LINK_TEXT, "Edit")
    edit_button.click()
    time.sleep(2)

    driver.execute_script("arguments[0].scrollIntoView();",  wait_for_element(By.NAME, "slip_description"))
    wait_for_element(By.NAME, "slip_description").send_keys("This is a updated test description.")
    wait_for_element(By.XPATH, "//button[@type='submit']").click()

    edit_success_alert = wait_for_element(By.CSS_SELECTOR, ".alert").text
    print(edit_success_alert)
    assert "Data inserted Successfully!" in edit_success_alert
    
      # Double check by verifying the changes are there 
    wait_for_element(By.XPATH, "//td[contains(text(),'Selenium Test')]").click()
    time.sleep(2)
    driver.execute_script("arguments[0].scrollIntoView();",  wait_for_element(By.NAME, "slip_description"))
    updated_text = wait_for_element(By.NAME, "slip_description").text
    assert "updated" in updated_text


    # Verifying Delete action
    wait_for_element(By.XPATH, "//a[@href='/timeslips']").click()
    time.sleep(2)
    driver.execute_script("arguments[0].scrollIntoView();",  wait_for_element(By.XPATH, "//tr[contains(., 'Selenium Test')]"))
    dropDown = driver.find_element(By.XPATH, "//tr[contains(., 'Selenium Test')]//span[@id='dropdownMenuButton']")
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


    # Delete cloned timeslips
    driver.execute_script("arguments[0].scrollIntoView();",  wait_for_element(By.XPATH, "//tr[contains(., 'Selenium Test')]"))
    dropDown = driver.find_element(By.XPATH, "//tr[contains(., 'Selenium Test')]//span[@id='dropdownMenuButton']")
    dropDown.click()
    time.sleep(2)
    delete_button = wait_for_element(By.LINK_TEXT, "Delete")
    delete_button.click()

    time.sleep(4)
    alert = driver.switch_to.alert
    alert.accept()
    time.sleep(2)
    delete_success_alert = wait_for_element(By.CSS_SELECTOR, ".alert").text
    assert "deleted Successfully!" in delete_success_alert


    
   





 







