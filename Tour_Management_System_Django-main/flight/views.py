from django.shortcuts import render, HttpResponse, HttpResponseRedirect
from django.urls import reverse
from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth import authenticate, login, logout
from django.core.mail import EmailMessage
from django.template.loader import render_to_string
from django.conf import settings
import os

from datetime import datetime
import math
from .models import *
from tourmate.utils import render_to_pdf, createticket
from datetime import datetime
import psycopg2

#Fee and Surcharge variable
from .constant import FEE
from flight.utils import createWeekDays, addPlaces, addDomesticFlights, addInternationalFlights

try:
    if len(Week.objects.all()) == 0:
        createWeekDays()

    if len(Place.objects.all()) == 0:
        addPlaces()

    if len(Flight.objects.all()) == 0:
        print("Do you want to add flights in the Database? (y/n)")
        if input().lower() in ['y', 'yes']:
            addDomesticFlights()
            addInternationalFlights()
except:
    pass


# Create your views here.
# con = psycopg2.connect(
#     database="tourmate",
#     user="postgres",
#     password="123",
#     host="localhost",
#     port= '5432'
#     )
# print("Tourmate Connected")
# cur = con.cursor()

def index(request):
    return render(request, "flight/index.html")

def login_page(request):
    return render(request, 'flight/login.html')

def signup(request):
    return render(request, "flight/sign_up_first.html")

def checkout(request):
    return render(request, 'flight/checkout.html')

def contact(request):
    return render(request, "flight/contact.html")

# def login_usr(request):
#     return

def travel_selection(request):
    return render(request, "flight/trav.html")

# Create your views here.
def flight_booking(request):
    min_date = f"{datetime.now().date().year}-{datetime.now().date().month}-{datetime.now().date().day}"
    max_date = f"{datetime.now().date().year if (datetime.now().date().month+3)<=12 else datetime.now().date().year+1}-{(datetime.now().date().month + 3) if (datetime.now().date().month+3)<=12 else (datetime.now().date().month+3-12)}-{datetime.now().date().day}"
    if request.method == 'POST':
        origin = request.POST.get('Origin')
        destination = request.POST.get('Destination')
        depart_date = request.POST.get('DepartDate')
        seat = request.POST.get('SeatClass')
        trip_type = request.POST.get('TripType')
        if(trip_type == '1'):
            return render(request, 'flight/flight_booking.html', {
            'origin': origin,
            'destination': destination,
            'depart_date': depart_date,
            'seat': seat.lower(),
            'trip_type': trip_type
        })
        elif(trip_type == '2'):
            return_date = request.POST.get('ReturnDate')
            return render(request, 'flight/flight_booking.html', {
            'min_date': min_date,
            'max_date': max_date,
            'origin': origin,
            'destination': destination,
            'depart_date': depart_date,
            'seat': seat.lower(),
            'trip_type': trip_type,
            'return_date': return_date
        })
    else:
        # print("hola")
        return render(request, 'flight/flight_booking.html', {
            'min_date': min_date,
            'max_date': max_date
        })

# def flight_booking(request):
#     min_date = f"{datetime.now().date().year}-{datetime.now().date().month}-{datetime.now().date().day}"
#     max_date = f"{datetime.now().date().year if (datetime.now().date().month + 3) <= 12 else datetime.now().date().year + 1}-{(datetime.now().date().month + 3) if (datetime.now().date().month + 3) <= 12 else (datetime.now().date().month + 3 - 12)}-{datetime.now().date().day}"
#     if request.method == 'POST':
#         origin = request.POST.get('Origin')
#         destination = request.POST.get('Destination')
#         depart_date = request.POST.get('DepartDate')
#         seat = request.POST.get('SeatClass')
#         trip_type = request.POST.get('TripType')

#         return render(request, 'flight/flight_booking.html', {
#                 'origin': origin,
#                 'destination': destination,
#                 'depart_date': depart_date,
#                 'seat': seat.lower(),
#                 'trip_type': trip_type
#             })




