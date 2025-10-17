import requests
import sys

API_URL = sys.argv[1] if len(sys.argv) > 1 else "http://localhost:8000/api/v2/token"
EMAIL = sys.argv[2] if len(sys.argv) > 2 else "user@example.com"
PASSWORD = sys.argv[3] if len(sys.argv) > 3 else "yourpassword"
TYPE = sys.argv[4] if len(sys.argv) > 4 else "contacts"

payload = {
    "email": EMAIL,
    "password": PASSWORD,
    "type": TYPE
}

resp = requests.post(API_URL, json=payload)

try:
    resp.raise_for_status()
    data = resp.json()
    print("Access token:", data.get("access_token", data))
except Exception as e:
    print("Error:", resp.status_code, resp.text)
    sys.exit(1)
