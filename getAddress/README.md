Resurs Bank OpenCart Plugin - Get Address

===============
#How to get it working.

##Manual
In the file : catalog\view\theme\default\template\checkout\guest.tpl and\or catalog\view\theme\default\template\checkout\register.tpl
If you have any other checkouts or themes it should be placed in those files instead.

* Added the content of the getAddress.js file to end very end of the file.
* Added the conent of the getAddress.html to where you want the getAddress function to be, in the standard cart it is at the very top of the file.

##VQMod
 * Copy the file from the vqmod-standard folder to the vqmod\xml folder in your opencart installtion
 * If you use any template or custom checkout you can try to change the path and files in the vqmod.

##Other
* getAddress Only works for Sweden.
	
##Verified to be working with:

* 1.5.6.4 , with standard checkout
* 1.5.3.1 , with standard checkout