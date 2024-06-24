# FireChaser

## Prerequisites 

You must have an account in [FireChaser](TODO) and have a registered domain tied to your account. 

You will receive an **API Site Key** that you will need to place in your `.env` file:

```dotenv
FIRECHASER_API_SITE_KEY="YOUR_KEY_HERE"
```

## Installation

Install the FireChaser package at the root level of your Laravel application:

```console
composer require grayloon/firechaser-laravel
```

That's it, you're done! 

## Syncing

Whenever you update or install composer packages, the `firechaser:sync` command will fire. Don't worry, we'll only be 
reporting vendor packages whenever your environment is in production, debug is turned off, and the application url does
not have a `.test` TLD.

This `firechaser:sync` command will send an encoded JWT Token to FireChaser containing all your application's vendor files 
and PHP version, using your **FireChaser Key** as a signature.

Once FireChaser receives this JWT Token, we will confirm the origin of the request to ensure it matches a registered domain.
Then, if we find it, we will use the registered domain's key, which should match the FireChaser Key, to decode the JWT Token.
