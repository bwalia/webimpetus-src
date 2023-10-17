package main

import (
	"bytes"
	"encoding/json"
	"io/ioutil"
	"mime/multipart"
	"net/http"
	"strings"
	"testing"
)

var taskId string

// Calling the Tasks API for GET method to get all tasks data
func TestGetAllTasks(t *testing.T) {
	url := targetHost + "/api/v2/tasks"

	req, err := http.NewRequest("GET", url, nil)
	if err != nil {
		t.Log(err)
		return
	}
	client := &http.Client{}
	req.Header.Set("Authorization", "Bearer "+tokenValue)
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

	if res.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", res.StatusCode)
	} else {
		t.Log("Successfully get the tasks data")
	}
}

//Calling the Tasks API for POST method to create a new task
func TestCreateTask(t *testing.T) {
	url := targetHost + "/api/v2/tasks"
	method := "POST"

	type Tasks struct {
		Data struct {
			UUID string `json:"uuid"`
		} `json:"data"`
	}

	payload := &bytes.Buffer{}
	writer := multipart.NewWriter(payload)
	_ = writer.WriteField("name", "Testing task")
	_ = writer.WriteField("customers_id", customerId)
	_ = writer.WriteField("uuid_business_id", businessId)
	_ = writer.WriteField("contacts_id", contactId)
	_ = writer.WriteField("reported_by", userId)
	_ = writer.WriteField("projects_id", projectId)
	_ = writer.WriteField("category", "review")
	_ = writer.WriteField("start_date", "20231012")
	_ = writer.WriteField("end_date", "20231013")
	_ = writer.WriteField("priority", "medium")
	_ = writer.WriteField("sprint_id", sprintId)

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

	req.Header.Set("Authorization", "Bearer "+tokenValue)
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

	//Getting the task id
	var jsonData Tasks
	err = json.NewDecoder(buff).Decode(&jsonData)
	if err != nil {
		t.Log("failed to decode json", err)
		return
	} else {
		taskId = jsonData.Data.UUID
		//t.Log(taskId)
	}
	if res.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", res.StatusCode)
		return
	}
	if !strings.Contains(string(body), "Testing") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully created a new task")
	}

}

// Calling the Task API for PUT method to update the single Tasks data with the uuid
func TestUpdateTasks(t *testing.T) {

	url := targetHost + "/api/v2/tasks/" + taskId
	//t.Log(url)

	type TasksData struct {
		Name             string `json:"name"`
		Uuid_business_id string `json:"uuid_business_id"`
		UUID             string `json:"uuid"`
		Customers_id     string `json:"customers_id"`
		Projects_id      string `json:"projects_id"`
		Contacts_id      string `json:"contacts_id"`
		Reported_by      string `json:"reported_by"`
		Category         string `json:"category"`
		Start_date       string `json:"start_date"`
		Priority         string `json:"priority"`
		End_date         string `json:"end_date"`
		Sprint_id        string `json:"sprint_id"`
	}
	data := TasksData{
		Name:             "Task update",
		UUID:             taskId,
		Uuid_business_id: businessId,
		Customers_id:     customerId,
		Projects_id:      projectId,
		Contacts_id:      contactId,
		Reported_by:      userId,
		Category:         "review",
		Start_date:       "20231012",
		Priority:         "medium",
		End_date:         "20231013",
		Sprint_id:        sprintId,
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
	if !strings.Contains(string(body), "Task update") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully updated tasks data")
	}
}

//Calling the Tasks API for DELETE method to delete the single tasks data with uuid
func TestDeleteTasks(t *testing.T) {
	url := targetHost + "/api/v2/tasks/" + taskId

	req, err := http.NewRequest("DELETE", url, nil)
	if err != nil {
		t.Log(err)
		return
	}

	client := &http.Client{}

	req.Header.Set("Authorization", "Bearer "+tokenValue)
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
		t.Log("Successfully deleted the Task")
	}
}

// Calling the Tasks API for GET method to get single task data with UUID
func TestGetSingleTask(t *testing.T) {
	url := targetHost + "/api/v2/tasks/" + taskId

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
	defer resp.Body.Close()
	// With the 'null' in response body, it will verify the task data is deleted successfully
	if !strings.Contains(string(body), "null") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("The delete action for the tasks is verified")

	}
}
