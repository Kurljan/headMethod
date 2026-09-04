import requests

# Simple HEAD request
url = 'https://web.facebook.com/?_rdc=1&_rdr#'
response = requests.head(url, allow_redirects=True)

print(f"Status:   {response.status_code}")
print(f"Size:     {response.headers.get('Content-Length')} bytes")
print(f"Type:     {response.headers.get('Content-Type')}")
print(f"ETag:     {response.headers.get('ETag')}")

# Body is always empty for HEAD
print(f"Body: '{response.text}'")  # ''

# Utility: check if resource exists
def resource_exists(url: str) -> bool:
    try:
        r = requests.head(url, timeout=5)
        return r.status_code == 200
    except requests.RequestException:
        return False