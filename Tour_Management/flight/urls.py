from django.urls import path
from . import views
from two_factor.urls import urlpatterns as tf_urls
from django.urls import include, path

urlpatterns = [
    path('', views.index, name='index'),
    path('index', views.index, name="index"),
    path('login_page', views.login_page, name='Login'),

    path('', include(tf_urls)),
    path("login", views.login_view, name="login"),
    path('login_usr', views.login_direct, name = "Login User"),

    path("logout", views.logout_view, name="logout"),
    path("register", views.register_view, name="register"),

]