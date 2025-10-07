System Requirement
- 
- PHP Version : 8.3 or above.
- Download Composer latest version : 2.4.1 (preferred)
- MySQL Version : 8.0.30 (preferred)
- Node Version : 20.12.2 (preferred)
-------------------------------------------------

Laravel Setup (Backend)
- 
- Clone your backend and frontend repository : **_git clone https://github.com/parnikaInsight/Borrowss.git_**


- Go to Backend folder 


- Use this command to install composer dependency : **_composer install_**.


- Copy env : **_cp .env.example .env_**

    ### Set database credentials in .env file
    
    - **DB_CONNECTION=mysql**
    - **DB_HOST=127.0.0.1**
    - **DB_PORT=3306**
    - **DB_DATABASE=laravel**
    - **DB_USERNAME=root**
    - **DB_PASSWORD=**
  

### Set database credentials in .env file
- Run database migration command : _**php artisan migrate**_

### Seeding database record
- Run database seeding command : _**php artisan db:seed**_


### Run Project : 
- Run project command : _**php artisan serve**_

-----------------------------------------

React.js Setup (Frontend)
- 

- Go to frontend folder and install npm dependencies.


- Run npm command : _**npm install**_

### Run Frontend Project
- run given command to start project : _**npm start**_
