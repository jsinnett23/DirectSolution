from django.urls import path
from . import views
from two_factor.urls import urlpatterns as tf_urls
from django.urls import include, path

urlpatterns = [
    path('', views.index, name='index'),
    path('index', views.index, name="index"),
    path('login_page', views.login_page, name='Login'),
    # path('login', views.login, name="login"),
    path('signup/', views.signup, name='signup'),
    path('checkout', views.checkout, name="checkout"),

    path("flight_book", views.flight_booking, name="flight_booking"),

    path('contact', views.contact, name="Contact"),

    path('', include(tf_urls)),
    path("login", views.login_view, name="login"),
    path('login_usr', views.login_direct, name = "Login User"),

    path("logout", views.logout_view, name="logout"),
    path("register", views.register_view, name="register"),
    path("query/places/<str:q>", views.query, name="query"),
    path("flight", views.flight, name="flight"),
    path("review", views.review, name="review"),
    path("flight/ticket/book", views.book, name="book"),
    path("flight/ticket/payment", views.payment, name="payment"),
    path('flight/ticket/api/<str:ref>', views.ticket_data, name="ticketdata"),
    path('flight/ticket/print',views.get_ticket, name="getticket"),
    path('flight/bookings', views.bookings, name="bookings"),
    path('flight/ticket/cancel', views.cancel_ticket, name="cancelticket"),
    path('flight/ticket/resume', views.resume_booking, name="resumebooking"),
    path('contact', views.contact, name="contact"),
    path('privacy-policy', views.privacy_policy, name="privacypolicy"),
    path('terms-and-conditions', views.terms_and_conditions, name="termsandconditions"),
    path('about-us', views.about_us, name="aboutus"),

    path('demo/', views.demo, name='demo_form'),
    path('city_search/', views.city_search, name='city_search'),
    path('book_hotel/<str:offer_id>/<str:room_price>/<str:hotel_name>/<str:checkInDate>/<str:checkOutDate>', views.get_user_details, name='book_hotel'),
    path('rooms_per_hotel/<str:hotel>/<str:departureDate>/<str:returnDate>', views.rooms_per_hotel,name='rooms_per_hotel'),
    # path('book_acd', views.booking_accommodation, name="Book Accommodation")

    path(r'home_iti', views.home_itinerary, name="Itinerary"),
    path('process_response', views.process_response, name="Process Response Itinerary"),
    path('submit_iti', views.submit, name="Submit Itinerary")
]