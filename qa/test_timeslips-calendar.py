import time
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions
from selenium.webdriver.support.wait import WebDriverWait
from selenium.webdriver import Keys
from datetime import timedelta
from dateutil import relativedelta
import datetime
from selenium.webdriver import ActionChains


current_time = datetime.datetime.now()
today_date =  current_time.strftime("%-d")


def test_timeslipsCalendar(setup, request):
    driver = request.module.driver
    wait = WebDriverWait(driver, 15)

    def wait_for_element(by, selector):
      element = wait.until(expected_conditions.presence_of_element_located((by, selector)))
      return element


    # Verifying user lands on the current month when opens the calendar
    wait_for_element(By.XPATH, "//a[@href='/fullcalendar']").click()
    time.sleep(2)
    title = wait_for_element(By.ID, "fc-dom-1").text
    print(title)
    expected_title = current_time.strftime("%B %Y")
    assert expected_title in title

# Verifying 'Previous month', 'Today' and 'Next month' button
    # Verifying previous month button
    wait_for_element(By.XPATH, "//button[@title='Previous month']").click()
    time.sleep(2)
    previous_month_title = wait_for_element(By.ID, "fc-dom-1").text
      # Compairing the output with the expected
    current_month = current_time.replace(day=1)
    last_month = current_month - datetime.timedelta(days=1)
    previous_month = last_month.strftime("%B %Y")
    print(previous_month_title)

    assert previous_month in previous_month_title


    # Verifying 'Today' button
    wait_for_element(By.XPATH, "//button[@title='This month']").click()
    this_month_title = wait_for_element(By.ID, "fc-dom-1").text
    print(this_month_title)
    expected_month_title = current_time.strftime("%B %Y")

    assert expected_month_title in this_month_title


    # Verifying next month button
    wait_for_element(By.XPATH, "//button[@title='Next month']").click()
    time.sleep(2)
    next_month_title = wait_for_element(By.ID, "fc-dom-1").text
    print(next_month_title)
    next_month = datetime.date.today() + relativedelta.relativedelta(months=1)
    expected_next_month_title = next_month.strftime("%B %Y")

    assert expected_next_month_title in next_month_title


# Verifying month, week, day and list view
    # Going back to today
    wait_for_element(By.XPATH, "//button[@title='This month']").click()

    # Verifying the month view
    wait_for_element(By.XPATH, "//button[@title='month view']").click()
    time.sleep(2)
    month_view_title = wait_for_element(By.ID, "fc-dom-1").text
    expected_month_view_title = current_time.strftime("%B %Y")
    assert expected_month_view_title in month_view_title
    
    # Verifying the week view
    wait_for_element(By.XPATH, "//button[@title='week view']").click()
    time.sleep(2)
    week_view_title = wait_for_element(By.ID, "fc-dom-1").text

      # Compairing with the expected title
    start_of_week = current_time - timedelta(days=current_time.weekday()+1)
    end_of_week = start_of_week + timedelta(days=6)

    start_week_date = start_of_week.strftime("%d")
    end_week_date = end_of_week.strftime("%d")

    week_range = f"{start_week_date} – {end_week_date}"
    print(week_range)
    assert week_range in week_view_title


    # Verifying the day view
    wait_for_element(By.XPATH, "//button[@title='day view']").click()
    time.sleep(2)
    day_view_title = wait_for_element(By.ID, "fc-dom-1").text
    day_title = wait_for_element(By.XPATH, "//a[@class='fc-col-header-cell-cushion ']").text
    print(day_title)

      # Compairing the expected title
    expected_day_title = current_time.strftime("%A")
    assert expected_day_title in day_title

    day_view_title = wait_for_element(By.ID, "fc-dom-1").text
    print(today_date)
    assert today_date in day_view_title


    # Verifying the list view
    wait_for_element(By.XPATH, "//button[@title='list view']").click()
    time.sleep(2)
    list_view_title = wait_for_element(By.ID, "fc-dom-1").text

      # Compairing with the expected title
    start_Of_week = current_time - timedelta(days=current_time.weekday()+1)
    end_Of_week = start_of_week + timedelta(days=6)

    Start_week_date = start_Of_week.strftime("%d")
    End_week_date = end_Of_week.strftime("%d")

    Week_range = f"{Start_week_date} – {End_week_date}"
    assert Week_range in list_view_title


# Creating a new timeslip through timeslips calendar
    wait_for_element(By.XPATH, "//button[@title='month view']").click()
    time.sleep(2)

    action = ActionChains(driver)
    #driver.execute_script("arguments[0].scrollIntoView();",  wait_for_element(By.XPATH, "//a[contains(text(),'13')]"))

    obj = driver.find_element(By.XPATH, "//a[contains(text(),'14')]")
    action.move_to_element(obj).click().perform()
    wait_for_element(By.XPATH, "//div[@class='fc-daygrid-day-events']").click()
    time.sleep(2)
    wait_for_element(By.ID, "select2-task_name-container").click()
    driver.execute_script("arguments[0].scrollIntoView();",  wait_for_element(By.XPATH, "//li[contains(text(),'Selenium Test')]"))
    wait_for_element(By.XPATH, "//li[contains(text(),'Selenium Test')]").click()

    wait_for_element(By.ID, "select2-employee_name-container").click()
    wait_for_element(By.XPATH, "//li[contains(text(),'Tester')]").click()
    Slip_time_start = wait_for_element(By.NAME, "slip_timer_started")
    Slip_time_start.click()
    Slip_time_start.send_keys(Keys.END)
    length = len(Slip_time_start.get_attribute("value"))
    Slip_time_start.send_keys(Keys.BACKSPACE * length)
    Slip_time_start.send_keys("01:00:00 pm")
    driver.execute_script("arguments[0].scrollIntoView();",  wait_for_element(By.ID, "slip_description"))
    wait_for_element(By.ID, "slip_description").click()
    time.sleep(2)

    driver.execute_script("arguments[0].scrollIntoView();",  wait_for_element(By.NAME, "slip_timer_end"))
    Slip_time_end = wait_for_element(By.NAME, "slip_timer_end")
    Slip_time_end.click()
    Slip_time_end.send_keys(Keys.END)
    length = len(Slip_time_end.get_attribute("value"))
    Slip_time_end.send_keys(Keys.BACKSPACE * length)
    Slip_time_end.send_keys("06:00:00 pm")
    time.sleep(2)
    driver.execute_script("arguments[0].scrollIntoView();",  wait_for_element(By.ID, "slip_description"))
    wait_for_element(By.ID, "slip_description").send_keys("This is a test description")
    time.sleep(2)
    driver.execute_script("arguments[0].scrollIntoView();",  wait_for_element(By.XPATH, "//button[@class='btn btn-primary btn-color margin-right-5 btn-sm']"))
    wait_for_element(By.XPATH, "//button[@class='btn btn-primary btn-color margin-right-5 btn-sm']").click()
    time.sleep(2)



    


