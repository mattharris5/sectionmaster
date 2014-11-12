#SectionMaster
&copy; 2005-2014 Clostridion Design & Support, LLC.

Use SectionMaster to run your OA section's conclave, online trading post, and other events. There are two complementary software components: 

* SM-Online is the PHP web application used for online registration and trading post before your event.
* SectionMaster Desktop is the Microsoft Access-based application used for on-site registration and event management.

Both components are provided in this repository. Unfortunately we cannot provide any additional support for this project.

##Requirements

To run SectionMaster, you will need:

* Apache HTTP Server 2.x
* PHP 4.4.x (not higher)
* MySQL (any version)
* SSL certificate for secure transactions
* Merchant account to process credit cards - Authorize.net preferred (if you use something else you'll have some coding changes to make)

##Software Setup

The software was designed to run in a particular shared hosting environment. You will want to make a number of modifications to function in a new hosting environment. There are a number of hard coded URLs, paths, and other items that might need to be updated. At the very least, you need to provide new credentials for the database connection and payment processing:

* Update the `dbconnect.php`, `includes/db_connect.php` and `register/admin/includes/geocode_updater.php` with your database connection information and credentials.
* Update the query parameters in `register/html/payment_viaklix.php` with your merchant account information and credentials.

##Database Setup

In the root directory is a sql script `sectionmaster.sql`. This script will create two databases: sm-online and w1n.  Search and replace the script for all instances of "w1n" with your section name without a hyphen, all lower-case (i.e., W-6W becomes w6w).

Two admin users will be created by this script:

1. **Global super admin** - access to all events, all sections

	- username: superadmin
	- password: abcd1234
	- section: (none)

2. **Section admin** - access to all events in own section

	- username: admin
	- password: abcd1234
	- section: W-1N (or whatever section you searched and replaced in the database setup script)

##License
&copy; 2005-2014 Clostridion Design & Support, LLC.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
* The name of the author may not be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
