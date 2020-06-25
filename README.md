# Kirby QL

Kirby's Query Language API combines the flexibility of Kirby's data structures, the power of GraphQL and the simplicity of REST.

The Kirby QL API takes POST requests with standard JSON objects and returns highly customized results that fit your application.

## Alpha

**This plugin is still in an experimental state. Please don't use it in production yet.**

## Example

Query:

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

Result:

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

## Credits

[Bastian Allgeier](https://getkirby.com)

## License

<http://www.opensource.org/licenses/mit-license.php>
