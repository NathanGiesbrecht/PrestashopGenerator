PrestashopGenerator
===================

A simple generator module for Prestashop

See [video on YouTube][1]

#Install
Then clone repo into a directory of your choosing. To stay with the video, we'll use `bin`

    git clone https://github.com/BWLab/PrestashopGenerator.git bin
    cd bin
	chmod u+x console.sh
    cd ..

Now create composer.json

    touch composer.json

and insert 
    
    {
        "require": {
            "twig/twig": "1.*",
            "symfony/console": "2.5.*@dev",
            "symfony/finder": "2.5.*@dev",
            "symfony/filesystem": "2.3.*@dev"
        },
        "autoload": {
        "classmap": ["bin/commands"]
        }
    }

Install dependencies
	composer install or composer update
    
#Use generator
##init module
    
    bin/console.sh module:init <name> <module display name> "<dscription>" <author> <tab>

##create view module

    bin/console.sh module:view:add --name=<module name> --viewname<file template name>
    


  [1]: http://youtu.be/E6gmHFSGYxk