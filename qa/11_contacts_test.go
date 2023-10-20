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

var contactId string

// Calling the Contacts API for GET method to get all contacts data
func TestGetAllContacts(t *testing.T) {
	url := targetHost + fmt.Sprintf("/api/v2/contacts?_format=json&params={\"pagination\":{\"page\":1,\"perPage\":12},\"sort\":{\"field\":\"id\",\"order\":\"ASC\"},\"filter\":{\"uuid_business_id\":\"%s\"}}", businessId)

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
	if err != nil {
		t.Log(err)
	}
	if false {
		t.Log(string(body))
	}

	if res.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", res.StatusCode)
	} else {
		t.Log("Successfully got the contacts data")

	}
}

// Calling the Contacts API for POST method to create a new contact
func TestCreateContact(t *testing.T) {

	url := targetHost + "/api/v2/contacts"
	method := "POST"

	type Contacts struct {
		Data struct {
			UUID string `json:"uuid"`
		} `json:"data"`
	}

	payload := &bytes.Buffer{}
	writer := multipart.NewWriter(payload)
	_ = writer.WriteField("first_name", "Tester")
	_ = writer.WriteField("client_id", customerId)
	_ = writer.WriteField("uuid_business", businessId)
	_ = writer.WriteField("email", "tester@gmail.com")
	_ = writer.WriteField("password", "testerpassword")
	_ = writer.WriteField("address_line_1[]", "test address")
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

	// Getting the uuid of the contact created
	var jsonData Contacts
	err = json.NewDecoder(buff).Decode(&jsonData)
	if err != nil {
		t.Log("failed to decode json", err)
		return
	} else {
		contactId = jsonData.Data.UUID
		//t.Log(contactId)
	}
	if res.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", res.StatusCode)
		return
	}
	if !strings.Contains(string(body), "Tester") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully created a new contact")
	}

}

// Calling the Contact API for DELETE method to delete the single contact data with uuid
func TestDeleteContacts(t *testing.T) {
	url := targetHost + "/api/v2/contacts/" + contactId

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
	if !strings.Contains(string(body), "true") {
		t.Error("Returned unexpected body")
	}

	if res.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", res.StatusCode)
	} else {
		t.Log("Successfully deleted the Contact")
	}
}

// Calling the Contact API for GET method to get single Contact data with UUID
func TestGetSingleContact(t *testing.T) {
	url := targetHost + "/api/v2/contacts/" + contactId

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
	// With the 'null' in response body, it will verify the contact data is deleted successfully
	if !strings.Contains(string(body), "null") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("The delete action for the contacts is verified")

	}
}
