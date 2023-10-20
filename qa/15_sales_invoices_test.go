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

var salesInvoicesUUId string

// Calling the sales_invoices API for GET method to get all sales_invoices data
func TestGetAllSalesInvoices(t *testing.T) {
	url := targetHost + fmt.Sprintf("/api/v2/sales_invoices?_format=json&params={\"pagination\":{\"page\":1,\"perPage\":12},\"sort\":{\"field\":\"id\",\"order\":\"ASC\"},\"filter\":{\"uuid_business_id\":\"%s\"}}", businessId)

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
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", resp.StatusCode)
		return
	} else {
		t.Log("Successfully get the sales_invoices data")

	}
}

//Calling the sales_invoices API for POST method to create a new sales invoice
func TestCreateSalesInvoice(t *testing.T) {
	url := targetHost + "/api/v2/sales_invoices/"
	method := "POST"

	type Sales_invoice struct {
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

	// Getting the uuid of the Sales_invoice created
	buffer := bytes.NewBuffer(body)

	var jsonData Sales_invoice

	err = json.NewDecoder(buffer).Decode(&jsonData)
	if err != nil {
		t.Log("failed to decode json", err)
	} else {
		salesInvoicesUUId = jsonData.Data.UUID
		//t.Log(salesInvoicesUUId)
	}
	if !strings.Contains(string(body), clientInternalId) {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully created a new sales invoice")
	}
}

// Calling the sales_invoices API for PUT method to update the single sales_invoice data with the uuid
func TestUpdateSalesInvoices(t *testing.T) {
	url := targetHost + "/api/v2/sales_invoices/" + salesInvoicesUUId

	type SalesInvoiceData struct {
		UUID         string `json:"uuid"`
		Terms        string `json:"terms"`
		BusinessUuid string `json:"uuid_business_id"`
	}

	Data := SalesInvoiceData{
		UUID:         salesInvoicesUUId,
		Terms:        "net 15",
		BusinessUuid: businessId,
	}

	jsonData, err := json.Marshal(Data)
	if err != nil {
		t.Error("Error marshaling data:", err)
	}
	client := &http.Client{}

	req, err := http.NewRequest("PUT", url, bytes.NewBuffer(jsonData))
	if err != nil {
		t.Log(err)
		return
	}
	req.Header.Add("Authorization", "Bearer "+tokenValue)
	req.Header.Set("Content-Type", "application/json")

	res, err := client.Do(req)
	if err != nil {
		t.Log(err)
	}
	body, err := ioutil.ReadAll(res.Body)
	if err != nil {
		t.Log(err)
	}
	if false {
		t.Log(string(body))
	}
	if !strings.Contains(string(body), "net 15") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("Successfully updated the sales invoice")
	}
	if res.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", res.StatusCode)
	}
}

// Calling the sales_invoices API for DELETE method to delete the single sales_invoice data with uuid
func TestDeleteSalesInvoice(t *testing.T) {
	url := targetHost + "/api/v2/sales_invoices/" + salesInvoicesUUId
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
		t.Log("Successfully deleted the sales invoice")
	}
}

// Calling the sales_invoices API for GET method to get single sales_invoice data with UUID
func TestGetSingleSalesInvoice(t *testing.T) {
	url := targetHost + "/api/v2/sales_invoices/" + salesInvoicesUUId

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
	defer resp.Body.Close()
	if !strings.Contains(string(body), "null") {
		t.Error("Returned unexpected body")
	} else {
		t.Log("The delete action for the sales invoice is verified")
	}
}
