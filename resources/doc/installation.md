# Installation

## Step 1 - Install RabbitMQ

You first need to install RabbitMQ on the machine that should deliver messages to recipients.  
Installation instructions are available at the [RabbitMQ website](https://www.rabbitmq.com/download.html).

## Step 2 - Install Composer (not mandatory but recommended)

To install Composer and let it manage the HAS-Server-Side dependencies, see the [installation instructions](https://getcomposer.org/doc/00-intro.md).

## Step 3 - Install the server side of the project

You can install the project with Composer, or by downloading the archive of the project.

### Using Composer (recommended)

Using Composer, just run the following command to download and install the project : `composer require Chrisp1tv/HAS-Server-Side`  
  
Once it's successfully installed, go to the step 4 !

### By downloading the project manually

If you don't want to use Composer, you can download the project manually.  
Then, download the last release of the project from [the releases page](https://github.com/Chrisp1tv/HAS-Server-Side/releases) of the project and unzip it.

## Step 4 - Configuration

Once it's installed, the server side of HAS must be configured. First, you must configure your server. For more information, read the [Web Server Configuration of Symfony Docs](https://symfony.com/doc/current/setup/web_server_configuration.html#apache-with-mod-php-php-cgi). You should configure your Web Server (e.g. Apache / Nginx...), and set environment variables.  

To find which environment variables must be set, open the `.env` file at the root of the project's server side.  

Then, you should run the assets (files, CSS, images...) compilation using Wepack Encore. To achieve it, use the following command (for Linux systems) : `./node_modules/.bin/encore production`.

Finally, run the installation command with a terminal opened at the project's root : `php bin/console has:setup`  

The command will handle most of the configurations, and will ask you to create the first administrator of the system who will be able to log in.

## Step 5 - Create the CRON job (or Windows equivalent) allowing the system to work correctly

To allow the system work correctly, you must add the following CRON job : `*/5 * * * * php path/to/project/bin/console has:run-services`.  
  
Please note that you should change the path by the real path where the project is hosted on your system.

If you want the campaigns to be sent every minute instead of every 5 minutes, use this CRON job instead : `* * * * * php path/to/project/bin/console has:run-services`.

## Step 6 - Deploy the client part of the project

To do it, consult the [HAS-Client-Side documentation](https://github.com/Chrisp1tv/HAS-Client-Side).