def login_view(request):
    if request.method == "POST":
        username = request.POST["username"]
        password = request.POST["password"]
        # print(username, password)
        user = authenticate(request, username=username, password=password)
        # print(user)
        if user is not None:
            login(request, user)

            return render(request, "flight/index.html")

        else:
            return render(request, "flight/login.html", {
                "message": "Invalid username and/or password."
            })
    else:
        if request.user.is_authenticated:
            print(123)
            return render(request, "flight/index.html")
        else:
            return render(request, "flight/login.html")

def login_direct(request):
    # print(123)
    if request.method == "POST":
        # print(1234)
        username = request.POST["username"]
        password = request.POST["password"]

        # print(username, password)
        user = authenticate(request, username=username, password=password)
        print(user)
        # if user is not None:
        login(request, user)
        # print(user)
        return render(request, "flight/trav.html")
        # else:
        #     return render(request, "flight/login.html", {
        #         "message": "Invalid username and/or password."
        #     })

def register_view(request):
    if request.method == "POST":
        fname = request.POST['firstname']
        lname = request.POST['lastname']
        username = request.POST["username"]
        email = request.POST["email"]

        # Ensuring password matches confirmation
        password = request.POST["password"]
        confirmation = request.POST["confirmation"]
        if password != confirmation:
            return render(request, "flight/register.html", {
                "message": "Passwords must match."
            })

        # Attempt to create new user
        try:
            user = User.objects.create_user(username, email, password)
            user.first_name = fname
            user.last_name = lname
            user.save()
        except:
            return render(request, "flight/register.html", {
                "message": "Username already taken."
            })
        login(request, user)
        return HttpResponseRedirect(reverse("index"))
    else:
        return render(request, "flight/register.html")

def logout_view(request):
    logout(request)
    return HttpResponseRedirect(reverse("index"))

def query(request, q):
    places = Place.objects.all()
    filters = []
    q = q.lower()
    for place in places:
        if (q in place.city.lower()) or (q in place.airport.lower()) or (q in place.code.lower()) or (q in place.country.lower()):
            filters.append(place)
    return JsonResponse([{'code':place.code, 'city':place.city, 'country': place.country} for place in filters], safe=False)

