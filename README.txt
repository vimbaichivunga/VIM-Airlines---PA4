COS216 PA3 - VIM Airlines
Student: Vimbai Chikwanda
Student Number: u25136608

=====================================
HOW TO USE THE WEBSITE
=====================================
1. Open the website at: https://wheatley.cs.up.ac.za/u25136608/COS216/PA3/index.php
2. Use the navbar to navigate between pages
3. To register, click "Register" in the navbar and fill in the form
4. After registering you will receive an API key , save it!
5. Use the Planes page to browse and search aircraft
6. Use the Book Flights page to search for flights

=====================================
DEFAULT LOGIN DETAILS
=====================================
Email: u25136608@tuks.co.za
Password: Vimbai@123
API Key: 6074fa0bad3b1bb72fe77a2f9e7c8a8b

=====================================
FUNCTIONALITY NOT IMPLEMENTED
=====================================
- Login/Logout 

=====================================
PASSWORD REQUIREMENTS
=====================================
Passwords must:
- Be at least 8 characters long
- Contain at least one uppercase letter
- Contain at least one lowercase letter
- Contain at least one digit
- Contain at least one special symbol

This is necessary because weak passwords are easily guessed
or cracked by brute force attacks. Strong passwords significantly
reduce the risk of unauthorized account access.

=====================================
HASHING ALGORITHM
=====================================
SHA-256 was chosen as the hashing algorithm because:
- It is a widely trusted and tested cryptographic hash function
- It produces a 256-bit hash which is sufficiently secure
- It is not reversible (one-way function)
- Blowfish was excluded as per assignment requirements

A dynamic random salt (32 hex characters = 16 bytes) is generated
for each user using random_bytes(). The salt is stored separately
in the database and combined with the password before hashing.
This prevents rainbow table attacks and ensures two users with
the same password have different hashes.

=====================================
API KEY GENERATION
=====================================
API keys are generated using bin2hex(random_bytes(16)) which
produces a cryptographically secure random 32-character
alphanumeric string. This ensures:
- Keys are unique and unpredictable
- Keys cannot be guessed or brute forced
- Each registered user gets a different key