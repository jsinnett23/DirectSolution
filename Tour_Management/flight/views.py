from django.shortcuts import render, HttpResponse, HttpResponseRedirect
from django.urls import reverse
from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth import authenticate, login, logout

from datetime import datetime
import math
from .models import *
from tourmate.utils import render_to_pdf, createticket
from datetime import datetime
import psycopg2




def index(request):
    return render(request, "flight/index.html")

def login_page(request):
    return render(request, 'flight/login.html')

def checkout(request):
    return render(request, 'flight/checkout.html')

def contact(request):
    return render(request, "flight/contact.html")

# def login_usr(request):
#     return

def travel_selection(request):
    return render(request, "flight/trav.html")



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

