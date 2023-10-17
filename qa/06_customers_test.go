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

var customerId string
var clientInternalId string

// Calling the Customers API for GET method to get all customers data
func TestGetAllCustomers(t *testing.T) {
	//t.Log(tokenValue)
	url := targetHost + "/api/v2/customers"

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
		t.Log("Successfully Get all customers data")

	}
}

// Calling the Employees API for POST method to create a new employee
func TestCreateCustomer(t *testing.T) {

	type Customer struct {
		Data struct {
			Client_id   int    `json:"internal_id"`
			Client_uuid string `json:"uuid"`
		} `json:"data"`
	}

	url := targetHost + "/api/v2/customers"
	method := "POST"

	payload := &bytes.Buffer{}
	writer := multipart.NewWriter(payload)
	_ = writer.WriteField("company_name", "Testing")
	_ = writer.WriteField("acc_no", "123456789012345")
	_ = writer.WriteField("uuid_business", businessId)
	_ = writer.WriteField("email", "test@gmail.com")
	_ = writer.WriteField("phone", "1234567890")
	_ = writer.WriteField("contact_firstname", "API Test")
	_ = writer.WriteField("status", "1")
	_ = writer.WriteField("supplier", "1")
	_ = writer.WriteField("first_name[0]", "Tester")
	_ = writer.WriteField("contact_email[0]", "test@test.com")

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

	var jsonData Customer
	err = json.NewDecoder(buff).Decode(&jsonData)
	if err != nil {
		t.Error("failed to decode json", err)
	} else {
		// Getting the uuid of the customer created
		clientInternalId = strconv.Itoa(jsonData.Data.Client_id)
		t.Log(customerId)
		customerId = jsonData.Data.Client_uuid
	}

	if !strings.Contains(string(body), "Testing") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully created a new customer")
	}

	if res.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", res.StatusCode)
		return
	}
}

// Calling the Customer API for PUT method to update the single Customers data with the uuid
func TestUpdateCustomers(t *testing.T) {

	url := targetHost + "/api/v2/customers/" + customerId
	//t.Log(url)

	type CustomersData struct {
		Company_name      string `json:"company_name"`
		Acc_no            string `json:"acc_no"`
		Contact_firstname string `json:"contact_firstname"`
		Email             string `json:"email"`
		Phone             string `json:"phone"`
		Supplier          string `json:"supplier"`
		Status            string `json:"status"`
		Uuid_business_id  string `json:"uuid_business"`
		UUID              string `json:"uuid"`
	}
	data := CustomersData{
		Company_name:      "Changed name",
		Acc_no:            "55343435545454",
		Contact_firstname: "API Test",
		Email:             "test@gmail.com",
		Phone:             "0123456789",
		Supplier:          "1",
		Status:            "1",
		UUID:              customerId,
		Uuid_business_id:  businessId,
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
	if !strings.Contains(string(body), "Changed name") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully updated customers data")
	}
}

// Calling the Customers API for DELETE method to delete the single customer data with uuid
func TestDeleteCustomers(t *testing.T) {
	url := targetHost + "/api/v2/customers/" + customerId
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
		t.Log("Successfully deleted the customer")
	}
}

// Calling the Customers API for GET method to get single customer data with UUID
func TestGetSingleCustomer(t *testing.T) {
	url := targetHost + "/api/v2/customers/" + customerId

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
	// With the 'null' in response body, it will verify the customers data is deleted successfully
	if !strings.Contains(string(body), "null") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("The delete action for the customers is verified")

	}
}
