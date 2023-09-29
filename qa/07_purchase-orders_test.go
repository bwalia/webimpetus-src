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

var purchaseOrdersUUId string

// Calling the Purchase_orders API for GET method to get all Purchase order data
func TestGetAllPurchaseOrders(t *testing.T) {
	//t.Log(tokenValue)

	req, err := http.NewRequest("GET", targetHost+"/api/v2/purchase_orders", nil)
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
		t.Log("Successfully get all purchase orders data")
	}
}

// Calling the Purchase_orders API for POST method to create a new Purchase order
func TestCreatePurchaseOrder(t *testing.T) {

	url := targetHost + "/api/v2/purchase_orders"
	method := "POST"

	type Purchase_orders struct {
		Data struct {
			UUID string `json:"uuid"`
		} `json:"data"`
	}

	payload := &bytes.Buffer{}
	writer := multipart.NewWriter(payload)
	_ = writer.WriteField("client_id", "14")
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
	if false {
		t.Log(string(body))
	}

	defer res.Body.Close()

	buffer := bytes.NewBuffer(body)

	var jsonData Purchase_orders
	err = json.NewDecoder(buffer).Decode(&jsonData)
	if err != nil {
		t.Log("failed to decode json", err)
	} else {
		// Getting the uuid of the Purchase order created
		purchaseOrdersUUId = jsonData.Data.UUID
		//t.Log(workOrdersUUId)
		t.Log("Successfully created a new purchase order")
	}
}

// Calling the Purchase_orders API for PUT method to update the single Purchase order data with the uuid
func TestUpdatePurchaseOrders(t *testing.T) {

	url := targetHost + "/api/v2/purchase_orders/" + purchaseOrdersUUId

	type PurchaseOrdersData struct {
		UUID         string `json:"uuid"`
		ClientId     string `json:"client_id"`
		BusinessUuid string `json:"uuid_business_id"`
	}
	data := PurchaseOrdersData{
		UUID:         purchaseOrdersUUId,
		ClientId:     "16",
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
	if !strings.Contains(string(body), "16") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully updated the purchase order")
	}
}

// Calling the Purchase_orders API for DELETE method to delete the single Purchase order data with uuid
func TestDeletePurchaseOrder(t *testing.T) {
	url := targetHost + "/api/v2/purchase_orders/" + purchaseOrdersUUId
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
		t.Log("Successfully deleted the purchase order")
	}
}

// Calling the purchase_orders API for GET method to get single purchase order data with UUID
func TestGetSinglePurchaseOrder(t *testing.T) {
	url := targetHost + "/api/v2/purchase_orders/" + purchaseOrdersUUId
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
	// With the 'null' in response body, it will verify the purchase_orders data is deleted successfully
	if !strings.Contains(string(body), "null") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("The delete action for the purchase order is verified")
	}
}
