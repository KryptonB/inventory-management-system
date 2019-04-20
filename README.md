# Inventory Management System

[![GitHub](https://img.shields.io/github/license/kryptonb/inventory-management-system.svg?style=popout)](https://choosealicense.com/licenses/mit/)

A simple PHP web system for managing an inventory.  
  
Screenshots:   
 
![Sale details](https://github.com/KryptonB/inventory-management-system/blob/master/screenshots/sale.PNG)  
![Search details](https://github.com/KryptonB/inventory-management-system/blob/master/screenshots/search.PNG)  

## Installation
* Clone the repository and move the root folder to the deployment folder of your browser. (for Apache, this is htdocs)
* Create a blank DB called *shop_inventory* in MySQL
* Create a new user called _inventoryUser_ with a password as _password_ and give full permission to that user.
* Load the sql dump to the newly created _shop_inventory_ database
* Change the root url of your website in [constants.php](inc/config/constants.php) file

## Requirements
* PHP
* MySQL
* Apache
* Google Chrome web browser (JavaScript enabled)
* Internet connection with a reasonable speed

## Usage
* Access the login.php file from via browser and give _guest_ as username and _1234_ as password

## Built With
* PHP - Scripting language
* MySQL - Database management system
* [HTML5](https://en.wikipedia.org/wiki/HTML5) - Basic markup
* [CSS3](https://en.wikipedia.org/wiki/Cascading_Style_Sheets) - Basic styling
* [Bootstrap 4.1.1](https://getbootstrap.com/) - Responsive framework
* [jQuery 3.3.1](https://jquery.com/) - JS framework

## Acknowledgments
* Inspired by many similar projects online
