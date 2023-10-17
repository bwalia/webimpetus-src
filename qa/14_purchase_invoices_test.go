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

var purchaseInvoicesUUId string

// Calling the purchase_invoices API for GET method to get all purchase_invoices data
func TestGetAllPurchaseInvoices(t *testing.T) {
	url := targetHost + "/api/v2/purchase_invoices"
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
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", resp.StatusCode)
		return
	} else {
		t.Log("Successfully get the purchase invoices data")

	}
}

//Calling the purchase_invoices API for POST method to create a new purchase invoice
func TestCreatePurchaseInvoice(t *testing.T) {

	url := targetHost + "/api/v2/purchase_invoices/"
	method := "POST"

	type Purchase_invoice struct {
		Data struct {
			UUID string `json:"uuid"`
		} `json:"data"`
	}

	payload := &bytes.Buffer{}
	writer := multipart.NewWriter(payload)
	_ = writer.WriteField("terms", "net 20")
	_ = writer.WriteField("uuid_business_id", businessId)
	_ = writer.WriteField("date", "12/02/2023")
	_ = writer.WriteField("due_date", "12/04/2023")
	_ = writer.WriteField("supplier", clientInternalId)
	_ = writer.WriteField("project_code", "4D")
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

	// Getting the uuid of the Purchase_invoice created
	buffer := bytes.NewBuffer(body)

	var jsonData Purchase_invoice

	err = json.NewDecoder(buffer).Decode(&jsonData)
	if err != nil {
		t.Log("failed to decode json", err)
	} else {
		purchaseInvoicesUUId = jsonData.Data.UUID
		//t.Log(purchaseInvoicesUUId)
	}
	if !strings.Contains(string(body), clientInternalId) {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully created a new purchase invoice")
	}

}

// Calling the purchase_invoices API for PUT method to update the single purchase_invoice data with the uuid
func TestUpdatePurchaseInvoice(t *testing.T) {

	url := targetHost + "/api/v2/purchase_invoices/" + purchaseInvoicesUUId

	type PurchaseInvoiceData struct {
		UUID         string `json:"uuid"`
		Terms        string `json:"terms"`
		BusinessUuid string `json:"uuid_business_id"`
	}
	data := PurchaseInvoiceData{
		UUID:         purchaseInvoicesUUId,
		Terms:        "net 15",
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

	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", resp.StatusCode)
		return
	}
	if !strings.Contains(string(body), "net 15") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully updated the purchase invoice")

	}

}

// Calling the purchase_invoices API for DELETE method to delete the single purchase_invoice data with uuid
func TestDeletePurchaseInvoice(t *testing.T) {
	url := targetHost + "/api/v2/purchase_invoices/" + purchaseInvoicesUUId
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
	body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		t.Log(err)
	}
	defer resp.Body.Close()

	if !strings.Contains(string(body), "true") {
		t.Error("Returned unexpected body")
	}
	if resp.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", resp.StatusCode)
		return
	} else {
		t.Log("Successfully deleted the purchase invoice")
	}
}

// Calling the purchase_invoices API for GET method to get single purchase_invoice data with UUID
func TestGetSinglePurchaseInvoice(t *testing.T) {
	url := targetHost + "/api/v2/purchase_invoices/" + purchaseInvoicesUUId

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
	defer resp.Body.Close()
	if !strings.Contains(string(body), "null") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("The delete action for the purchase invoice is verified")
	}
}
