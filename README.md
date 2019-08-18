# Managed Emails
Do you have a crapload of emails to send out to your users? Centrally manage them in the CMS, and refer to them in your codebase using a lookup label field. Content-manage your subject, message body, from address, and CC/BCC/ReplyTo addresses, with support for variables provided by a DataObject. Supports direct sending, or queuedjobs sending if it's available. 

This is a ModelAdmin for centrally managing emails within the CMS:

* [x] Specify a from address
* [x] Specify a subject
* [x] Compose an email body
* [x] Add additional recipients, in addition to user supplied ones
* [x] Queue an email to be sent, or configure it to send immediately


## Installation
1. add the repository to your composer.json. This is a private repo, so composer will not find it on packagist
2. `composer require elliotsawyer/managed-emails`
3. Run `vendor/bin/sake dev/build flush=`

## Configuration
There are two fields that can be configured.
```yml
ElliotSawyer\ManagedEmails\ManagedEmail:
  #default "From" address
  default_from_address: 'hello@sawyer.nz'
  #WYSIWYG field defaults to <p></p>, so can never be empty
  minimum_body_length: 10
```

## Usage
Define a new Managed Email in the "Email management" admin area:
1. Set a label: this is the identifier you'll "look up" within your code
2. Configure any other recipients: To, CC, BCC, and Reply-To addresses
3. Set a From address, Subject, and Message body
4. You can make references to specific variables in your message body, and pass them into into the message body from your code.
5. If you have Queued Jobs installed, your message will be queued for sending. If not, it will send immediately

```php
        /**
         * $messageBody = 'Hi {$Member.Email}, this is from {$FromPerson} located at {$Address}.'
         * **/
        $email = ManagedEmail::get()->find('Label', 'EXAMPLE_MESSAGE');
        if ($email && $email->ID) {
            //send a message to an email address, with template variables defined
            $email->send('someone@example.com', [
                'Member' => Member::get()->first(),
                'FromPerson' =>  'Elliot Sawyer',
                'Address' => '1234 Nowhere Street'
            ]);
        }
```

## Copyright
&copy; 2019 Elliot Sawyer, CryptoPay Limited. All rights reserved. 

## Support
Like my work? Consider shouting me a coffee or a small donation if this module helped you solve a problem. I accept cryptocurrency at the following addresses:
* Bitcoin: 12gSxkqVNr9QMLQMMJdWemBaRRNPghmS3p
* Bitcoin Cash: 1QETPtssFRM981TGjVg74uUX8kShcA44ni
* Litecoin: LbyhaTESx3uQvwwd9So4sGSpi4tTJLKBdz
