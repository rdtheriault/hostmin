# hostmin
Simple hosting solution to host/manage many websites with many users. The goal of this project is for CS teachers to have a low cost easy to manage web hosting server for student projects by mimicking cPanel (very loosely). This version is designed to work on a system as simple as a RaspberryPi. It could also run on a WAMP server running on the teacher/professor's computer. Security was not a major consideration for the project and most of the security just keeps students out of eachothers folders. Also, it is not meant to run on an online server (but in theory it could) thus students could only access it while on the same network as the server. I plan to make a more secure online version at some point. 

## Setup
1. Ensure you have [WAMP](https://youtu.be/dWmTOizpO_g) or [LAMP](https://ubuntu.com/server/docs/lamp-applications) set up on your server. 
2. Copy the files from this repository into the main `www` folder.
3. The files come with a SQLite database pre configured but also the SQL to create one of your own
4. Additionally, it comes with a template CSV file to start to add users
5. If you use the default database it will have an admin which is administrator and a default user called testuser.

### Optional Setup
1. Create database
  - Download [SQLITE](https://www.sqlite.org/2024/sqlite-tools-win-x64-3450200.zip)
  - There is a file in admin with the SQL commands
2. Add admin
  - You will need to create an insert statement to add an admin
  - `INSERT INTO users (name, username, password, admin) VALUES ('admin', 'administrator', 'notP@$$word1234', 1);`
  - Change the values as needed

## Use
### Create users
You will need to create an admin if you don't use the default admin (administrator - notP@$$word1234)
1. Logging on as an admin will direct you to the create user page. You can add a user one at a time or using a CSV.
2. If using the CSV, there is a template. Delete the first two rows and then modify them as needed.
3. Creating a user will add them to the database, create their folder, and then copy over the admin files that they need to control their pages.

### User Directions
Users will need to log into the same page as an admin. After doing so it will redirect the user to their admin folder.
1. The first time they log on, they will only see the default index.php page (I do not have a delete feature yet).
2. At the "bottom" of the page the can add another file or folder. The system will not stop them from not adding an extension to a file or adding an extension to a folder.
3. If they click on a file it will open to  new tab
4. If they click on a folder, it will show that folders contents
5. If they are not in their "root" folder, the dot dot will allow them to go back a folder
6. When the are one the change.php after clicking a file, they will see the contents of the file, they can change it, then save over the current contents with the changes.
7. They will need to close the tab when done.
8. There is no syntax highlighting at this time

### Viewing
Anyone can view any user website by simple typing `url/username`. 
