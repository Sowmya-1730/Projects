import requests
from bs4 import BeautifulSoup

def test_csrf(url):
    r = requests.get(url,verify=False)
    soup = BeautifulSoup(r.text, 'html.parser')
    forms = soup.find_all('form')
    
    results = []
    for form in forms:
        if 'csrf' not in str(form):
            
            results.append(f"Potential CSRF vulnerability: Form without CSRF token")
    
    if len(results) == 0:
        return ["The website is not vulnerable to CSRF"]
    else:
        return results
