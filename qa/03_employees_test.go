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

var employeeId string

// Calling the Employees API for GET method to get all employees data
func TestGetAllEmployees(t *testing.T) {
	//t.Log(tokenValue)
	url := targetHost + fmt.Sprintf("/api/v2/employees?_format=json&params={\"pagination\":{\"page\":1,\"perPage\":12},\"sort\":{\"field\":\"id\",\"order\":\"ASC\"},\"filter\":{\"uuid_business_id\":\"%s\"}}", businessId)

	req, err := http.NewRequest("GET", url, nil)
	if err != nil {
		t.Log(err)
		return
	}

	client := &http.Client{}
	req.Header.Set("Authorization", "Bearer "+tokenValue)
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
	} else {
		t.Log("Successfully Get all employees data")

	}
}

// Calling the Employees API for POST method to create a new employee
func TestCreateEmployees(t *testing.T) {

	type Employee struct {
		Data struct {
			UUID string `json:"uuid"`
		} `json:"data"`
	}

	url := targetHost + "/api/v2/employees/"
	method := "POST"

	payload := &bytes.Buffer{}
	writer := multipart.NewWriter(payload)
	_ = writer.WriteField("first name", "test")
	_ = writer.WriteField("email", "test.2@testing.com")
	_ = writer.WriteField("uuid_business_id", businessId)
	err := writer.Close()
	if err != nil {
		t.Log(err)
		return
	}

	client := &http.Client{}
	req, err := http.NewRequest(method, url, payload)
	if err != nil {
		t.Log(err)
		return
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

	var jsonData Employee
	err = json.NewDecoder(buff).Decode(&jsonData)
	if err != nil {
		t.Error("failed to decode json", err)
	} else {
		// Getting the uuid of the employee created
		employeeId = jsonData.Data.UUID
		//t.Log(employeeId)
	}
	if !strings.Contains(string(body), "test") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully created a new employee")
	}

	if res.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", res.StatusCode)
		return
	}
}

// Calling the Employees API for PUT method to update the single employee data with the uuid
func TestUpdateEmployees(t *testing.T) {

	url := targetHost + "/api/v2/employees/" + employeeId
	//t.Log(url)

	type EmployeeData struct {
		Surname          string `json:"surname"`
		Uuid_business_id string `json:"uuid_business_id"`
		UUID             string `json:"uuid"`
	}
	data := EmployeeData{
		Surname:          "newtest",
		UUID:             employeeId,
		Uuid_business_id: businessId,
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
	if !strings.Contains(string(body), "newtest") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully updated employees data")
	}
}

// Calling the Employees API for DELETE method to delete the single employee data with uuid
func TestDeleteEmployees(t *testing.T) {
	url := targetHost + "/api/v2/employees/" + employeeId
	client := &http.Client{}

	req, err := http.NewRequest("DELETE", url, nil)
	if err != nil {
		t.Log(err)
		return
	}
	req.Header.Add("Authorization", "Bearer "+tokenValue)
	resp, err := client.Do(req)
	if err != nil {
		t.Log(err)
		return
	}
	//t.Log(resp)

	if resp.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", resp.StatusCode)
		return
	} else {
		t.Log("Successfully deleted the employee")
	}
}

// Calling the Employees API for GET method to get single employees data with UUID
func TestGetSingleEmployee(t *testing.T) {
	url := targetHost + "/api/v2/employees/" + employeeId

	req, err := http.NewRequest("GET", url, nil)
	if err != nil {
		t.Log(err)
		return
	}
	client := &http.Client{}
	req.Header.Set("Authorization", "Bearer "+tokenValue)
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
	// With the 'null' in response body, it will verify the employee data is deleted successfully
	if !strings.Contains(string(body), "null") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("The delete action for the employee is verified")

	}
}
