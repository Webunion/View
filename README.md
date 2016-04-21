# View
Simple and Lightweight Tempate Class.

## Examples:
Note that the `defaul.php` layout and page should exist, because the default layout and page are loaded automatically, but you can set it as a `__construct` parameter or using the `loadLayout` and `loadPage` methods. The default file extension is `.php`.

```
Folder Structure:
path
└───views
	├───layouts		
    │   default.php
    │   empty.php
	│   ...
    │
    ├───pages
    │   │   default.php
    │   │   home.php
    │   │   ...
    │   └───partials
	│		partial.php
	│		...
```
```html
/path/views/layouts/default.php
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{#PAGE_TITLE#}</title>
        <link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <h1>Hello, world!</h1>
    <?php echo $appPage;?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://getbootstrap.com/dist/js/bootstrap.min.js"></script>
  </body>
</html>
```

```php
/path/views/pages/home.php
    <?php echo $someVar;?>
    <ul>
    <? foreach($last AS $v):?>
        <li><?= $v;?></li>
    <?php endforeach;?>
    </ul>
```
```php
/path/views/pages/partials/partial.php
    <aside>
    <?php echo $someVar;?>
    </aside>>
```

``` php
controller.php
use Webunion\View;
//The default layout and page are loaded automatically, but you can pass it as a parameter or using LoadLayout and LoadPage methods
$view = new View('path/views/');
$view->addFixData('PAGE_TITLE', 'Some Title Page');
$view->addData('someVar', 'Some Value');
$view->addData('anotherVar', array('a', 'b', 'c'));
$view->loadPartial('partialName', 'partials/partial');

echo $view->render('home', array('last'=>'Last value'));
```
