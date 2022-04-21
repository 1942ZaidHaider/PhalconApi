# PhalconApi
Api in phalcon

**All endpoints except /auth require a 'access_token' query parameter with a valid access token**

## Api endpoints:
/products/search/{keyword} : to search for products  
&ensp;&ensp;method : GET  
&ensp;&ensp;Parameters : none  
  
/products/get : to list all products  
&ensp;&ensp;method : GET  
&ensp;&ensp;Query Parameters (optional) :  
&ensp;&ensp;&ensp;&ensp;page : page number  
&ensp;&ensp;&ensp;&ensp;per_page: number of items per page  
  
/insert : to add new product   
  &ensp;&ensp;method : POST  
  &ensp;&ensp;Body / Form Parameters (required) :  
  &ensp;&ensp;&ensp;&ensp;name : Product name  
  &ensp;&ensp;&ensp;&ensp;price : Product price  
  

## Auth code flow:  
    &ensp;&ensp;redirect to the /register with callback url in the query parameters with the key 'callback'  
    &ensp;&ensp;Example :  
        &ensp;&ensp;&ensp;&ensp;/register?callback=http%3A%2F%2Flocalhost%3A8080  
    &ensp;&ensp;After this you will be redirected to the callback url provided with the access token in the query parameters in the key 'access_token'
