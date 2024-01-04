package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io/ioutil"
	"mime/multipart"
	"net/http"
	"strings"
	"testing"
)

var timeslipsId string

func TestGetAllTimeslips(t *testing.T) {
	url := targetHost + fmt.Sprintf("/api/v2/timeslips?_format=json&params={\"pagination\":{\"page\":1,\"perPage\":12},\"sort\":{\"field\":\"id\",\"order\":\"ASC\"},\"filter\":{\"uuid_business_id\":\"%s\"}}", businessId)

	req, err := http.NewRequest("GET", url, nil)
	if err != nil {
		t.Log(err)
		return
	}
	client := &http.Client{}
	req.Header.Add("Authorization", "Bearer "+tokenValue)
	res, err := client.Do(req)
	if err != nil {
		t.Log(err)
		return
	}
	body, err := ioutil.ReadAll(res.Body)
	if false {
		t.Log(string(body))
	}

	if res.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", res.StatusCode)
	} else {
		t.Log("Successfully get the timeslips data")
	}
}

//Calling the timeslips API for POST method to create a new timeslip
func TestCreateTimeslips(t *testing.T) {
	url := targetHost + "/api/v2/timeslips"
	method := "POST"

	type Timeslips struct {
		Data struct {
			UUID string `json:"uuid"`
		} `json:"data"`
	}

	payload := &bytes.Buffer{}
	writer := multipart.NewWriter(payload)
	_ = writer.WriteField("task_id", taskId)
	_ = writer.WriteField("week_no", "42")
	_ = writer.WriteField("uuid_business_id", businessId)
	_ = writer.WriteField("employee_id", employeeId)
	_ = writer.WriteField("reported_by", userId)
	_ = writer.WriteField("slip_start_date", "10/18/2023")
	_ = writer.WriteField("slip_end_date", "10/18/2023")
	_ = writer.WriteField("slip_description", "xyz")
	_ = writer.WriteField("slip_timer_started", "09:01:00 am")
	_ = writer.WriteField("slip_timer_end", "09:01:00 pm")

	err := writer.Close()
	if err != nil {
		t.Log(err)
		return
	}

	client := &http.Client{}

	req, err := http.NewRequest(method, url, payload)
	if err != nil {
		t.Log(err)
	}

	req.Header.Add("Authorization", "Bearer "+tokenValue)
	req.Header.Set("Content-Type", writer.FormDataContentType())

	res, err := client.Do(req)
	if err != nil {
		t.Log(err)
		return
	}

	body, err := ioutil.ReadAll(res.Body)
	if err != nil {
		t.Log(err)
	}
	if false {
		t.Log(string(body))
	}

	buff := bytes.NewBuffer(body)
	defer res.Body.Close()

	//Getting the timeslips id
	var jsonData Timeslips
	err = json.NewDecoder(buff).Decode(&jsonData)
	if err != nil {
		t.Log("failed to decode json", err)
		return
	} else {
		timeslipsId = jsonData.Data.UUID
		//t.Log(timeslipsId)
	}
	if res.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", res.StatusCode)
		return
	}
	if !strings.Contains(string(body), "xyz") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully created a new timeslip")
	}

}

// Calling the Timeslips API for PUT method to update the single timeslip data with the uuid
func TestUpdateTimeslip(t *testing.T) {

	url := targetHost + "/api/v2/timeslips/" + timeslipsId
	//t.Log(url)

	type TimeslipsData struct {
		Task_id          string `json:"task_id"`
		Uuid_business_id string `json:"uuid_business_id"`
		UUID             string `json:"uuid"`
		SlipStartDate    string `json:"slip_start_date"`
		EmployeeId       string `json:"employee_id"`
		SlipEndDate      string `json:"slip_end_date"`
		SlipDescription  string `json:"slip_description"`
		SlipTimerStarted string `json:"slip_timer_started"`
		SlipTimerEnd     string `json:"slip_timer_end"`
		Week_no          string `json:"week_no"`
	}
	data := TimeslipsData{
		Task_id:          taskId,
		Uuid_business_id: businessId,
		UUID:             timeslipsId,
		SlipStartDate:    "11/03/2023",
		EmployeeId:       employeeId,
		SlipEndDate:      "12/03/2023",
		SlipDescription:  "abc",
		SlipTimerStarted: "08:15:08 am",
		SlipTimerEnd:     "08:15:08 pm",
		Week_no:          "42",
	}

	//t.Log(data)
	jsonData, err := json.Marshal(data)
	if err != nil {
		t.Error("Error marshaling data:", err)
	}
	//t.Log(jsonData)
	client := &http.Client{}

	req, err := http.NewRequest("PUT", url, bytes.NewBuffer(jsonData))
	if err != nil {
		t.Log(err)
		return
	}
	req.Header.Add("Authorization", "Bearer "+tokenValue)
	req.Header.Set("Content-Type", "application/json")
	resp, err := client.Do(req)
	if err != nil {
		t.Log(err)
		return
	}
	//t.Log(resp)

	body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		t.Log(err)
	}
	if false {
		t.Log(string(body))
	}

	if resp.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", resp.StatusCode)
		return
	}
	// Verify the updated body
	if !strings.Contains(string(body), "abc") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully updated timeslips data")
	}
}

//Calling the timeslips API for DELETE method to delete the single timeslips data with uuid
func TestDeleteTimeslips(t *testing.T) {
	url := targetHost + "/api/v2/timeslips/" + taskId

	req, err := http.NewRequest("DELETE", url, nil)
	if err != nil {
		t.Log(err)
		return
	}

	client := &http.Client{}

	req.Header.Add("Authorization", "Bearer "+tokenValue)
	res, err := client.Do(req)
	if err != nil {
		t.Error(err)
		return
	}
	body, err := ioutil.ReadAll(res.Body)
	if err != nil {
		t.Log(err)
	}
	//t.Log(string(body))
	defer res.Body.Close()
	if !strings.Contains(string(body), "true") {
		t.Error("Returned unexpected body")
	}

	if res.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", res.StatusCode)
	} else {
		t.Log("Successfully deleted the timeslips")
	}
}

// Calling the timeslips API for GET method to get single timeslip data with UUID
func TestGetSingleTimeslip(t *testing.T) {
	url := targetHost + "/api/v2/timeslips/" + taskId

	req, err := http.NewRequest("GET", url, nil)
	if err != nil {
		t.Log(err)
		return
	}
	client := &http.Client{}
	req.Header.Add("Authorization", "Bearer "+tokenValue)
	resp, err := client.Do(req)
	if err != nil {
		t.Log(err)
		return
	}
	//t.Log(resp)

	body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		t.Log(err)
	}
	if false {
		t.Log(string(body))
	}
	defer resp.Body.Close()
	// With the 'null' in response body, it will verify the timeslips data is deleted successfully
	if !strings.Contains(string(body), "null") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("The delete action for the timeslips is verified")

	}
}
