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

var businessesId string

// Calling the Business API for GET method to get all businesses data
func TestGetAllBusinesses(t *testing.T) {
	url := targetHost + fmt.Sprintf("/api/v2/businesses?_format=json&params={\"pagination\":{\"page\":1,\"perPage\":12},\"sort\":{\"field\":\"id\",\"order\":\"ASC\"},\"filter\":{\"uuid_business_id\":\"%s\"}}", businessId)

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
	if false {
		t.Log(string(body))
	}
	if resp.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", resp.StatusCode)
		return
	}
}

// Calling the Business API for POST method to create a new business
func TestAddBusiness(t *testing.T) {

	type Business struct {
		Data struct {
			UUID string `json:"uuid"`
		} `json:"data"`
	}

	url := targetHost + "/api/v2/businesses"
	method := "POST"

	payload := &bytes.Buffer{}
	writer := multipart.NewWriter(payload)
	_ = writer.WriteField("business_code", "NB")
	_ = writer.WriteField("name", "New business")

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
	if false {
		t.Log(string(body))
	}
	buff := bytes.NewBuffer(body)
	defer res.Body.Close()

	var jsonData Business
	err = json.NewDecoder(buff).Decode(&jsonData)
	if err != nil {
		t.Error("failed to decode json", err)
	} else {
		// Getting the uuid of the business created
		businessesId = jsonData.Data.UUID
		//t.Log(businessesId)
	}
	if !strings.Contains(string(body), "New business") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully created a new business")
	}
	if res.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", res.StatusCode)
		return
	}
}

// Calling the Business API for PUT method to update the single business data with the uuid
func TestUpdateBusiness(t *testing.T) {

	url := targetHost + "/api/v2/businesses/" + businessesId

	type BusinessData struct {
		Name             string `json:"name"`
		Uuid_business_id string `json:"uuid_business_id"`
		UUID             string `json:"uuid"`
	}
	data := BusinessData{
		Name:             "business renew",
		UUID:             businessesId,
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
	if false {
		t.Log(string(body))
	}

	if resp.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", resp.StatusCode)
		return
	}
	// Verify the updated body
	if !strings.Contains(string(body), "business renew") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully updated business")
	}
}

// Calling the Business API for DELETE method to delete the single business data with uuid
func TestDeleteBusiness(t *testing.T) {
	url := targetHost + "/api/v2/businesses/" + businessesId
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
		t.Log("Successfully deleted business")
	}
}

// Calling the Business API for GET method to get single business data with UUID
func TestGetSingleBusiness(t *testing.T) {
	url := targetHost + "/api/v2/businesses/" + businessesId

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
	if false {
		t.Log(string(body))
	}
	// With the 'null' in response body, it will verify the Business data is deleted successfully
	if !strings.Contains(string(body), "null") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("The delete action for the Business is verified")
	}
}