@csrf_exempt
def flight(request):
    o_place = request.GET.get('Origin')
    d_place = request.GET.get('Destination')
    trip_type = request.GET.get('TripType')
    departdate = request.GET.get('DepartDate')
    depart_date = datetime.strptime(departdate, "%Y-%m-%d")
    return_date = None
    if trip_type == '2':
        returndate = request.GET.get('ReturnDate')
        return_date = datetime.strptime(returndate, "%Y-%m-%d")
        flightday2 = Week.objects.get(number=return_date.weekday()) ##
        origin2 = Place.objects.get(code=d_place.upper())   ##
        destination2 = Place.objects.get(code=o_place.upper())  ##
    seat = request.GET.get('SeatClass')

    flightday = Week.objects.get(number=depart_date.weekday())
    destination = Place.objects.get(code=d_place.upper())
    origin = Place.objects.get(code=o_place.upper())
    if seat == 'economy':
        flights = Flight.objects.filter(depart_day=flightday,origin=origin,destination=destination).exclude(economy_fare=0).order_by('economy_fare')
        for f in flights:
            f.economy_fare = round(f.economy_fare*0.012)
            # print(f.economy_fare)
        try:
            max_price = flights.last().economy_fare*0.012
            min_price = flights.first().economy_fare
        except:
            max_price = 0
            min_price = 0

        if trip_type == '2':    ##
            flights2 = Flight.objects.filter(depart_day=flightday2,origin=origin2,destination=destination2).exclude(economy_fare=0).order_by('economy_fare')    ##
            for f in flights2:
                f.economy_fare = round(f.economy_fare * 0.012)
            try:
                max_price2 = flights2.last().economy_fare*0.012   ##
                min_price2 = flights2.first().economy_fare  ##
            except:
                max_price2 = 0  ##
                min_price2 = 0  ##


    elif seat == 'business':
        flights = Flight.objects.filter(depart_day=flightday,origin=origin,destination=destination).exclude(business_fare=0).order_by('business_fare')
        for f in flights:
            f.business_fare = round(f.business_fare*0.012)
        try:
            max_price = flights.last().business_fare*0.012
            min_price = flights.first().business_fare
        except:
            max_price = 0
            min_price = 0

        if trip_type == '2':    ##
            flights2 = Flight.objects.filter(depart_day=flightday2,origin=origin2,destination=destination2).exclude(business_fare=0).order_by('business_fare')    ##
            for f in flights2:
                f.business_fare = round(f.business_fare * 0.012)
            try:
                max_price2 = flights2.last().business_fare*0.012   ##
                min_price2 = flights2.first().business_fare  ##
            except:
                max_price2 = 0  ##
                min_price2 = 0  ##

    elif seat == 'first':
        flights = Flight.objects.filter(depart_day=flightday,origin=origin,destination=destination).exclude(first_fare=0).order_by('first_fare')
        for f in flights:
            f.first_fare = round(f.first_fare*0.012)
        try:
            max_price = flights.last().first_fare*0.012
            min_price = flights.first().first_fare
        except:
            max_price = 0
            min_price = 0

        if trip_type == '2':    ##
            flights2 = Flight.objects.filter(depart_day=flightday2,origin=origin2,destination=destination2).exclude(first_fare=0).order_by('first_fare')
            for f in flights2:
                f.first_fare = round(f.first_fare * 0.012)
            try:
                max_price2 = flights2.last().first_fare*0.012   ##
                min_price2 = flights2.first().first_fare  ##
            except:
                max_price2 = 0  ##
                min_price2 = 0  ##    ##

    #print(calendar.day_name[depart_date.weekday()])
    if trip_type == '2':
        return render(request, "flight/search.html", {
            'flights': flights,
            'origin': origin,
            'destination': destination,
            'flights2': flights2,   ##
            'origin2': origin2,    ##
            'destination2': destination2,    ##
            'seat': seat.capitalize(),
            'trip_type': trip_type,
            'depart_date': depart_date,
            'return_date': return_date,
            'max_price': math.ceil(max_price/100)*100,
            'min_price': math.floor(min_price/100)*100,
            'max_price2': math.ceil(max_price2/100)*100,    ##
            'min_price2': math.floor(min_price2/100)*100    ##
        })
    else:
        return render(request, "flight/search.html", {
            'flights': flights,
            'origin': origin,
            'destination': destination,
            'seat': seat.capitalize(),
            'trip_type': trip_type,
            'depart_date': depart_date,
            'return_date': return_date,
            'max_price': math.ceil(max_price/100)*100,
            'min_price': math.floor(min_price/100)*100
        })

