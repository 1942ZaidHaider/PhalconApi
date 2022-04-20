# PhalconApi
Api in phalcon

**All endpoints except /auth require a 'access_token' query parameter with a valid access token**

## Api endpoints:
/products/search/{keyword} : to search for products
  method : GET
  Parameters : none
  
/products/get : to list all products
  method : GET
  Query Parameters (optional) : page : page number
                                per_page: number of items per page
/insert : to add new product 
  method : POST
  Body / Form Parameters (required) : name : Product name
                                      price : Product price
                                      
/auth : to generate access_token (valid for 1 day)
  method : POST
  Params : none
