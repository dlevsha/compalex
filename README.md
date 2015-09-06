# README

## What is Compalex?
Compalex is a lightweight script to compare two database schemas. It supports MySQL, MS SQL Server and PostgreSQL.


## Requirements
Compalex is only supported by PHP 5.4 and up with PDO extension.

## Installation
	$ git clone https://github.com/dlevsha/compalex.git
	$ cd compalex
	
Open `config.php` and uncomment required configuration section. For example MySQL section. This section describes two connections each of compared databases.

	// MySQL sample config
	define('FIRST_DSN',  'mysql://login:password@localhost/compalex_test_1');
	define('SECOND_DSN', 'mysql://login:password@localhost/compalex_test_2');
	
where

`mysql` - database driver	
`login` and `password` - login and password to access your database  
`localhost` - database host name or IP	
`compalex_test_1` - database name

If you don't have password your connection will look like

	mysql://login@localhost/compalex_test_1
	
Edit conection name section

	define('FIRST_DATABASE_NAME', 'Production database');
	define('SECOND_DATABASE_NAME', 'Developer database');	
These names will display as a database name.

Inside `compalex` directory run  

	$ php -S localhost:8000
	
Now open your browser and type `http://localhost:8000/`

You'll see database schema of two compared databases.

-
![Database Compare Panel](https://cloud.githubusercontent.com/assets/1639576/9703302/1327b858-5488-11e5-856a-96b139c7b938.png)	
-

LICENSE
-------

Copyright (c) 2015, Levsha Dmitry

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
	