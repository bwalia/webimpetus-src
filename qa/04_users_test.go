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

var userId string

// Calling the Users API for GET method to get all users data
func TestGetAllUsers(t *testing.T) {

	req, err := http.NewRequest("GET", targetHost+"/api/v2/users", nil)
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
	// Verifying the status code
	if resp.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", resp.StatusCode)
		return
	} else {
		t.Log("Successfully get the users data")

	}
}

// Calling the Users API for POST method to create a new user
func TestCreateUser(t *testing.T) {

	type User struct {
		Data struct {
			UUID string `json:"uuid"`
		} `json:"data"`
	}

	url := targetHost + "/api/v2/users/"
	method := "POST"

	payload := &bytes.Buffer{}
	writer := multipart.NewWriter(payload)
	_ = writer.WriteField("name", "test")
	_ = writer.WriteField("email", "test.6@testing.com")
	_ = writer.WriteField("password", "test123")
	_ = writer.WriteField("uuid_business_id", businessId)
	err := writer.Close()
	if err != nil {
		fmt.Println(err)
		return
	}

	client := &http.Client{}
	req, err := http.NewRequest(method, url, payload)
	if err != nil {
		fmt.Println(err)
		return
	}

	req.Header.Add("Authorization", "Bearer "+tokenValue)
	req.Header.Set("Content-Type", writer.FormDataContentType())
	res, err := client.Do(req)
	if err != nil {
		fmt.Println(err)
		return
	}
	body, err := ioutil.ReadAll(res.Body)
	if false {
		t.Log(string(body))
	}
	buf := bytes.NewBuffer(body)
	defer res.Body.Close()

	var jsonData User
	err = json.NewDecoder(buf).Decode(&jsonData)
	if err != nil {
		t.Error("failed to decode json", err)
	} else {
		// Getting the uuid of the user created by the API call
		userId = jsonData.Data.UUID
		//t.Log(userId)
	}
	if !strings.Contains(string(body), "test") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully created a new user")
	}
	if res.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", res.StatusCode)
		return
	}
}

// Calling the Users API for PUT method to update the single user data with the uuid
func TestUpdateUsers(t *testing.T) {

	url := targetHost + "/api/v2/users/" + userId
	//t.Log(userId)

	type UserData struct {
		UUID  string `json:"uuid"`
		Email string `json:"email"`
		Name  string `json:"name"`
	}
	data := UserData{
		UUID:  userId,
		Email: "test.5@testing.com",
		Name:  "newuser",
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
	if !strings.Contains(string(body), "newuser") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully updated the user")
	}
}

// Calling the Users API for DELETE method to delete the single user data with uuid
func TestDeleteUsers(t *testing.T) {
	url := targetHost + "/api/v2/users/" + userId
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
		t.Log("Successfully deleted the user")
	}
}

// Calling the Users API for GET method to get single user data with UUID
func TestGetSingleUser(t *testing.T) {
	url := targetHost + "/api/v2/users/" + userId
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
	if err != nil {
		t.Log(err)
	}
	if false {
		t.Log(string(body))
	}
	// With the 'null' in response body, it will verify the users data is deleted successfully
	if !strings.Contains(string(body), "null") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("The delete action for the user is verified")
	}
}
