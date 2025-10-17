package main

import (
    "bytes"
    "context"
    "encoding/json"
    "fmt"
    "io"
    "log"
    "net/http"
    "os"
    "strings"
    "time"

    "github.com/google/uuid"
)

type timesheet struct {
    UUID            string  `json:"uuid"`
    BusinessID      string  `json:"uuid_business_id"`
    EmployeeID      string  `json:"employee_id"`
    DurationMinutes *int    `json:"duration_minutes"`
    BillableHours   *string `json:"billable_hours"`
    TotalAmount     *string `json:"total_amount"`
    Status          string  `json:"status"`
    IsRunning       bool    `json:"is_running"`
    UpdatedAt       string  `json:"updated_at"`
}

type timesheetListResponse struct {
    Data  []timesheet `json:"data"`
    Meta  pagination   `json:"meta"`
    Links any          `json:"links"`
}

type pagination struct {
    NextCursor *string `json:"next_cursor"`
    PrevCursor *string `json:"prev_cursor"`
    PageSize   int     `json:"page_size"`
}

func main() {
    ctx := context.Background()

    baseURL := valueOrDefault(os.Getenv("MODERN_API_BASE_URL"), "http://localhost:8000/api/v1")
    bearer := os.Getenv("MODERN_API_BEARER_TOKEN")
    businessID := os.Getenv("MODERN_API_BUSINESS_ID")

    if businessID == "" {
        log.Fatal("MODERN_API_BUSINESS_ID environment variable is required")
    }

    client := &http.Client{Timeout: 10 * time.Second}

    log.Printf("Using API base %s", baseURL)

    created, etag, err := createTimesheet(ctx, client, baseURL, bearer, businessID)
    if err != nil {
        log.Fatalf("createTimesheet failed: %v", err)
    }
    log.Printf("Created timesheet %s (status=%s, duration=%v minutes)", created.UUID, created.Status, created.DurationMinutes)

    _, newEtag, err := updateTimesheet(ctx, client, baseURL, bearer, created.UUID, etag)
    if err != nil {
        log.Fatalf("updateTimesheet failed: %v", err)
    }
    log.Printf("Updated timesheet description. New ETag=%s", newEtag)

    list, err := listTimesheets(ctx, client, baseURL, bearer)
    if err != nil {
        log.Fatalf("listTimesheets failed: %v", err)
    }
    log.Printf("Timesheet count=%d, page_size=%d, next_cursor=%v", len(list.Data), list.Meta.PageSize, list.Meta.NextCursor)

    running, runningEtag, err := startTimer(ctx, client, baseURL, bearer, businessID)
    if err != nil {
        log.Fatalf("startTimer failed: %v", err)
    }
    log.Printf("Started timer %s", running.UUID)

    stopped, _, err := stopTimer(ctx, client, baseURL, bearer, running.UUID, runningEtag)
    if err != nil {
        log.Fatalf("stopTimer failed: %v", err)
    }
    log.Printf("Stopped timer %s (status=%s, is_running=%t)", stopped.UUID, stopped.Status, stopped.IsRunning)
}

func createTimesheet(ctx context.Context, client *http.Client, baseURL, bearer, businessID string) (timesheet, string, error) {
    start := time.Now().UTC().Truncate(time.Second)
    end := start.Add(1 * time.Hour)

    payload := map[string]any{
        "uuid_business_id": businessID,
        "employee_id":      uuid.NewString(),
        "start_time":       start.Format(time.RFC3339),
        "end_time":         end.Format(time.RFC3339),
        "hourly_rate":      "120.00",
        "is_billable":      true,
        "status":           "draft",
        "description":      "Consulting session (Go client)",
    }

    respBody, headers, err := doRequest(ctx, client, http.MethodPost, fmt.Sprintf("%s/timesheets", baseURL), bearer, payload, nil)
    if err != nil {
        return timesheet{}, "", err
    }
    defer respBody.Close()

    if headers.StatusCode != http.StatusCreated {
        return timesheet{}, "", fmt.Errorf("unexpected status %d", headers.StatusCode)
    }

    var result timesheet
    if err := json.NewDecoder(respBody).Decode(&result); err != nil {
        return timesheet{}, "", err
    }
    return result, headers.Header.Get("ETag"), nil
}

