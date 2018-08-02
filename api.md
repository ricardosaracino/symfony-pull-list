
# JSend

*https://labs.omniti.com/labs/jsend*

```
{
    "status" : "fail",
    "data" : { "title" : "A title is required" }
}

{
    status : "success",
    data : {
        "posts" : [
            { "id" : 1, "title" : "A blog post", "body" : "Some useful content" },
            { "id" : 2, "title" : "Another blog post", "body" : "More content" },
        ]
     }
}

{
    "status" : "fail",
    "data" : { "title" : "A title is required" }
}


{
    "status" : "error",
    "message" : "Unable to communicate with database"
}
```

# Marvel API

*https://developer.marvel.com/docs*

results are the "comics" and they are always returned as an array even for a single item

*https://gateway.marvel.com:443/v1/public/comics?title=cable&apikey=39754936249867122be92159703640da*

```
{
  "code": 200,
  "status": "Ok",
  "copyright": "© 2018 MARVEL",
  "attributionText": "Data provided by Marvel. © 2018 MARVEL",
  "attributionHTML": "<a href=\"http://marvel.com\">Data provided by Marvel. © 2018 MARVEL</a>",
  "etag": "a8dbb5aa00b15071a1c945f253c8ac30cc402b8c",
  "data": {
    "offset": 0,
    "limit": 20,
    "total": 159,
    "count": 20,
    "results": [
      {
        "id": 67907,
        "digitalId": 0,
        "title": "Cable (2017) #159",
        "issueNumber": 159,
        "variantDescription": "",
        
        ....
 ```
 
# Error Codes & Messages
 
Status | Reason | Trigger
------------ | ------------- | -------------
401 | Authentication Required | ApiLoginAuthenticator::start HTTP_UNAUTHORIZED
401 | Invalid Password | ApiLoginAuthenticator::onAuthenticationFailure HTTP_UNAUTHORIZED
403 | ... | 
409 | Limit greater than 100.
409 | Limit invalid or below 1.
409 | Invalid or unrecognized parameter.
409 | Empty parameter.
409 | Invalid or unrecognized ordering parameter.
409 | Too many values sent to a multi-value list filter.
409 | Invalid value passed to filter.
