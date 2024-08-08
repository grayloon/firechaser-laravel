# FireChaser™
A Product from Gray Loon

## Prerequisites 

You must have an account in [FireChaser](https://firechaser.io) and have a registered domain tied to your account. 

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

Based on your subscription tier, FireChaser will send a POST request to your website at a set interval to fetch any package updates.