func updateTimesheet(ctx context.Context, client *http.Client, baseURL, bearer, timesheetID, etag string) (timesheet, string, error) {
    payload := map[string]any{
        "description": "Updated via Go client",
    }

    headers := map[string]string{
        "If-Match": etag,
    }

    body, meta, err := doRequest(ctx, client, http.MethodPatch, fmt.Sprintf("%s/timesheets/%s", baseURL, timesheetID), bearer, payload, headers)
    if err != nil {
        return timesheet{}, "", err
    }
    defer body.Close()

    if meta.StatusCode != http.StatusOK {
        return timesheet{}, "", fmt.Errorf("unexpected status %d", meta.StatusCode)
    }

    var result timesheet
    if err := json.NewDecoder(body).Decode(&result); err != nil {
        return timesheet{}, "", err
    }
    return result, meta.Header.Get("ETag"), nil
}

func listTimesheets(ctx context.Context, client *http.Client, baseURL, bearer string) (timesheetListResponse, error) {
    body, meta, err := doRequest(ctx, client, http.MethodGet, fmt.Sprintf("%s/timesheets", baseURL), bearer, nil, nil)
    if err != nil {
        return timesheetListResponse{}, err
    }
    defer body.Close()

    if meta.StatusCode != http.StatusOK {
        return timesheetListResponse{}, fmt.Errorf("unexpected status %d", meta.StatusCode)
    }

    var result timesheetListResponse
    if err := json.NewDecoder(body).Decode(&result); err != nil {
        return timesheetListResponse{}, err
    }
    return result, nil
}

func startTimer(ctx context.Context, client *http.Client, baseURL, bearer, businessID string) (timesheet, string, error) {
    payload := map[string]any{
        "uuid_business_id": businessID,
        "employee_id":      uuid.NewString(),
        "description":      "Timer started from Go client",
        "hourly_rate":       "95.00",
        "status":            "running",
    }

    body, meta, err := doRequest(ctx, client, http.MethodPost, fmt.Sprintf("%s/timesheets/start", baseURL), bearer, payload, nil)
    if err != nil {
        return timesheet{}, "", err
    }
    defer body.Close()

    if meta.StatusCode != http.StatusCreated {
        return timesheet{}, "", fmt.Errorf("unexpected status %d", meta.StatusCode)
    }

    var result timesheet
    if err := json.NewDecoder(body).Decode(&result); err != nil {
        return timesheet{}, "", err
    }
    return result, meta.Header.Get("ETag"), nil
}

func stopTimer(ctx context.Context, client *http.Client, baseURL, bearer, timesheetID, etag string) (timesheet, string, error) {
    headers := map[string]string{}
    if etag != "" {
        headers["If-Match"] = etag
    }

    body, meta, err := doRequest(ctx, client, http.MethodPost, fmt.Sprintf("%s/timesheets/%s/stop", baseURL, timesheetID), bearer, nil, headers)
    if err != nil {
        return timesheet{}, "", err
    }
    defer body.Close()

    if meta.StatusCode != http.StatusOK {
        return timesheet{}, "", fmt.Errorf("unexpected status %d", meta.StatusCode)
    }

    var result timesheet
    if err := json.NewDecoder(body).Decode(&result); err != nil {
        return timesheet{}, "", err
    }
    return result, meta.Header.Get("ETag"), nil
}

type responseMeta struct {
    StatusCode int
    Header     http.Header
}

func doRequest(ctx context.Context, client *http.Client, method, url, bearer string, payload any, extraHeaders map[string]string) (io.ReadCloser, responseMeta, error) {
    var body io.Reader
    if payload != nil {
        buf, err := json.Marshal(payload)
        if err != nil {
            return nil, responseMeta{}, err
        }
        body = bytes.NewReader(buf)
    }

    req, err := http.NewRequestWithContext(ctx, method, url, body)
    if err != nil {
        return nil, responseMeta{}, err
    }

    if payload != nil {
        req.Header.Set("Content-Type", "application/json")
    }
    if bearer != "" {
        req.Header.Set("Authorization", "Bearer "+bearer)
    }
    for k, v := range extraHeaders {
        req.Header.Set(k, v)
    }

    resp, err := client.Do(req)
    if err != nil {
        return nil, responseMeta{}, err
    }

    if resp.StatusCode >= 400 {
        defer resp.Body.Close()
        data, _ := io.ReadAll(resp.Body)
        snippet := strings.TrimSpace(string(data))
        if snippet == "" {
            snippet = resp.Status
        }
        return nil, responseMeta{}, fmt.Errorf("status %d: %s", resp.StatusCode, snippet)
    }

    return resp.Body, responseMeta{StatusCode: resp.StatusCode, Header: resp.Header}, nil
}

func valueOrDefault(val, fallback string) string {
    if val == "" {
        return fallback
    }
    return val
}
