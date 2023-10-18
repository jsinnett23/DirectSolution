import http.client
import json
import os

from requests.api import head
from dotenv import load_dotenv
import requests
load_dotenv()
API_HOST = os.getenv("RAPID_API_HOST")
API_KEY = os.getenv("RAPID_API_KEY")

# API_HOST = 'booking-com.p.rapidapi.com'
# API_KEY = 'f37bccd9edmsh6cf3d78c052f92bp1d5a9djsn6344f3dcb412'

# We are using the booking.com API for hotel data
conn = http.client.HTTPSConnection("booking-com.p.rapidapi.com")

headers = {
    'x-rapidapi-host': API_HOST,
    'x-rapidapi-key': API_KEY
}


def get_hotels_by_location(location, adults_number, children_number, checkin_date, checkout_date, room_number):

    params = {}

    if location:
        params['name'] = location
    
    if adults_number:
        params['adults_number'] = adults_number
    else:
        params['adults_number'] = 1

    if children_number:
        params['children_number'] = children_number
    else:
        params['children_number'] = 1 # children must be at lease 1, the api does not like 0 with each call

    if checkin_date:
        params['checkin_date'] = checkin_date

    if checkout_date:
        params['checkout_date'] = checkout_date

    if room_number:
        params['room_number'] = room_number
    else:
        params['room_number'] = 1

    params['order_by'] = 'popularity'
    params['units'] = 'metric'
    params['page_number'] = 0
    params['filter_by_currency'] = 'USD'
    params['locale'] = 'en-us'

    params = tuple(params.items())
    # response = requests.get("http://booking-com.p.rapidapi.com/v1/hotels/locations/", params=params, headers=headers).json()

    conn.request("GET", "/v1/hotels/locations?name="+location+"&locale=en-us", headers=headers)

    res = conn.getresponse()
    data = res.read()

    # Turn into String
    dataStr = data.decode()

    # Turn this string into an object (in python, this is a dictionary aka json)
    x = json.loads(dataStr)
    return parse_data(x, adults_number, children_number, checkin_date, checkout_date, room_number)


def parse_data(dataObject, adults_number, children_number, checkin_date, checkout_date, room_number):
    # Get the destID and destType from the dictionary with US as the country
    destID = ""
    destType = ""

    destID = dataObject[0]["dest_id"]
    destType = dataObject[0]["dest_type"]



    # Make the new request
    conn.request("GET", "/v1/hotels/search?locale=en-gb&room_number=" + room_number + "&checkout_date="+ checkout_date +"&order_by=popularity&units=metric&adults_number=" + adults_number + "&filter_by_currency=AED&checkin_date=" + checkin_date + "&dest_type=" + destType + "&dest_id=" + destID + "&children_number=1&page_number=0&categories_filter_ids=facility%3A%3A107%2Cfree_cancellation%3A%3A1&children_ages=5%2C0", headers=headers)

    # # Hotel Data!
    res1 = conn.getresponse()
    data1 = res1.read()

    # Turn Into String
    dataStr1 = data1.decode()


    # Load the data and return it
    # list_of_hotels = json.loads(dataStr1)

    return dataStr1
