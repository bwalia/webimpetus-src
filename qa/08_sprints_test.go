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

var sprintId string

// Calling the Sprints API for GET method to get all sprints data
func TestGetAllSprints(t *testing.T) {
	//t.Log(tokenValue)

	req, err := http.NewRequest("GET", targetHost+"/api/v2/sprints", nil)
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
		t.Log("Successfully get all sprints data")
	}
}

// Calling the Sprints API for POST method to create a new Sprint
func TestCreateSprint(t *testing.T) {

	url := targetHost + "/api/v2/sprints/"
	method := "POST"

	type Sprints struct {
		Data struct {
			ID int `json:"id"`
		} `json:"data"`
	}

	payload := &bytes.Buffer{}
	writer := multipart.NewWriter(payload)
	_ = writer.WriteField("sprint_name", "test sprint")
	_ = writer.WriteField("start_date", "10/11/2023")
	_ = writer.WriteField("end_date", "12/11/2023")
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

	var jsonData Sprints
	err = json.NewDecoder(buf).Decode(&jsonData)
	if err != nil {
		t.Error("failed to decode json", err)
	} else {
		// Getting the uuid of the sprint created
		sprintId = strconv.Itoa(jsonData.Data.ID)
		//t.Log(sprintId)
	}
	if res.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", res.StatusCode)
		return
	}
	if !strings.Contains(string(body), "test sprint") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully created a new sprint")
	}
}

// Calling the Sprint API for PUT method to update the single sprint data with the uuid
func TestUpdateSprint(t *testing.T) {

	url := targetHost + "/api/v2/sprints/" + sprintId

	type SprintData struct {
		ID           string `json:"id"`
		Sprintname   string `json:"sprint_name"`
		StartDate    string `json:"start_date"`
		EndDate      string `json:"end_date"`
		BusinessUuid string `json:"uuid_business_id"`
	}
	data := SprintData{
		ID:           sprintId,
		Sprintname:   "new sprint",
		BusinessUuid: businessId,
		StartDate:    "09/02/2023",
		EndDate:      "11/02/2023",
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
	if !strings.Contains(string(body), "new sprint") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully updated the sprint")
	}
}

// Calling the sprint API for DELETE method to delete the single sprint data with uuid
func TestDeleteSprint(t *testing.T) {
	url := targetHost + "/api/v2/sprints/" + sprintId
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
		t.Log("Successfully deleted the sprint")
	}
}

// Calling the Sprints API for GET method to get single sprint data with UUID
func TestGetSingleSprint(t *testing.T) {
	url := targetHost + "/api/v2/sprints/" + sprintId
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
	// With the 'null' in response body, it will verify the sprint data is deleted successfully
	if !strings.Contains(string(body), "null") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("The delete action for the sprints is verified")
	}
}