def review(request):
    flight_1 = request.GET.get('flight1Id')
    date1 = request.GET.get('flight1Date')
    seat = request.GET.get('seatClass')
    round_trip = False
    if request.GET.get('flight2Id'):
        round_trip = True

    if round_trip:
        flight_2 = request.GET.get('flight2Id')
        date2 = request.GET.get('flight2Date')

    if request.user.is_authenticated:
        flight1 = Flight.objects.get(id=flight_1)
        flight1.economy_fare = round(flight1.economy_fare * 0.012)
        flight1.business_fare = round(flight1.business_fare * 0.012)
        flight1.first_fare = round(flight1.first_fare * 0.012)

        flight1ddate = datetime(int(date1.split('-')[2]),int(date1.split('-')[1]),int(date1.split('-')[0]),flight1.depart_time.hour,flight1.depart_time.minute)
        flight1adate = (flight1ddate + flight1.duration)
        flight2 = None
        flight2ddate = None
        flight2adate = None
        if round_trip:
            flight2 = Flight.objects.get(id=flight_2)
            flight2.economy_fare = round(flight2.economy_fare*0.012)
            flight2.business_fare = round(flight2.business_fare * 0.012)
            flight2.first_fare = round(flight2.first_fare * 0.012)
            flight2ddate = datetime(int(date2.split('-')[2]),int(date2.split('-')[1]),int(date2.split('-')[0]),flight2.depart_time.hour,flight2.depart_time.minute)
            flight2adate = (flight2ddate + flight2.duration)
        #print("//////////////////////////////////")
        #print(f"flight1ddate: {flight1adate-flight1ddate}")
        #print("//////////////////////////////////")
        if round_trip:
            return render(request, "flight/book.html", {
                'flight1': flight1,
                'flight2': flight2,
                "flight1ddate": flight1ddate,
                "flight1adate": flight1adate,
                "flight2ddate": flight2ddate,
                "flight2adate": flight2adate,
                "seat": seat,
                "fee": FEE
            })
        return render(request, "flight/book.html", {
            'flight1': flight1,
            "flight1ddate": flight1ddate,
            "flight1adate": flight1adate,
            "seat": seat,
            "fee": FEE
        })
    else:
        return HttpResponseRedirect(reverse("login"))

def book(request):
    if request.method == 'POST':
        if request.user.is_authenticated:
            flight_1 = request.POST.get('flight1')
            flight_1date = request.POST.get('flight1Date')
            flight_1class = request.POST.get('flight1Class')
            f2 = False
            if request.POST.get('flight2'):
                flight_2 = request.POST.get('flight2')
                flight_2date = request.POST.get('flight2Date')
                flight_2class = request.POST.get('flight2Class')
                f2 = True
            countrycode = request.POST['countryCode']
            mobile = request.POST['mobile']
            email = request.POST['email']
            flight1 = Flight.objects.get(id=flight_1)
            flight1.economy_fare = round(flight1.economy_fare * 0.012)
            flight1.business_fare = round(flight1.business_fare * 0.012)
            flight1.first_fare = round(flight1.first_fare * 0.012)
            if f2:
                flight2 = Flight.objects.get(id=flight_2)
                flight2.economy_fare = round(flight2.economy_fare * 0.012)
                flight2.business_fare = round(flight2.business_fare * 0.012)
                flight2.first_fare = round(flight2.first_fare * 0.012)
            passengerscount = request.POST['passengersCount']
            passengers=[]
            for i in range(1,int(passengerscount)+1):
                fname = request.POST[f'passenger{i}FName']
                lname = request.POST[f'passenger{i}LName']
                gender = request.POST[f'passenger{i}Gender']
                passengers.append(Passenger.objects.create(first_name=fname,last_name=lname,gender=gender.lower()))
            coupon = request.POST.get('coupon')

            try:
                ticket1 = createticket(request.user,passengers,passengerscount,flight1,flight_1date,flight_1class,coupon,countrycode,email,mobile)
                if f2:
                    ticket2 = createticket(request.user,passengers,passengerscount,flight2,flight_2date,flight_2class,coupon,countrycode,email,mobile)

                if(flight_1class == 'Economy'):
                    if f2:
                        fare = (flight1.economy_fare*int(passengerscount))+(flight2.economy_fare*int(passengerscount))
                    else:
                        fare = flight1.economy_fare*int(passengerscount)
                elif (flight_1class == 'Business'):
                    if f2:
                        fare = (flight1.business_fare*int(passengerscount))+(flight2.business_fare*int(passengerscount))
                    else:
                        fare = flight1.business_fare*int(passengerscount)
                elif (flight_1class == 'First'):
                    if f2:
                        fare = (flight1.first_fare*int(passengerscount))+(flight2.first_fare*int(passengerscount))
                    else:
                        fare = flight1.first_fare*int(passengerscount)
            except Exception as e:
                return HttpResponse(e)


            if f2:    ##
                return render(request, "flight/payment.html", { ##
                    'fare': fare+FEE,   ##
                    'ticket': ticket1.id,   ##
                    'ticket2': ticket2.id   ##
                })  ##
            return render(request, "flight/payment.html", {
                'fare': fare+FEE,
                'ticket': ticket1.id
            })
        else:
            return HttpResponseRedirect(reverse("login"))
    else:
        return HttpResponse("Method must be post.")

