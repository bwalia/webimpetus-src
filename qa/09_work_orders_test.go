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

var workOrdersUUId string

// Calling the Work_orders API for GET method to get all Work orders data
func TestGetAllWorkOrders(t *testing.T) {
	//t.Log(tokenValue)

	req, err := http.NewRequest("GET", targetHost+"/api/v2/work_orders", nil)
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
		t.Log("Successfully get all work orders data")
	}
}

// Calling the Work_orders API for POST method to create a new Work order
func TestCreateWorkOrder(t *testing.T) {

	url := targetHost + "/api/v2/work_orders/"
	method := "POST"

	type Work_orders struct {
		Data struct {
			UUID string `json:"uuid"`
		} `json:"data"`
	}

	payload := &bytes.Buffer{}
	writer := multipart.NewWriter(payload)
	_ = writer.WriteField("client_id", customerId)
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

	defer res.Body.Close()

	buffer := bytes.NewBuffer(body)

	var jsonData Work_orders
	err = json.NewDecoder(buffer).Decode(&jsonData)
	if err != nil {
		t.Log("failed to decode json", err)
	} else {
		// Getting the uuid of the work order created
		workOrdersUUId = jsonData.Data.UUID
		//t.Log(workOrdersUUId)
	}

	if !strings.Contains(string(body), customerId) {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully created a new work order")
	}

	if res.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", res.StatusCode)
		return
	}
}

// Calling the Work_orders API for PUT method to update the single work order data with the uuid
func TestUpdateWorkOrders(t *testing.T) {

	url := targetHost + "/api/v2/work_orders/" + workOrdersUUId

	type WorkordersData struct {
		UUID         string `json:"uuid"`
		ClientId     string `json:"client_id"`
		BusinessUuid string `json:"uuid_business_id"`
		Bill_to      string `json:"bill_to"`
	}
	data := WorkordersData{
		UUID:         workOrdersUUId,
		ClientId:     customerId,
		BusinessUuid: businessId,
		Bill_to:      "Tester",
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
	//t.Log(string(body))

	if resp.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", resp.StatusCode)
		return
	}
	if !strings.Contains(string(body), "Tester") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully updated the work order")
	}
}

// Calling the Work_orders API for DELETE method to delete the single work order data with uuid
func TestDeleteWorkOrder(t *testing.T) {
	url := targetHost + "/api/v2/work_orders/" + workOrdersUUId
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
		t.Log("Successfully deleted the work order")
	}
}

// Calling the Work_orders API for GET method to get single work order data with UUID
func TestGetSingleWorkOrder(t *testing.T) {
	url := targetHost + "/api/v2/work_orders/" + workOrdersUUId
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
	// With the 'null' in response body, it will verify the work order data is deleted successfully
	if !strings.Contains(string(body), "null") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("The delete action for the work order is verified")
	}
}
