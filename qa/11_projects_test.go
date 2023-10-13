package main

import (
	"bytes"
	"encoding/json"
	"io/ioutil"
	"mime/multipart"
	"net/http"
	"strconv"
	"strings"
	"testing"
)

var projectId string

// Calling the Project API for GET method to get all projects data

func TestGetAllProjects(t *testing.T) {
	//t.Log(tokenValue)
	url := targetHost + "/api/v2/projects"
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
	if false {
		t.Log(string(body))
	}
	if resp.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", resp.StatusCode)
		return
	} else {
		t.Log("Successfully get the projects data")
	}
}

// Calling the Projects API for POST method to create a new project
func TestCreateProject(t *testing.T) {

	url := targetHost + "/api/v2/projects/"
	method := "POST"

	type Project struct {
		Data struct {
			ID int `json:"id"`
		} `json:"data"`
	}

	payload := &bytes.Buffer{}
	writer := multipart.NewWriter(payload)
	_ = writer.WriteField("name", "test project")
	_ = writer.WriteField("customers_id", customerId)
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

	buf := bytes.NewBuffer(body)
	defer res.Body.Close()

	var jsonData Project
	err = json.NewDecoder(buf).Decode(&jsonData)
	if err != nil {
		t.Error("failed to decode json", err)
	} else {
		projectId = strconv.Itoa(jsonData.Data.ID)
		t.Log(projectId)
	}

	if res.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", res.StatusCode)
		return
	}
	if !strings.Contains(string(body), "test project") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully created a new project")
	}
}

// Calling the Project API for PUT method to update the single project data with the uuid
func TestUpdateProjects(t *testing.T) {

	url := targetHost + "/api/v2/projects/" + projectId

	type projectData struct {
		ID           string `json:"id"`
		CustomersId  string `json:"customers_id"`
		Name         string `json:"name"`
		BusinessUuid string `json:"uuid_business_id"`
	}
	data := projectData{
		ID:           projectId,
		CustomersId:  customerId,
		Name:         "new project",
		BusinessUuid: businessId,
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

	if resp.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", resp.StatusCode)
		return
	}

	// Verify the updated body
	if !strings.Contains(string(body), "new project") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully updated the project")
	}
}

// Calling the Projects API for DELETE method to delete the single project data with uuid
func TestDeleteProjects(t *testing.T) {
	url := targetHost + "/api/v2/projects/" + projectId
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
		t.Log("Successfully deleted the project")
	}
}

// Calling the Project API for GET method to get single Project data with UUID
func TestGetSingleProject(t *testing.T) {
	url := targetHost + "/api/v2/projects/" + projectId
	//t.Log(tokenValue)

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
	if false {
		t.Log(string(body))
	}
	// With the 'null' in response body, it will verify the project data is deleted successfully
	if !strings.Contains(string(body), "null") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("The delete action for the project is verified")
	}
}