def payment(request):
    if request.user.is_authenticated:
        if request.method == 'POST':
            ticket_id = request.POST['ticket']
            if ticket_id=="123_accommodation":
                offer_id = request.POST['offer_id']
                guests = [{'id': 1, 'name': {'title': 'MR', 'firstName': 'BOB', 'lastName': 'SMITH'},
                           'contact': {'phone': '+33679278416', 'email': 'bob.smith@email.com'}}]

                payments = {'id': 1, 'method': 'creditCard',
                            'card': {'vendorCode': 'VI', 'cardNumber': '4151289722471370', 'expiryDate': '2023-08'}}
                # booking = amadeus.booking.hotel_bookings.post(offer_id, guests, payments).data
                # return render(request, 'demo/booking.html', {'id': booking[0]['id'],
                #                                              'providerConfirmationId': booking[0][
                #                                                  'providerConfirmationId']
                #                                              })
                return render(request, 'demo/booking.html', {'id': 'BOOKING123456',
                                                             'providerConfirmationId': "PROVIDER789012"
                                                             })
            t2 = False
            if request.POST.get('ticket2'):
                ticket2_id = request.POST['ticket2']
                t2 = True
            fare = request.POST.get('fare')
            card_number = "Paypal"
            # card_holder_name = request.POST['cardHolderName']
            # exp_month = request.POST['expMonth']
            # exp_year = request.POST['expYear']
            # cvv = request.POST['cvv']

            try:
                ticket = Ticket.objects.get(id=ticket_id)
                ticket.status = 'CONFIRMED'
                ticket.booking_date = datetime.now()
                ticket.save()
                if t2:
                    ticket2 = Ticket.objects.get(id=ticket2_id)
                    ticket2.status = 'CONFIRMED'
                    ticket2.save()
                    return render(request, 'flight/payment_process.html', {
                        'ticket1': ticket,
                        'ticket2': ticket2
                    })
                return render(request, 'flight/payment_process.html', {
                    'ticket1': ticket,
                    'ticket2': ""
                })
            except Exception as e:
                return HttpResponse(e)
        else:
            return HttpResponse("Method must be post.")
    else:
        return HttpResponseRedirect(reverse('login'))


def ticket_data(request, ref):
    ticket = Ticket.objects.get(ref_no=ref)
    return JsonResponse({
        'ref': ticket.ref_no,
        'from': ticket.flight.origin.code,
        'to': ticket.flight.destination.code,
        'flight_date': ticket.flight_ddate,
        'status': ticket.status
    })

# @csrf_exempt
# def get_ticket(request):
#     ref = request.GET.get("ref")
#     ticket1 = Ticket.objects.get(ref_no=ref)
#     data = {
#         'ticket1':ticket1,
#         'current_year': datetime.now().year
#     }
#     pdf = render_to_pdf('flight/ticket.html', data)
#     return HttpResponse(pdf, content_type='application/pdf')
@csrf_exempt
def get_ticket(request):
    ref = request.GET.get("ref")
    ticket1 = Ticket.objects.get(ref_no=ref)
    data = {
        'ticket1': ticket1,
        'current_year': datetime.now().year
    }
    pdf = render_to_pdf('flight/ticket.html', data)

    # Email Sending Logic
    email_subject = 'Your Flight Ticket'
    email_body = 'Please find your flight ticket attached.'
    from_email = settings.EMAIL_HOST_USER
    to_email = ticket1.user.email

    # Convert PDF content to bytes
    pdf_bytes = pdf.content

    # Create an EmailMessage instance and attach the PDF
    msg = EmailMessage(email_subject, email_body, from_email, [to_email])
    msg.attach(f'ticket_{ref}.pdf', pdf_bytes, 'application/pdf')
    msg.send()
    return HttpResponse("Email sent with the ticket PDF attached.")

