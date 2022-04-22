# PhalconApi
Api in phalcon

**All endpoints except /auth require a 'access_token' query parameter with a valid access token**

## Api endpoints:
/api/products/search/{keyword} : to search for products  
&ensp;&ensp;method : GET  
&ensp;&ensp;Parameters : none  
  
/api/products/get : to list all products 
&ensp;&ensp;method : GET  
&ensp;&ensp;Query Parameters (optional) :  
&ensp;&ensp;&ensp;&ensp;page : page number  
&ensp;&ensp;&ensp;&ensp;per_page: number of items per page  
  
/api/products/get/{product_id} : to fetch product by id 
&ensp;&ensp;method : GET  
&ensp;&ensp;Query Parameters : none  
/api/insert : to add new product   
  &ensp;&ensp;method : POST  
  &ensp;&ensp;Body / Form Parameters (required) :  
  &ensp;&ensp;&ensp;&ensp;name : Product name  
  &ensp;&ensp;&ensp;&ensp;price : Product price  
  
/api/orders/create : to Create a new order   
  &ensp;&ensp;method : POST  
  &ensp;&ensp;Body / Form Parameters (required) :  
  &ensp;&ensp;&ensp;&ensp;product_id : Product ID  
  &ensp;&ensp;&ensp;&ensp;qty : Product Quantity  
  
/api/orders/create : to update order status   
  &ensp;&ensp;method : PUT  
  &ensp;&ensp;Body / x-www-form-urlencoded (required) :  
  &ensp;&ensp;&ensp;&ensp;order_id : Order ID  
  &ensp;&ensp;&ensp;&ensp;status : new Status  
  
/api/orders/get : to update order status   
  &ensp;&ensp;method : GET  
  &ensp;&ensp;Params : None    

## Auth code flow:  
  &ensp;&ensp;redirect to the /register endpoint with callback url in the query parameters with the key 'callback'  
  &ensp;&ensp;Example :  
  &ensp;&ensp;&ensp;&ensp;/register?callback=http%3A%2F%2Flocalhost%3A8080  
  &ensp;&ensp;After this you will be redirected to the callback url provided with the access token in the query parameters in the key 'access_token'
