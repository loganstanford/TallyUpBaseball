import requests, json

url = "https://api.sportradar.com/mlb/trial/v7/en/league/2023/12/19/transactions.json?api_key=sIZFvBq3gg8lXA6Uso9Ak7wYDFUmdtV84h8Tw287"

headers = {"accept": "application/json"}

response = requests.get(url, headers=headers)
data = response.json()

# Define the filename where you want to save the data
filename = 'response_data230316.json'

# Write the JSON data to a file
with open(filename, 'w') as file:
    json.dump(data, file, indent=4)

print(f"Data saved to {filename}")
