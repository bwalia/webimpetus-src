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

var businessesId string

func TestGetAllBusinesses(t *testing.T) {

	req, err := http.NewRequest("GET", targetHost+"/api/v2/businesses", nil)
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
		businessesId = jsonData.Data.UUID
		//t.Log(businessesId)
	}
}

func TestUpdateBusiness(t *testing.T) {

	url := targetHost + "/api/v2/businesses/"

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

	req, err := http.NewRequest("PUT", url+businessesId, bytes.NewBuffer(jsonData))
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
	if !strings.Contains(string(body), "business renew") {
		t.Error("Returned unexpected body")
	}

}

func TestDeleteBusiness(t *testing.T) {
	url := targetHost + "/api/v2/businesses/"
	client := &http.Client{}

	req, err := http.NewRequest("DELETE", url+businessesId, nil)
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
func TestGetSingleBusiness(t *testing.T) {
	url := targetHost + "/api/v2/businesses/"

	req, err := http.NewRequest("GET", url+businessesId, nil)
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
	if !strings.Contains(string(body), "null") {
		t.Error("Returned unexpected body")
	}
}
