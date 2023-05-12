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
	}
}

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
		purchaseOrdersUUId = jsonData.Data.UUID
		//t.Log(workOrdersUUId)
	}

}

func TestUpdatePurchaseOrders(t *testing.T) {

	url := targetHost + "/api/v2/purchase_orders/"

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

	req, err := http.NewRequest("PUT", url+purchaseOrdersUUId, bytes.NewBuffer(jsonData))
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
	}

}
func TestDeletePurchaseOrder(t *testing.T) {
	url := targetHost + "/api/v2/purchase_orders/"
	client := &http.Client{}

	req, err := http.NewRequest("DELETE", url+purchaseOrdersUUId, nil)
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
func TestGetSinglePurchaseOrder(t *testing.T) {
	url := targetHost + "/api/v2/purchase_orders/"
	//t.Log(tokenValue)

	req, err := http.NewRequest("GET", url+purchaseOrdersUUId, nil)
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
	if !strings.Contains(string(body), "null") {
		t.Error("Returned unexpected body")
	}
}
