Resurs Bank OpenCart Plugin
===============

Pre Requsitions:
* Don't use localhost/127.0.0.1 or any local ip of on your opencart installtion, Resurs Bank needs to be able to contact your opencart.
* An account with Payment Methods needs to be given to you from Resurs Bank.
* Will only work for Sweden, Norway and Denmark at the moment and the currency must also match for that country, so for Sweden you must use SEK to enable the payment method in the cart.

Installtion:
Copy the content of the upload catalog into the folder of your opencart.


Payment Admin - Resurs Bank: 
	* Install the plugin.
	* Setup status that should be match when an action occurs.
	* Go to the country you will use and fill in the username and password, (The are always diffrent for diffrent countries).
	* If you enter correct username and password , payment methods should show. 
	* Enable those you want,  if you want to use a custom text to show in the checkout , fill in "Custom name to be displayed as PaymentMethodName".
	* An default image will be shown even if you don't enter any image url, if you want to use a custom one, fill in the whole url in the "Image url" and follow up with the size in "image width" and "image height".
	* After you done this go to order totals to fill in what you want to charge for the payment method.
		
Order Totals Admin - Resurs Bank Fee:
	* Install the plugin.
	* If you done correct in the "Payment Admin" there should be payment methods showing.
	* Fill in how much you should charge for the payment method without any TAX, and select the corresponding TAX Class.
	* Under "Invoice Line" fill in both what should be shown on the Invoice and on the Check Out line.
	
Sales Order:
    * If you view and order, under history, the flow of the order what Resurs Bank has done with it should be easy to follow.

Log:
The logging will be avalible under "System->Error Loggs"
	
	
Verified to be working with:

* 1.5.6.4
* 1.5.3.1