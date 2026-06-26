===================================================
  ShopZone - PHP E-Commerce Project
  Setup & Run Instructions
===================================================

REQUIREMENTS
------------
- XAMPP (with Apache + MySQL running)
- PHP 8.x (included in XAMPP)

STEP 1 — Extract the ZIP
-------------------------
Extract this ZIP anywhere inside your XAMPP htdocs folder.
Example:
  C:\xampp\htdocs\shopzone\

After extracting you should see these folders inside shopzone\:
  app\, config\, core\, db\, public\, helpers\, ...

STEP 2 — Start XAMPP
---------------------
Open XAMPP Control Panel and start:
  - Apache
  - MySQL

STEP 3 — Open Terminal IN the project folder
---------------------------------------------
Open Command Prompt (CMD) and navigate to the project:

  cd C:\xampp\htdocs\shopzone

  (Replace "shopzone" with whatever folder name you used)

IMPORTANT: All commands below must be run from INSIDE this folder.

STEP 4 — Create the database
------------------------------
  php db\setup.php

STEP 5 — Add sample data
--------------------------
  php db\seed.php

STEP 6 — Start the server
--------------------------
  php -S localhost:8000 -t public public/router.php

STEP 7 — Open in browser
--------------------------
  http://localhost:8000

===================================================
  LOGIN CREDENTIALS
===================================================

Admin:
  Email:    admin@shopzone.com
  Password: admin1234

Customer (sample):
  Email:    sara@example.com
  Password: customer123

===================================================
  IMPORTANT NOTES
===================================================

- Run ALL commands from INSIDE the shopzone folder.
- MySQL must be running in XAMPP before setup.php.
- If you see a database error, make sure MySQL is on.
- The database name is: shopzone
  (created automatically by setup.php)

===================================================
