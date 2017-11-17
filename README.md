PM Connect API Documents

Preface
For this task I’ve used the Yii2 framework. I chose this as it allows for rapid development of APIs. What I’ve developed can be roughly equated to one day’s effort(including the docs). Hopefully this will give you a good idea of what can achieve on any given day in the office. At the end of they day I could keep adding features but then I’d never end up handing the code over. 

I’ve included dummy data in the DB. If you wish to generate more, run “php yii seed” in the root project folder on the command line.

Set Up
Put the files in a folder called pmconnect in the web folder of a server. For development I used XAMPP VM for OSX as it’s very fast to set up. Create a database called “pmconnect” and import the sql file.

How To

All actions return JSON. All responses return a status mode. 200 with a data array on success. 400 with an error message with an invalid request.

To re-subscribe or create a subscription, use the action:
../pmconnect/web/api/subscribe?product_id=product id here&msisdn=phone or alias here

To unsubscribe, use this action:
../pmconnect/web/api/unsubscribe?product_id=product id here&msisdn=phone number or alias here

To search, us this action:
../http://localhost:8080/pmconnect/web/api/search?product_id=product id here&msisdn=phone number or alias here

Only one needs to be set as per the specification. It either returns a single subscription or several depending on if both parameters are set or not. Subscriptions returned with their status, start date and end date. 

Caveats

As this is is a test, I thought it would be worth noting what hasn’t been included. 

I’ve not modified the htaccess file so the public directory points to Yii2’s standard web folder.

Further Development

Validation outside of what the framework offers.

An API key system.

Unit testing.

A management system for users, subscriptions and products. 

Database done via migrations.

