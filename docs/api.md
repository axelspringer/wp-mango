FORMAT: 1A

# WordPress Mango Plugin
The WordPress Mango Plugin is an companion plugin to [Mango](https://github.com/axelspringer/mango). It adds multiple REST endpoints which are thus not available in the current [WP REST V2](https://developer.wordpress.org/rest-api/) specification.

## Authentication
Currently the WordPress Mango Plugin does not supports and authentication or authorization.

## Error States
The common [HTTP Response Status Codes](https://github.com/for-GET/know-your-http-well/blob/master/status-codes.md) are used.


# WordPress Mango Root [/mango/v1]
WordPress Mango Plugin REST entrypoint.

# Group Nav

## Locations [/nav/locations]
Exposes the available nav menu locations.

### List all nav locations [GET]
A single object containing all the available nav locations of the enabled WordPress theme.

+ Response 200 (application/json)
    {
        "top": 2
    }

## Nav menu [/nav/menus/{id}]
Exposes the information about nav menus.

### Get nav menu [GET]
A single object containing all the information about a nav menu.

+ Parameters
    + id: 1 (required, int) - Id of the nav menu to get.

+ Response 200 (application/json)
    {
        "term_id": 2,
        "name": "Test",
        "slug": "test",
        "term_group": 0,
        "term_taxonomy_id": 2,
        "taxonomy": "nav_menu",
        "description": "",
        "parent": 0,
        "count": 1,
        "filter": "raw"
    }

## Nav items [/nav/items/{id}]
Exposes the associated nav items of a nav menu.

### Get nav items [GET]
An array containing all the associated nav items of a nav menu.

+ Parameters
    + id: 1 (required, int) - Id of the nav menu to get.

+ Response 200 (application/json)
    [
        {
            "ID": 4,
            "post_author": "1",
            "post_date": "2018-01-02 14:23:10",
            "post_date_gmt": "2018-01-02 13:23:10",
            "post_content": " ",
            "post_title": "",
            "post_excerpt": "",
            "post_status": "publish",
            "comment_status": "closed",
            "ping_status": "closed",
            "post_password": "",
            "post_name": "4",
            "to_ping": "",
            "pinged": "",
            "post_modified": "2018-01-02 14:23:57",
            "post_modified_gmt": "2018-01-02 13:23:57",
            "post_content_filtered": "",
            "post_parent": 0,
            "guid": "http://localhost:8080/?p=4",
            "menu_order": 1,
            "post_type": "nav_menu_item",
            "post_mime_type": "",
            "comment_count": "0",
            "filter": "raw",
            "db_id": 4,
            "menu_item_parent": "0",
            "object_id": "1",
            "object": "post",
            "type": "post_type",
            "type_label": "Beitrag",
            "url": "http://localhost:8080/2018/01/02/hallo-welt/",
            "title": "Hallo Welt!",
            "target": "",
            "attr_title": "",
            "description": "",
            "classes": [
                ""
            ],
            "xfn": ""
        }
    ] 

