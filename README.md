1: run composer install in the terminal to install the dependencies (you may need to install composer first)

2: run sql script in the db/login.sql file in your local xammp phpmyadmin

3: you need to be running mysql and apache in your xammp to run the project

4: do not push to  master, create a new branch and push to that branch and open a pull request to merge the branch to master...

5: you need a env file in the root of the project with the following variables: 

SMTP_USER="provided_email@gmail.com"
SMTP_PASS="your_app_password"