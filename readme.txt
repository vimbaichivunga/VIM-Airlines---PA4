================================================================
VIM Airlines - COS216 Practical Assignment 4
Student: Vimbai Chivunga
Student Number: u25136608
================================================================

HOW TO USE THE WEBSITE
----------------------------------------------------------------
1. Open the website at:
   https://wheatley.cs.up.ac.za/u25136608/COS216/PA4/login.php

2. Register a new account or use the default login below.

3. Once logged in you can:
   - Book Flights: Select a plane, departure airport, arrival
     airport, date and number of passengers. Toggle "Return
     Flight" to book a return trip. Click "Book Flight".
   - Bookings: View all your booked flights and cancel any.
   - Planes: Browse all planes, search, sort and filter by
     seats. Click "View Details" to see full specs.
     Click "❤ Save" to add a plane to your favourites.
   - Favourites: View and manage your saved planes.
   - Logout: Clears your session and returns to login.

================================================================
DEFAULT LOGIN DETAILS
----------------------------------------------------------------
Email:    vimbai@test.com
Password: Test@1234

================================================================
LOCAL DOM STORAGE
----------------------------------------------------------------
This website uses localStorage (not cookies) to store the
API key after login. localStorage was chosen because:
- It persists across page refreshes without expiry
- It is not sent with every HTTP request (unlike cookies)
- It is simpler to manage for a single-origin web app
- It can be easily cleared on logout

================================================================
FUNCTIONALITY IMPLEMENTED
----------------------------------------------------------------
- Register (API + validation)
- Login / Logout (API key stored in localStorage)
- Get All Planes (search, sort, filter via API)
- Get All Airports (search via API)
- View Plane Details (view.php)
- Add / Remove / Get / Clear Favourites (API + DB)
- Book Flight (Haversine distance + flight time calculation)
- Return flight booking
- Seat capacity check before booking
- Get Bookings (all user bookings with details)
- Cancel Booking

================================================================
FUNCTIONALITY NOT IMPLEMENTED
----------------------------------------------------------------
- Payment processing
- Email confirmation

================================================================
DATABASE SCHEMA ADDITIONS (PA4)
----------------------------------------------------------------
favourites table:
  - id (PK, AUTO_INCREMENT)
  - user_id (FK -> Users.id)
  - plane_id (FK -> planes.id)
  - UNIQUE(user_id, plane_id)

flights table:
  - id (PK, AUTO_INCREMENT)
  - plane_id (FK -> planes.id)
  - departure_airport (VARCHAR 10)
  - arrival_airport (VARCHAR 10)
  - departure_date (DATE)
  - flight_time (DECIMAL - minutes)
  - distance (DECIMAL - km)

bookings table:
  - id (PK, AUTO_INCREMENT)
  - flight_id (FK -> flights.id)
  - user_id (FK -> Users.id)
  - passengers (INT)

