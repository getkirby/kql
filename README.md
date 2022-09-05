# Kirby QL

Kirby's Query Language API combines the flexibility of Kirby's data structures, the power of GraphQL and the simplicity of REST.

The Kirby QL API takes POST requests with standard JSON objects and returns highly customized results that fit your application.

## Playground

You can play in our [KQL sandbox](https://kql.getkirby.com). The sandbox is based on the Kirby starterkit.

> ℹ️ Source code of the playground is [available on GitHub](https://github.com/getkirby/kql.getkirby.com).

## Example

Given a POST request to: `/api/query`

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

<details open>
<summary>🆗 Response</summary>

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
          {
            "url": "https://example.com/media/pages/photography/trees/1353177920-1579007734/cheesy-autumn.jpg"
          },
          {
            "url": "https://example.com/media/pages/photography/trees/1940579124-1579007734/last-tree-standing.jpg"
          },
          {
            "url": "https://example.com/media/pages/photography/trees/3506294441-1579007734/monster-trees-in-the-fog.jpg"
          }
        ]
      },
      {
        "url": "https://example.com/photography/sky",
        "title": "Sky",
        "text": "<h1>Dolor sit amet</h1> …",
        "images": [
          {
            "url": "https://example.com/media/pages/photography/sky/183363500-1579007734/blood-moon.jpg"
          },
          {
            "url": "https://example.com/media/pages/photography/sky/3904851178-1579007734/coconut-milkyway.jpg"
          }
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

</details>

## Installation

### Manual

[Download](https://github.com/getkirby/kql/releases) and copy this repository to `/site/plugins/kql` of your Kirby installation.

### Composer

```bash
composer require getkirby/kql
```

## Documentation

### API Endpoint

KQL adds a new `query` API endpoint to your Kirby API (i.e. `yoursite.com/api/query`). This endpoint [requires authentication](https://getkirby.com/docs/guide/api/authentication).

You can switch off authentication in your config at your own risk:

```php
return [
  'kql' => [
    'auth' => false
  ]
];
```

### Sending POST Requests

You can use any HTTP request library in your language of choice to make regular POST requests to your `/api/query` endpoint. In this example, we are using [ohmyfetch](https://github.com/unjs/ohmyfetch) (a better fetch API for Node and the browser) and JavaScript to retreive data from our Kirby installation.

```js
import { $fetch } from "ohmyfetch";

const api = "https://yoursite.com/api/query";
const username = "apiuser";
const password = "strong-secret-api-password";

const headers = {
  Authorization: Buffer.from(`${username}:${password}`).toString("base64"),
};

const response = await $fetch(api, {
  method: "post",
  body: {
    query: "page('notes').children",
    select: {
      title: true,
      text: "page.text.kirbytext",
      slug: true,
      date: "page.date.toDate('d.m.Y')",
    },
  },
  headers,
});

console.log(response);
```

### `query`

With the query, you can fetch data from anywhere in your Kirby site. You can query fields, pages, files, users, languages, roles and more.

#### Queries Without Selects

When you don't pass the select option, Kirby will try to come up with the most useful result set for you. This is great for simple queries.

##### Fetching the Site Title

```js
const response = await $fetch(api, {
  method: "post",
  body: {
    query: "site.title",
  },
  headers,
});

console.log(response);
```

<details>
<summary>🆗 Response</summary>

```js
{
  code: 200,
  result: "Kirby Starterkit",
  status: "ok"
}
```

</details>

##### Fetching a List of Page IDs

```js
const response = await $fetch(api, {
  method: "post",
  body: {
    query: "site.children",
  },
  headers,
});

console.log(response);
```

<details>
<summary>🆗 Response</summary>

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

</details>

#### Running Field Methods

Queries can even execute field methods.

```js
const response = await $fetch(api, {
  method: "post",
  body: {
    query: "site.title.upper",
  },
  headers,
});

console.log(response);
```

<details>
<summary>🆗 Response</summary>

```js
{
  code: 200,
  result: "KIRBY STARTERKIT",
  status: "ok"
}
```

</details>

### `select`

KQL becomes really powerful by its flexible way to control the result set with the select option.

#### Select Single Properties and Fields

To include a property or field in your results, list them as an array. Check out our [reference for available properties](https://getkirby.com/docs/reference) for pages, users, files, etc.

```js
const response = await $fetch(api, {
  method: "post",
  body: {
    query: "site.children",
    select: ["title", "url"],
  },
  headers,
});

console.log(response);
```

<details>
<summary>🆗 Response</summary>

```js
{
  code: 200,
  result: {
    data: [
      {
        title: "Photography",
        url: "/photography"
      },
      {
        title: "Notes",
        url: "/notes"
      },
      {
        title: "About us",
        url: "/about"
      },
      {
        title: "Error",
        url: "/error"
      },
      {
        title: "Home",
        url: "/"
      }
    ],
    pagination: {
      page: 1,
      pages: 1,
      offset: 0,
      limit: 100,
      total: 5
    }
  },
  status: "ok"
}
```

</details>

You can also use the object notation and pass true for each key/property you want to include.

```js
const response = await $fetch(api, {
  method: "post",
  body: {
    query: "site.children",
    select: {
      title: true,
      url: true,
    },
  },
  headers,
});

console.log(response);
```

<details>
<summary>🆗 Response</summary>

```js
{
  code: 200,
  result: {
    data: [
      {
        title: "Photography",
        url: "/photography"
      },
      {
        title: "Notes",
        url: "/notes"
      },
      {
        title: "About us",
        url: "/about"
      },
      {
        title: "Error",
        url: "/error"
      },
      {
        title: "Home",
        url: "/"
      }
    ],
    pagination: { ... }
  },
  status: "ok"
}
```

</details>

#### Using Queries for Properties and Fields

Instead of passing true, you can also pass a string query to specify what you want to return for each key in your select object.

```js
const response = await $fetch(api, {
  method: "post",
  body: {
    query: "site.children",
    select: {
      title: "page.title",
    },
  },
  headers,
});

console.log(response);
```

<details>
<summary>🆗 Response</summary>

```js
{
  code: 200,
  result: {
    data: [
      {
        title: "Photography",
      },
      {
        title: "Notes",
      },
      ...
    ],
    pagination: { ... }
  },
  status: "ok"
}
```

</details>

#### Executing Field Methods

```js
const response = await $fetch(api, {
  method: "post",
  body: {
    query: "site.children",
    select: {
      title: "page.title.upper",
    },
  },
  headers,
});

console.log(response);
```

<details>
<summary>🆗 Response</summary>

```js
{
  code: 200,
  result: {
    data: [
      {
        title: "PHOTOGRAPHY",
      },
      {
        title: "NOTES",
      },
      ...
    ],
    pagination: { ... }
  },
  status: "ok"
}
```

</details>

#### Creating Aliases

String queries are a perfect way to create aliases or return variations of the same field or property multiple times.

```js
const response = await $fetch(api, {
  method: "post",
  body: {
    query: "page('notes').children",
    select: {
      title: "page.title",
      upperCaseTitle: "page.title.upper",
      lowerCaseTitle: "page.title.lower",
      guid: "page.id",
      date: "page.date.toDate('d.m.Y')",
      timestamp: "page.date.toTimestamp",
    },
  },
  headers,
});
```

<details>
<summary>🆗 Response</summary>

```js
{
  code: 200,
  result: {
    data: [
      {
        title: "Explore the universe",
        upperCaseTitle: "EXPLORE THE UNIVERSE",
        lowerCaseTitle: "explore the universe",
        guid: "notes/explore-the-universe",
        date: "21.04.2018",
        timestamp: 1524316200
      },
      { ... },
      { ... },
      ...
    ],
    pagination: { ... }
  },
  status: "ok"
}
```

</details>

#### Subqueries

With such string queries you can of course also include nested data

```js
const response = await $fetch(api, {
  method: "post",
  body: {
    query: "page('photography').children",
    select: {
      title: "page.title",
      images: "page.images",
    },
  },
  headers,
});
```

<details>
<summary>🆗 Response</summary>

```js
{
  code: 200,
  result: {
    data: [
      {
        title: "Trees",
        images: [
          "photography/trees/cheesy-autumn.jpg",
          "photography/trees/last-tree-standing.jpg",
          "photography/trees/monster-trees-in-the-fog.jpg",
          "photography/trees/sharewood-forest.jpg",
          "photography/trees/stay-in-the-car.jpg"
        ]
      },
      { ... },
      { ... },
      ...
    ],
    pagination: { ... }
  },
  status: "ok"
}
```

</details>

#### Subqueries With Selects

You can also pass an object with a `query` and a `select` option

```js
const response = await $fetch(api, {
  method: "post",
  body: {
    query: "page('photography').children",
    select: {
      title: "page.title",
      images: {
        query: "page.images",
        select: {
          filename: true,
        },
      },
    },
  },
  headers,
});
```

<details>
<summary>🆗 Response</summary>

```js
{
  code: 200,
  result: {
    data: [
      {
        title: "Trees",
        images: {
          {
            filename: "cheesy-autumn.jpg"
          },
          {
            filename: "last-tree-standing.jpg"
          },
          {
            filename: "monster-trees-in-the-fog.jpg"
          },
          {
            filename: "sharewood-forest.jpg"
          },
          {
            filename: "stay-in-the-car.jpg"
          }
        }
      },
      { ... },
      { ... },
      ...
    ],
    pagination: { ... }
  },
  status: "ok"
}
```

</details>

### Pagination

Whenever you query a collection (pages, files, users, roles, languages) you can limit the resultset and also paginate through entries. You've probably already seen the pagination object in the results above. It is included in all results for collections, even if you didn't specify any pagination settings.

#### `limit`

You can specify a custom limit with the limit option. The default limit for collections is 100 entries.

```js
const response = await $fetch(api, {
  method: "post",
  body: {
    query: "page('notes').children",
    pagination: {
      limit: 5,
    },
    select: {
      title: "page.title",
    },
  },
  headers,
});
```

<details>
<summary>🆗 Response</summary>

```js
{
  code: 200,
  result: {
    data: [
      {
        title: "Across the ocean"
      },
      {
        title: "A night in the forest"
      },
      {
        title: "In the jungle of Sumatra"
      },
      {
        title: "Through the desert"
      },
      {
        title: "Himalaya and back"
      }
    ],
    pagination: {
      page: 1,
      pages: 2,
      offset: 0,
      limit: 5,
      total: 7
    }
  },
  status: "ok"
}
```

</details>

#### `page`

You can jump to any page in the resultset with the `page` option.

```js
const response = await $fetch(api, {
  method: "post",
  body: {
    query: "page('notes').children",
    pagination: {
      page: 2,
      limit: 5,
    },
    select: {
      title: "page.title",
    },
  },
  headers,
});
```

<details>
<summary>🆗 Response</summary>

```js
{
  code: 200,
  result: {
    data: [
      {
        title: "Chasing waterfalls"
      },
      {
        title: "Exploring the universe"
      }
    ],
    pagination: {
      page: 2,
      pages: 2,
      offset: 5,
      limit: 5,
      total: 7
    }
  },
  status: "ok"
}
```

</details>

### Pagination in Subqueries

Pagination settings also work for subqueries.

```js
const response = await $fetch(api, {
  method: "post",
  body: {
    query: "page('photography').children",
    select: {
      title: "page.title",
      images: {
        query: "page.images",
        pagination: {
          page: 2,
          limit: 5,
        },
        select: {
          filename: true,
        },
      },
    },
  },
  headers,
});
```

### Multiple Queries in a Single Call

With the power of selects and subqueries you can basically query the entire site in a single request

```js
const response = await $fetch(api, {
  method: "post",
  body: {
    query: "site",
    select: {
      title: "site.title",
      url: "site.url",
      notes: {
        query: "page('notes').children.listed",
        select: {
          title: true,
          url: true,
          date: "page.date.toDate('d.m.Y')",
          text: "page.text.kirbytext",
        },
      },
      photography: {
        query: "page('photography').children.listed",
        select: {
          title: true,
          images: {
            query: "page.images",
            select: {
              url: true,
              alt: true,
              caption: "file.caption.kirbytext",
            },
          },
        },
      },
      about: {
        text: "page.text.kirbytext",
      },
    },
  },
  headers,
});
```

### Allowing Methods

KQL is very strict with allowed methods by default. Custom page methods, file methods or model methods are not allowed to make sure you don't miss an important security issue by accident. You can allow additional methods though.

#### Allow List

The most straight forward way is to define allowed methods in your config.

```php
return [
  'kql' => [
    'methods' => [
      'allowed' => [
        'MyCustomPage::cover'
      ]
    ]
  ]
];
```

#### DocBlock Comment

You can also add a comment to your methods' doc blocks to allow them:

```php
class MyCustomPage extends Page
{
  /**
   * @kql-allowed
   */
  public function cover()
  {
    return $this->images()->findBy('name', 'cover') ?? $this->image();
  }
}
```

This works for model methods as well as for custom page methods, file methods or other methods defined in plugins.

```php
Kirby::plugin('your-name/your-plugin', [
  'pageMethods' => [
    /**
     * @kql-allowed
     */
    'cover' => function () {
      return $this->images()->findBy('name', 'cover') ?? $this->image();
    }
  ]
]);
```

### Blocking Methods

You can block individual class methods that would normally be accessible by listing them in your config:

```php
return [
  'kql' => [
    'methods' => [
      'blocked' => [
        'Kirby\Cms\Page::url'
      ]
    ]
  ]
];
```

### Blocking Classes

Sometimes you might want to reduce access to various parts of the system. This can be done by blocking individual methods (see above) or by blocking entire classes.

```php
return [
  'kql' => [
    'classes' => [
      'blocked' => [
        'Kirby\Cms\User'
      ]
    ]
  ]
];
```

Now, access to any user is blocked.

### Custom Classes and Interceptors

If you want to add support for a custom class or a class in Kirby's source that is not supported yet, you can list your own interceptors in your config

```php
return [
  'kql' => [
    'interceptors' => [
      'Kirby\Cms\System' => 'SystemInterceptor'
    ]
  ]
];
```

You can put the class for such a custom interceptor in a plugin for example.

```php
class SystemInterceptor extends Kirby\Kql\Interceptors\Interceptor
{
  public const CLASS_ALIAS = 'system';

  protected $toArray = [
    'isInstallable',
  ];

  public function allowedMethods(): array
  {
    return [
      'isInstallable',
    ];
  }
}
```

Interceptor classes are pretty straight forward. With the `CLASS_ALIAS` you can give objects with that class a short name for KQL queries. The `$toArray` property lists all methods that should be rendered if you don't run a subquery. I.e. in this case `kirby.system` would render an array with the `isInstallable` value.

The `allowedMethods` method must return an array of all methods that can be access for this object. In addition to that you can also create your own custom methods in an interceptor that will then become available in KQL.

```php
class SystemInterceptor extends Kirby\Kql\Interceptors\Interceptor
{
  ...

  public function isReady()
  {
    return 'yes it is!';
  }
}
```

This custom method can now be used with `kirby.system.isReady` in KQL and will return `yes it is!`

### Unintercepted Classes

If you want to fully allow access to an entire class without putting an interceptor in between, you can add the class to the allow list in your config:

```php
return [
  'kql' => [
    'classes' => [
      'allow' => [
        'Kirby\Cms\System'
      ]
    ]
  ]
];
```

This will introduce full access to all public class methods. This can be very risky though and you should avoid this if possible.

### No Mutations

KQL only offers access to data in your site. It does not support any mutations. All destructive methods are blocked and cannot be accessed in queries.

## Plugins

- [nuxt-kql](https://nuxt-kql.jhnn.dev): A Nuxt 3 module for KQL.

## License

[MIT](./LICENSE) License © 2020-2022 [Bastian Allgeier](https://getkirby.com)
