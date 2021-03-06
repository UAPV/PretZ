>PretZ is a software developed by University of Avignon. It allows to lend products thanks barcode.

>You must have two computer : one for a person who want to borrow and the other for the person who lends.


Features
--------

*  Php 5/MySql Framework Symfony 1.4 and Propel 1.5 ORM.
*  Authentication CAS + LDAP (thaks 2 plugins)
*  Jquery Javascript Library and use of Jqplot (plugin of JQuery) for graphics
*  FPDF class for barcode
*  Email notifications
*  Statistics of lends and products




Requirements
------------

*  Apache configured with `mod_rewrite` and the `AllowOverride` of your virtual host set to “FileInfo Options” or “All” (**important !**) `a2enmod rewrite && apache2ctl restart`
*  You will need php5-curl to be able to authenticate against a CAS server
*  You will need php5-ldap to be able to identicate with a LDAP server





Installation
------------


* Download PretZ sources :
   * from Git

        git clone git://github.com/UAPV/PretZ.git web

   * from SVN

        svn checkout http://svn.github.com/UAPV/PretZ.git web

   * from a package

        tar -xvvf pretz.tar.gz
        cp pretz/* web

* Create your database pretz with these commands :
       
      > mysqladmin -uroot -p create pretz

      > php symfony configure:database "mysql:host=localhost;dbname=pretz" root MySecret

      > php symfony propel:build-all --no-confirmation

      > php symfony cc
      
      > php symfony propel:data-load

* In this version, there is no install module so you have to write your database password in `/config/database.yml`. You must add your IP address in database (`option_ip`).
To finish, to access to administration page, you must have a first name of administrator. So you write your name in `web/uploads/god.yml` file.


Description
-----------

> There are different levels for users : person who lends is a "gestionnaire" and person who lends and can change database (ie access admin page) is an "admin".
> You can lend product into `pretz/emprunt`

> PretZ contains two parts :

   1.FRONTEND

   * apps/frontend/modules/default
   > Customize error pages due to rights access

   * apps/frontend/modules/utilisateur
   > If you have right to access this page (can be limited to IP addresses by the admin), you can login via LDAP or you can scan a library card.
   
      > (To change validation of these cards, go to "users.js") to borrow a product.

   * apps/frontend/modules/emprunt
   > If you are "gestionnaire", you can access to this page. This page uses a refresh function (check in the database if a person want to borrow).

      > This page uses much ajax you can see here "web/js/emprunt.js".
	
      > This module is the hardest of all because it contains much ajax and much features like "scan a product","add user if he can't be logged by CAS", "return a product" and so on.

   * apps/frontend/modules/stat
   > Module to display statistics as number of lends. You can choice it by product or category with a historic.
      
      > Use of Jqplot (plugin of JQuery). You must install it to display graphics.

   * apps/frontend/modules/materiel
      > List of materiel to display for users with products available or not. You have any javascript here.



 2.BACKEND

   * "category","product", "service" are modules directly created by Symfony. 
> It's forms to add, delete or edit each item. However a thing has been added : you can print or see barcode of products.

   * "list" : allow to register a student on a list of an employee. 
> A product can be borrowed by a student only if it doesn't need a "referant" (indicated during creating of product). In this part, you can add students on a list or delete a list.

   * "main" : home of backend with actions you can do in this part. 
> You can also print all barcode directly.

   * "option" : here, you can add a new template and named it. 
> You can also see and add ip addresses which can access to PretZ pages. 

    > Lastly you can activate option auto mail. If a person borrows a product with a "referant", an email is sent to this person or service.

   * "administrator" : add or delete rights to a person.



How to personalize ?
---------------------------------------

> First, get to backend and frontend/config/app.yml to change LDAP and CAS addresses. 

> In this file, you can change SMTP address and port.

> You've got many options to disable like "ip addresses adding", "new template creating" or "auto email sending". Theses options can be a update in `app/backend/modules/options/templates/indexSuccess.php`