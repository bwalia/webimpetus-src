package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io/ioutil"
	"mime/multipart"
	"net/http"
	"strconv"
	"strings"
	"testing"
)

var projectId string

func TestGetAllProjects(t *testing.T) {
	//t.Log(tokenValue)

	req, err := http.NewRequest("GET", targetHost+"/api/v2/projects", nil)
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

func TestCreateProject(t *testing.T) {
	fmt.Println(tokenValue)

	url := targetHost + "/api/v2/projects/"
	method := "POST"

	type Project struct {
		Data struct {
			ID int `json:"id"`
		} `json:"data"`
	}

	payload := &bytes.Buffer{}
	writer := multipart.NewWriter(payload)
	_ = writer.WriteField("name", "test project")
	_ = writer.WriteField("customers_id", "fe416e2d-0afb-5c82-a077-66da33137e26")
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

	buf := bytes.NewBuffer(body)
	defer res.Body.Close()

	var jsonData Project
	err = json.NewDecoder(buf).Decode(&jsonData)
	if err != nil {
		t.Error("failed to decode json", err)
	} else {
		projectId = strconv.Itoa(jsonData.Data.ID)
		t.Log(projectId)
	}
	if res.StatusCode != http.StatusOK {
		t.Error("Unexpected response status code", res.StatusCode)
		return
	}
	if !strings.Contains(string(body), "test project") {
		t.Error("Returned unexpected body")
	}

}
func TestUpdateProjects(t *testing.T) {

	url := targetHost + "/api/v2/projects/"

	type projectData struct {
		ID           string `json:"id"`
		CustomersId  string `json:"customers_id"`
		Name         string `json:"name"`
		BusinessUuid string `json:"uuid_business_id"`
	}
	data := projectData{
		ID:           projectId,
		CustomersId:  "fe416e2d-0afb-5c82-a077-66da33137e26",
		Name:         "new project",
		BusinessUuid: businessId,
	}
	//t.Log(data)
	jsonData, err := json.Marshal(data)
	if err != nil {
		t.Error("Error marshaling data:", err)
	}
	//t.Log(jsonData)
	client := &http.Client{}

	req, err := http.NewRequest("PUT", url+projectId, bytes.NewBuffer(jsonData))
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
	if !strings.Contains(string(body), "new project") {
		t.Error("Returned unexpected body")
	} else {
		fmt.Println("Updated")
	}

}
func TestDeleteProjects(t *testing.T) {
	url := targetHost + "/api/v2/projects/"
	client := &http.Client{}

	req, err := http.NewRequest("DELETE", url+projectId, nil)
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
func TestGetSingleProject(t *testing.T) {
	url := targetHost + "/api/v2/projects/"
	//t.Log(tokenValue)

	req, err := http.NewRequest("GET", url+projectId, nil)
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