def bookings(request):
    if request.user.is_authenticated:
        tickets = Ticket.objects.filter(user=request.user).order_by('-booking_date')
        return render(request, 'flight/bookings.html', {
            'page': 'bookings',
            'tickets': tickets
        })
    else:
        return HttpResponseRedirect(reverse('login'))

@csrf_exempt
def cancel_ticket(request):
    if request.method == 'POST':
        if request.user.is_authenticated:
            ref = request.POST['ref']
            try:
                ticket = Ticket.objects.get(ref_no=ref)
                if ticket.user == request.user:
                    ticket.status = 'CANCELLED'
                    ticket.save()
                    return JsonResponse({'success': True})
                else:
                    return JsonResponse({
                        'success': False,
                        'error': "User unauthorised"
                    })
            except Exception as e:
                return JsonResponse({
                    'success': False,
                    'error': e
                })
        else:
            return HttpResponse("User unauthorised")
    else:
        return HttpResponse("Method must be POST.")

def resume_booking(request):
    if request.method == 'POST':
        if request.user.is_authenticated:
            ref = request.POST['ref']
            ticket = Ticket.objects.get(ref_no=ref)
            if ticket.user == request.user:
                return render(request, "flight/payment.html", {
                    'fare': ticket.total_fare,
                    'ticket': ticket.id
                })
            else:
                return HttpResponse("User unauthorised")
        else:
            return HttpResponseRedirect(reverse("login"))
    else:
        return HttpResponse("Method must be post.")

# def contact(request):
#     return render(request, 'flight/contact.html')

def privacy_policy(request):
    return render(request, 'flight/privacy-policy.html')

def terms_and_conditions(request):
    return render(request, 'flight/terms.html')

def about_us(request):
    return render(request, 'flight/about.html')



import json
from amadeus import Client, ResponseError, Location
from django.shortcuts import render
from django.contrib import messages
from .hotel import Hotel
from .room import Room
from django.http import HttpResponse

amadeus = Client( client_id='b0QKnR4h9f5Hm05ZuCaWFjkixfGAztRN',
    client_secret='PSsHXO1GQnS50AZo')
def demo(request):
    origin = request.POST.get('Origin')
    checkinDate = request.POST.get('Checkindate')
    checkoutDate = request.POST.get('Checkoutdate')

    kwargs = {'cityCode': request.POST.get('Origin'),
              'checkInDate': request.POST.get('Checkindate'),
              'checkOutDate': request.POST.get('Checkoutdate')}

    if origin and checkinDate and checkoutDate:
        try:
            # Hotel List
            hotel_list = amadeus.reference_data.locations.hotels.by_city.get(cityCode=origin)
        except ResponseError as error:
            messages.add_message(request, messages.ERROR, error.response.body)
            return render(request, 'demo/demo_form.html', {})
        hotel_offers = []
        hotel_ids = []
        for i in hotel_list.data:
            hotel_ids.append(i['hotelId'])
        num_hotels = 40
        kwargs = {'hotelIds': hotel_ids[0:num_hotels],
            'checkInDate': request.POST.get('Checkindate'),
            'checkOutDate': request.POST.get('Checkoutdate')}
        try:
            # Hotel Search
            search_hotels = amadeus.shopping.hotel_offers_search.get(**kwargs)
        except ResponseError as error:
            messages.add_message(request, messages.ERROR, error.response.body)
            return render(request, 'demo/demo_form.html', {})
        try:
            for hotel in search_hotels.data:
                offer = Hotel(hotel).construct_hotel()
                hotel_offers.append(offer)
                response = zip(hotel_offers, search_hotels.data)
            # print(response)
            return render(request, 'demo/results.html', {'response': response,
                                                         'origin': origin,
                                                         'departureDate': checkinDate,
                                                         'returnDate': checkoutDate,
                                                         })
        except UnboundLocalError:
            messages.add_message(request, messages.ERROR, 'No hotels found.')
            return render(request, 'demo/demo_form.html', {})
    return render(request, 'demo/demo_form.html', {})


