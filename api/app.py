import os
import time
from flask import Flask, request, send_from_directory 

from config import *
import display_data.hotels as hotels
import display_data.events as events
import display_data.exchange as exchange


app = Flask(__name__)
# app = Flask(__name__, static_folder='public')


@app.route("/")
def hello_world():
    return "<p>Flask app </p>"


@app.route('/time')
def get_current_time():
    return {'time': time.time()}


@app.route('/hotels', methods=['GET'])
def get_all_hotels():
    # Assume that all params are strings
    location = request.args.get("location")
    adults_number = request.args.get("adults")
    children_number = request.args.get("children")
    checkin_date = request.args.get("checkIN")
    checkout_date = request.args.get("checkOUT")
    room_number = request.args.get("rooms")
    print("hi")
    if location:
        print('Hi')
        res = hotels.get_hotels_by_location(location, adults_number, children_number, checkin_date, checkout_date, room_number)
        print(res)
        return res





if __name__ == '__main__':
    # app.run(host="0.0.0.0", port=5000)
    querystring = {"checkin_date": "2023-10-27", "dest_type": "city", "units": "metric", "checkout_date": "2023-10-28",
                   "adults_number": "2", "order_by": "popularity", "dest_id": "-553173", "filter_by_currency": "AED",
                   "locale": "en-gb", "room_number": "1", "children_number": "2", "children_ages": "5,0",
                   "categories_filter_ids": "class::2,class::4,free_cancellation::1", "page_number": "0",
                   "include_adjacency": "true"}
    print(res)

    res = hotels.get_hotels_by_location("Chicago", querystring['adults_number'], querystring['children_number'], querystring['checkin_date'], querystring['checkout_date'],
                                        querystring['room_number'])
