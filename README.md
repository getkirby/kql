# Kirby QL

Kirby's Query Language API combines the flexibility of Kirby's data structures, the power of GraphQL and the simplicity of REST.

The Kirby QL API takes POST requests with standard JSON objects and returns highly customized results that fit your application.

## Alpha

**This plugin is still in an experimental state. Please don't use it in production yet.**

## Example

POST: /api/query

```json
{
    "query": "page('photography').children",
    "select": {
        "url": true,
        "title": true,
        "text": "page.text.markdown",
        "images": {
            "query": "page.images",
            "select": {
                "url": true
            }
        }
    },
    "pagination": {
        "limit": 10
    }
}
```

Response:

```json
{
    "code": 200,
    "result": {
        "data": [
            {
                "url": "https://example.com/photography/trees",
                "title": "Trees",
                "text": "Lorem <strong>ipsum</strong> …",
                "images": [
                    { "url": "https://example.com/media/pages/photography/trees/1353177920-1579007734/cheesy-autumn.jpg" },
                    { "url": "https://example.com/media/pages/photography/trees/1940579124-1579007734/last-tree-standing.jpg" },
                    { "url": "https://example.com/media/pages/photography/trees/3506294441-1579007734/monster-trees-in-the-fog.jpg" }
                ]
            },
            {
                "url": "https://example.com/photography/sky",
                "title": "Sky",
                "text": "<h1>Dolor sit amet</h1> …",
                "images": [
                    { "url": "https://example.com/media/pages/photography/sky/183363500-1579007734/blood-moon.jpg" },
                    { "url": "https://example.com/media/pages/photography/sky/3904851178-1579007734/coconut-milkyway.jpg" }
                ]
            }
        ],
        "pagination": {
            "page": 1,
            "pages": 1,
            "offset": 0,
            "limit": 10,
            "total": 2
        }
    },
    "status": "ok"
}
```

## Installation

### Manual

[Download](https://github.com/getkirby/kql/releases) and copy this repository to `/site/plugins/kql` of your Kirby installation.

### Composer

```
composer require getkirby/kql
```

## Documentation

### API Endpoint

KQL adds a new `query` API endpoint to your Kirby API. (i.e. https://yoursite.com/api/query) The endpoint requires authentication: https://getkirby.com/docs/guide/api/authentication

### Sending POST requests

You can use any HTTP request library in your language of choice to make regular POST requests to your `/api/query` endpoint. In this example, we are using [axios](https://github.com/axios/axios) and Javascript to get data from our Kirby installation. 

```js
const axios = require("axios")

const api = "https://yoursite.com/api/query";
const username = "apiuser";
const password = "secret-api-password";

const response = await axios.post(api, {
    query: 'page("blog").children',
    select: {
        title: true,
        text: 'page.text.kirbytext',
        slug: true,
        date: 'page.date.toDate("d.m.Y")'
    }
}, {
  auth: {
    username: username,
    password: password
  }
});

console.log(response);
```

### `query`

With the query, you can fetch data from anywhere in your Kirby site. You can query fields, pages, files, users, languages, roles and more. 

#### Queries without selects

When you don't pass the select option, Kirby will try to come up with the most useful result set for you. This is great for simple queries.

##### Fetching the site title
```js
const response = await axios.post(api, {
    query: 'site.title',
    auth: { ... }
});

console.log(response.data);
```

Result: 

```js
{
  code: 200,
  result: "Kirby Starterkit",
  status: "ok"
}
```

##### Fetching a list of page ids
```js
const response = await axios.post(api, {
    query: 'site.children',
    auth: { ... }
});

console.log(response.data);
```

Result: 

```js
{
  code: 200,
  result: [
    "photography",
    "notes",
    "about",
    "error",
    "home"      
  ],
  status: "ok"
}
```

#### Running field methods

Queries can even execute field methods. 

```js
const response = await axios.post(api, {
    query: 'site.title.toUpper',
    auth: { ... }
});

console.log(response.data);
```

Result: 

```js
{
  code: 200,
  result: "KIRBY STARTERKIT",
  status: "ok"
}
```

## Credits

[Bastian Allgeier](https://getkirby.com)

## License

<http://www.opensource.org/licenses/mit-license.php>
