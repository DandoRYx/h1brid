# H1brid

H1brid is a PHP web development framework. Is it MVC? No, it is a **hybrid**.

## Features

It does a lot of things, but still lets you have control.

- Routing
- API integration
- Multi language
- Font loading
- SEO optimalization (sitemap, breadcrumbs, ...)
- Security measures

## Installation

Just download and edit values in `config.php`.

### Deployment

Deploying your project is easy! Remove `.localhost` from `/app/` and deploy.

## Usage
### Creating a view

If you want to create a view, create a file with the view name in `/app/mvc/views/`.
Then in `/app/languages/titles/` and `/app/languages/descriptions/` add title and description of a view. The page should be working then.

For custom titles and descriptions (for example in use of products, items, ...) you can use methods in the controller of the view  `Page::setCustomTitle($title)` and `Page::setCustomDescription($description)`.

#### Components
If you have a component that needs to be added to page multiple times (for example listing of Books), you can use components.

To create a component, all you have to do is create file in `/app/mvc/views/components/` and optionally (for example to load some data from DB) you can create controller for the component in `/app/mvc/controllers/components/`.

Then you can access the component in view by using `Page::getComponent($component, $arguments)`.

`Loading book names in home view`
```php
<div class="books">
    <h1>List of books</h1>
    <?php

    for($i = 0; $i < 5; $i++) {
        Page::getComponent('book', [$i]);
    }
    
    ?>
</div>
```

`Book component controller`
```php
<?php

$name;

// $arguments[0] being the ID
if(isset($arguments[0])) {
    // Load from DB as covered later
    $name = $result;
}
```

`Book component view`
```php
<div class="book-component">
    <h2><?= $name ?></h2>
</div>
```

### Controller

A view does not need to have a controller. If you need one, the only thing you have to do is create file with view name in `/app/mvc/controllers/`.

### Models

Models get automatically loaded from `/app/mvc/models/`. They should be used in controllers or in data.

### Languages

If you have multiple languages on your site, you need to change `MULTI_LANGUAGE` to `true` in `/config.php`. Then you need to create language files in `/app/languages/`. For example for English the files would be `/app/languages/en.php` `/app/languages/titles/en.php` `/app/languages/descriptions/en.php`. For making relative `<a>` links inside page you can use `Language::link('path')`.

For getting language words you can use `Language::word($key)` where `$key` is index specified in language files.

Every language will have its own URL. For english page that would be `example.com/en/`. If you need to know user's language for any reason, you can use `Language::get()`.

### Database

To connect to database you need to change login information in `/config.php`. Then you can use the database with `Database::get()`.

#### Example for SELECT

Example for `SELECT` from table named `test` and using prepared statement.  

```php
// Query and execution
$stmt = Database::get()->prepare('SELECT * FROM test WHERE test_name = ?');
if(!$stmt->execute(['name'])) {
    die('fail');
}

// Printing the output data
while($row = $stmt->fetch()) {
    var_dump($row);
}
```

### SEO

#### Sitemap

Sitemap content is dynamically generated. For adding sites to sitemap you can add them in `/app/sitemap.php`. Be aware of the difference in syntax when using multiple languages on your page. In `/app/sitemap.php` you can also write PHP code which allows you to for example generate sites to sitemap automatically from database.

#### Breadcrumbs

Breadcrumbs are being processed in controller. For adding a breadcrumb, use `Breadcrumb::add($position, $name, $url)`. `$position` should be an integer that represents the position of breadcrumb in hierarchy. Breadcrumbs are then automatically loaded into `<head>` for search engines to use them.

You need to also show the breadcrumbs on page. Do that using `<ol>` and `<li>` tags. More information on how to use the breadcrumbs is in Google Developers documentation.

`Inside controller of Award Winners view` 
```php
Breadcrumb::add(1, 'Books', Language::link('books'));
Breadcrumb::add(2, 'Science Fiction', Language::link('sciencefiction'));
Breadcrumb::add(3, 'Award Winners', '');
```

### API

For creating an API route, add the API into the `/app/api.php`. To process the API calls, you should create an api file (let's name the api Example) in `/app/Example.php`. In the file should be `class Example` with a public method called `action()` (action being your api call name). The file and class should be in `namespace Api` to separating regular classes from API classes.

To call this API the only thing you have to do is to call URL `example.com/api/example/action/....` where `....` are additional parameters you can handle.

There is private key handling where you have to send the key in `POST` request with name `X-API-KEY`.
All API variables can be accessed by `\Api::getKey()` and `\Api::getParameters()`.

If you want to see how the API file looks like, there is an example file `Handler.php` in `/app/api/`.

### Forms

To process forms, you should use `<form action="/data/yourfile" method="POST">`. Only thing you need to do for processing the form is to create file `yourfile.php` in `/app/data/`.

For security reasons you should use `Utils::generateCSRF()` inside form and in data file you should use `Utils::parseCSRF()`. That way you and your users are protected from CSRF attacks.

`Form file`
```php
<form action="/data/yourfile" method="POST">
    <input name="name" type="text" />
    <?php Utils::generateCSRF() ?>
    <input name="submit" value="Submit" type="submit" />
</form>
```

`Processing file in /app/data/`
```php
<?php

// CSRF check
if(!Utils::parseCSRF()) {
    exit;
}

// Proceed...
```

### Resources

Resources have their place in `/public/`. All resources get properly loaded when accessed. The original `style.css`, `clear.css` and `script.js` are being loaded from start.

For accessing a resource use URL `example.com/resource/folder/filename`.

### Font loading

If you have downloaded a static font file, you can put it in `/public/fonts/` with name `Example_style_weight.extension` that would be `Roboto_normal_400.ttf` for Roboto. The font file gets automatically loaded into the page. You can load as many fonts as you want.

### Sending emails

You can send emails using `Email::send`. There you can also add attachment paths so files can be sent with the email.

## License

[MIT](https://choosealicense.com/licenses/mit/)
