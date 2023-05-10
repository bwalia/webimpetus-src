package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io/ioutil"
	"mime/multipart"
	"net/http"
	"os"
	"strings"
	"testing"
)

var tokenValue string
var userId string
var employeeId string
var email = os.Getenv("QA_LOGIN_EMAIL")
var password = os.Getenv("QA_LOGIN_PASSWORD")
var businessId = os.Getenv("QA_BUSINESS_ID")

const targetHost = "https://test-my.workstation.co.uk"

func TestHealthCheck(t *testing.T) {
	url := targetHost + "/api/v1/ping"

	client := &http.Client{}

	req, err := http.NewRequest("GET", url, nil)
	if err != nil {
		t.Log(err)
		return
	}

	res, err := client.Do(req)
	if err != nil {
		t.Log(err)
		return
	}
	defer res.Body.Close()

	var data map[string]interface{}
	err = json.NewDecoder(res.Body).Decode(&data)
	if err != nil {
		t.Errorf("Failed to decode JSON: %v", err)
	}

	expectedResp := "pong"
	if data["response"] != expectedResp {
		t.Errorf("Returned unexpected response")
	} else {
		t.Log("Received response pong")
	}
	if res.StatusCode != http.StatusOK {
		t.Errorf("Returned wrong status code: got %v want %v", res.StatusCode, http.StatusOK)
	}
}

func TestLoginAuth(t *testing.T) {
	url := targetHost + "/auth/login"

	payload := &bytes.Buffer{}
	writer := multipart.NewWriter(payload)
	_ = writer.WriteField("email", email)
	_ = writer.WriteField("password", password)
	err := writer.Close()
	if err != nil {
		t.Log(err)
		return
	}

	client := &http.Client{}
	req, err := http.NewRequest("POST", url, payload)

	if err != nil {
		t.Log(err)
		return
	}
	req.Header.Set("Content-Type", writer.FormDataContentType())
	resp, err := client.Do(req)
	if err != nil {
		t.Log(err)
		return
	}
	//t.Log(resp)
	body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		t.Log(err)
		return
	}

	if !strings.Contains(string(body), "authenticated successfully") {
		t.Errorf("Returned unexpected body")
	}

	if resp.StatusCode != http.StatusOK {
		t.Errorf("Returned wrong status code: got %v want %v", resp.StatusCode, http.StatusOK)
	}
	//t.Log(string(body))

}

func TestFetchToken(t *testing.T) {

	type AuthResponse struct {
		AccessToken string `json:"access_token"`
	}

	url := "https://test-my.workstation.co.uk/auth/login"

	payload := &bytes.Buffer{}
	writer := multipart.NewWriter(payload)
	_ = writer.WriteField("email", email)
	_ = writer.WriteField("password", password)
	err := writer.Close()
	if err != nil {
		fmt.Println(err)
		return
	}

	client := &http.Client{}
	req, err := http.NewRequest("POST", url, payload)

	if err != nil {
		t.Log(err)
		return
	}
	req.Header.Set("Content-Type", writer.FormDataContentType())
	resp, err := client.Do(req)
	if err != nil {
		t.Log(err)
		return
	}
	defer resp.Body.Close()

	var decodeValue AuthResponse
	err = json.NewDecoder(resp.Body).Decode(&decodeValue)

	tokenValue = decodeValue.AccessToken
}

func TestGetAllUsers(t *testing.T) {
	//t.Log(tokenValue)

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
	if false {
		t.Log(string(body))
	}
	if resp.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", resp.StatusCode)
		return
	}
}

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
	_ = writer.WriteField("email", "test.4@testing.com")
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
		userId = jsonData.Data.UUID
		//t.Log(userId)
	}

}
func TestUpdateUsers(t *testing.T) {

	url := targetHost + "/api/v2/users/"
	//t.Log(userId)

	type UserData struct {
		UUID  string `json:"uuid"`
		Email string `json:"email"`
		Name  string `json:"name"`
	}
	data := UserData{
		UUID:  userId,
		Email: "test.4@testing.com",
		Name:  "dixanew",
	}
	//t.Log(data)
	jsonData, err := json.Marshal(data)
	if err != nil {
		t.Error("Error marshaling data:", err)
	}
	//t.Log(jsonData)
	client := &http.Client{}

	req, err := http.NewRequest("PUT", url+userId, bytes.NewBuffer(jsonData))
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
	if !strings.Contains(string(body), "dixanew") {
		t.Error("Returned unexpected body")
	}

}
func TestDeleteUsers(t *testing.T) {
	url := targetHost + "/api/v2/users/"
	client := &http.Client{}

	req, err := http.NewRequest("DELETE", url+userId, nil)
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
	}

}
func TestGetSingleUser(t *testing.T) {
	url := targetHost + "/api/v2/users/"
	//t.Log(tokenValue)

	req, err := http.NewRequest("GET", url+userId, nil)
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
}

func TestGetAllEmployees(t *testing.T) {
	//t.Log(tokenValue)

	req, err := http.NewRequest("GET", targetHost+"/api/v2/employees", nil)
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
	}
}

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
	_ = writer.WriteField("uuid_business_id", "a2d14181-c413-5e36-98ae-131e975c744e")
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

	var jsonData Employee
	err = json.NewDecoder(buff).Decode(&jsonData)
	if err != nil {
		t.Error("failed to decode json", err)
	} else {
		employeeId = jsonData.Data.UUID
		//t.Log(employeeId)
	}

}
func TestUpdateEmployees(t *testing.T) {

	url := targetHost + "/api/v2/employees/"
	//t.Log(employeeId)

	type EmployeeData struct {
		Surname          string `json:"surname"`
		Uuid_business_id string `json:"uuid_business_id"`
		UUID             string `json:"uuid"`
	}
	data := EmployeeData{
		Surname:          "newtest",
		UUID:             employeeId,
		Uuid_business_id: "a2d14181-c413-5e36-98ae-131e975c744e",
	}

	//t.Log(data)
	jsonData, err := json.Marshal(data)
	if err != nil {
		t.Error("Error marshaling data:", err)
	}
	//t.Log(jsonData)
	client := &http.Client{}

	req, err := http.NewRequest("PUT", url+employeeId, bytes.NewBuffer(jsonData))
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
	if !strings.Contains(string(body), "newtest") {
		t.Error("Returned unexpected body")
	}

}
func TestDeleteEmployees(t *testing.T) {
	url := targetHost + "/api/v2/employees/"
	client := &http.Client{}

	req, err := http.NewRequest("DELETE", url+employeeId, nil)
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
	}

}
func TestGetSingleEmployee(t *testing.T) {
	url := targetHost + "/api/v2/employees/"

	req, err := http.NewRequest("GET", url+employeeId, nil)
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

}
