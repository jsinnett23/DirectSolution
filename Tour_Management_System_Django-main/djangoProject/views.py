from django.shortcuts import render, redirect
from django.http import HttpResponseRedirect
from datetime import datetime
import psycopg2


def index(request):
    return render(request, "index.html")

def login_page(request):
    return render(request, 'login.html')

def login(request):
    # cur.execute('select id,password from student')
    # dict = {}
    # for i, j in cur:
    #     dict[i] = j

    id = ["Yoru", "Raze", "Team20"]
    pasw = {"Yoru": "1", "Raze": "2", "Team20":"3"}
    # retrieve data
    user_id = request.POST.get('username')
    user_pass = request.POST.get('password')
    print(user_id, user_pass)
    # print(user_id, user_pass, dict[user_id], type(user_pass), type(dict[user_id]))
    if (user_id in id):
        if (pasw[user_id] == user_pass):
            print('Login Successfully')
            return render(request, 'first_page.html', {"student_info": user_id})
        else:
            return render(request, 'login.html', {"msg": "Wrong Password"})
    else:
        return render(request, 'login.html', {"msg": "Wrong ID"})


def checkout(request):
    return render(request, 'checkout.html')

def contact(request):
    return render(request, "contact.html")