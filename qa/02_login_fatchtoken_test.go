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
var businessId string

var email = os.Getenv("QA_LOGIN_EMAIL")
var password = os.Getenv("QA_LOGIN_PASSWORD")
var targetHost = os.Getenv("TARGET_HOST")

func TestLoginAuth(t *testing.T) {
	url := targetHost + "/auth/user_login"

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

		User struct {
			UUID_Business string `json:"uuid_business_id"`
		} `json:"user"`
	}

	url := targetHost + "/auth/user_login"

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
	//fmt.Println(resp.Body)
	defer resp.Body.Close()

	var decodeValue AuthResponse
	err = json.NewDecoder(resp.Body).Decode(&decodeValue)
	if err != nil {
		t.Log(err)
	}
	// Getting the access token and business uuid from auth API
	tokenValue = decodeValue.AccessToken
	businessId = decodeValue.User.UUID_Business

	//fmt.Println(businessId)
	//fmt.Println(tokenValue)
}