def rooms_per_hotel(request, hotel, departureDate, returnDate):
    if request.user.is_authenticated:
        try:
            # Search for rooms in a given hotel
            rooms = amadeus.shopping.hotel_offers_search.get(hotelIds=hotel,
                                                               checkInDate=departureDate,
                                                               checkOutDate=returnDate).data
            hotel_rooms = Room(rooms).construct_room()
            print(hotel_rooms)
            desc = hotel_rooms[0]['description'].split(",")
            print(desc)
            return render(request, 'demo/rooms_per_hotel.html', {'response': hotel_rooms,
                                                                 'name': rooms[0]['hotel']['name'],
                                                                 "checkInDate": departureDate,
                                                                 "checkOutDate": returnDate
                                                                 })
        except (TypeError, AttributeError, ResponseError, KeyError) as error:
            messages.add_message(request, messages.ERROR, error)
            return render(request, 'demo/rooms_per_hotel.html', {})

    else:
        return HttpResponseRedirect(reverse("login"))


# def booking_accommodation(request, checkoutdate, fee, offer_id):


def get_user_details(request, offer_id, room_price, hotel_name, checkInDate, checkOutDate):
    # print(offer_availability)
    # if offer_availability.status_code == 200:
        # booking = amadeus.booking.hotel_bookings.post(offer_id, guests, payments).data
    # print(hotel_discount)
    fee = round(float(room_price)*0.1,2)
    total_price = round(float(room_price)+fee,2)
    if checkOutDate=="book_acd":
        if request.method == 'POST':
            if request.user.is_authenticated:
                hotel_name = request.POST.get('hotel_name')
                checkindate = request.POST.get('indate')
                checkoutdate = checkOutDate

                countrycode = request.POST['countryCode']
                mobile = request.POST['mobile']
                email = request.POST['email']

                price = request.POST.get('price')

                passengerscount = request.POST['passengersCount']
                passengers = []
                for i in range(1, int(passengerscount) + 1):
                    fname = request.POST[f'passenger{i}FName']
                    lname = request.POST[f'passenger{i}LName']
                    gender = request.POST[f'passenger{i}Gender']
                    passengers.append(
                        Passenger.objects.create(first_name=fname, last_name=lname, gender=gender.lower()))
                coupon = request.POST.get('coupon')

                if int(passengerscount)>1:
                    fare = round((float(price) * int(passengerscount) + (float(fee)*int(passengerscount))) / 2, 2)
                else:
                    fare = float(price) * int(passengerscount) + (float(fee)*int(passengerscount))
                ticket = "123_accommodation"
                return render(request, "demo/payment_acd.html", {
                    'fare': round(fare),
                    'ticket': ticket,
                    "offer_id": offer_id
                })
            else:
                return HttpResponseRedirect(reverse("login"))
        else:
            return HttpResponse("Method must be post.")
    else:
        return render(request, "demo/get_details_accommodation.html", {
            'price':room_price,
            'hotel_name': hotel_name,
            'fee':fee,
            'total_price': total_price,
            'indate': checkInDate,
            'outdate': checkOutDate
        })

