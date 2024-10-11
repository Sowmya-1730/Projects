import requests
from bs4 import BeautifulSoup

def find_form_parameters(url):
    # Send a GET request to the URL
    response = requests.get(url)
    
    # Check if the request was successful
    if response.status_code != 200:
        print(f"Failed to retrieve the page. Status code: {response.status_code}")
        return
    
    # Parse the HTML content using BeautifulSoup
    soup = BeautifulSoup(response.text, 'html.parser')
    
    # Find all forms on the page
    forms = soup.find_all('form')
    
    if not forms:
        print("No forms found on the page.")
        return
    
    # Iterate through each form and extract the input names
    for i, form in enumerate(forms):
        print(f"Form {i+1}:")
        
        # Find all input fields in the form
        inputs = form.find_all(['input', 'select', 'textarea'])
        
        if not inputs:
            print("  No input fields found.")
            continue
        
        # Extract the name attribute from each input field
        for input_field in inputs:
            input_name = input_field.get('name')
            input_type = input_field.get('type', 'textarea' if input_field.name == 'textarea' else 'select' if input_field.name == 'select' else 'input')
            if input_name:
                print(f"  {input_type} field with name: {input_name}")
            else:
                print(f"  {input_type} field without a name attribute")
                
        print()  # Blank line for readability between forms

# Example usage
url = "https://habittracer.s3.ap-south-1.amazonaws.com/habit/index.html "
find_form_parameters(url)
