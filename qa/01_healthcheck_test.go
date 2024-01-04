package main

import (
	"encoding/json"
	"net/http"
	"testing"
)

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
	// Verify the response
	expectedResp := "pong"
	if data["response"] != expectedResp {
		t.Errorf("Returned unexpected response")
	} else {
		t.Log("Received response pong")
	}
	// Verify the status code
	if res.StatusCode != http.StatusOK {
		t.Errorf("Returned wrong status code: got %v want %v", res.StatusCode, http.StatusOK)
	}
}