def book_hotel(request, offer_id, room_price, hotel_name):
    if request.user.is_authenticated:
        try:
            # Confirm availability of a given offer
            # print(room_price, hotel_name, offer_id)
            offer_availability = amadeus.shopping.hotel_offer_search(offer_id).get()
            if offer_availability.status_code == 200:
                # print(request.GET.get('hotel_desc'))
                guests = [{'id': 1, 'name': {'title': 'MR', 'firstName': 'BOB', 'lastName': 'SMITH'},
                           'contact': {'phone': '+33679278416', 'email': 'bob.smith@email.com'}}]

                payments = {'id': 1, 'method': 'creditCard',
                            'card': {'vendorCode': 'VI', 'cardNumber': '4151289722471370', 'expiryDate': '2023-08'}}
                booking = amadeus.booking.hotel_bookings.post(offer_id, guests, payments).data
                # print(booking)
            else:
                return render(request, 'demo/booking.html', {'response': 'The room is not available'})
        except ResponseError as error:
            messages.add_message(request, messages.ERROR, error.response.body)
            return render(request, 'demo/booking.html', {})
        return render(request, 'demo/booking.html', {'id': booking[0]['id'],
                                                     'providerConfirmationId': booking[0]['providerConfirmationId']
                                                     })
    else:
        return HttpResponseRedirect(reverse('login'))

# Main Code of city_search in older version of Django, which is the below one
# def city_search(request):
#     if request.is_ajax():
#         try:
#             data = amadeus.reference_data.locations.get(keyword=request.GET.get('term', None),
#                                                         subType=Location.ANY).data
#         except ResponseError as error:
#             messages.add_message(request, messages.ERROR, error.response.body)
#     return HttpResponse(get_city_list(data), 'application/json')


# Below is the code for newer version of Django, only change is that is_ajax methos is replicated by accepts method
# References: https://stackoverflow.com/questions/70419441/attributeerror-wsgirequest-object-has-no-attribute-is-ajax,
# References: https://stackoverflow.com/questions/73782139/why-do-i-get-this-error-httprequest-accepts-missing-1-required-positional-arg
def city_search(request):
    if request.accepts('text/html'):
        try:
            data = amadeus.reference_data.locations.get(keyword=request.GET.get('term', None),
                                                        subType=Location.ANY).data
            print(data)
        except Exception as error:
            messages.add_message(request, messages.ERROR, error.response.body)
    return HttpResponse(get_city_list(data), 'application/json')


def get_city_list(data):
    result = []
    for i, val in enumerate(data):
        result.append(data[i]['iataCode'] + ', ' + data[i]['name'])
    result = list(dict.fromkeys(result))
    return json.dumps(result)


#################
import os
import openai

openai.api_key = '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'

def home_itinerary(request):
    return render(request, "itinerary/home_iti.html")


def process_response(response):
    """Processes the response from OpenAI.

    Splits the response into a list of lists where each inner list contains the day number and the itinerary for that day.

    Args:
        response: The response from OpenAI.

    Returns:
        The processed response as a list of lists.
    """
    response = response.replace('\n', '').split('Day')[1:]
    response = list([[item.split('.')[0], '.'.join(item.split('.')[1:])] for item in response])
    return response


@csrf_exempt
def submit(request):
    # if request.method == 'POST':
    #     place = request.POST.get("Origin")
    #     review_text = request.POST.get("review")
    #     rating_value = int(request.POST.get("rating"))
    #
    #     # Create and save the review instance
    #     review_instance = Review(place=place, review=review_text, rating=rating_value)
    #     review_instance.save()
    #
    #     # Retrieve all reviews from the database
    #     all_reviews = Review.objects.all()
    #
    #     # Pass the reviews to the template
    #     return render(request, 'itinerary/response_iti.html', {
    #         'response': all_reviews,
    #         'message': 'Review and rating submitted successfully!'
    #     })

    # Handle cases where the request method is not POST
    return render(request, 'itinerary/response_iti.html', {
        'error_message': 'Failed to submit review and rating. Please try again.'
    })