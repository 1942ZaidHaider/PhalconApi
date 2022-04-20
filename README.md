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
               
/auth : to generate access_token (valid for 1 day)  
  &ensp;&ensp;method : POST  
  &ensp;&ensp;Params : none  
