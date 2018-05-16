# Color Field - Drupal 8

## ABOUT & FEATURES

### Formatters

  Plain text HEX code (#FFFFFF)
  Css Declaration (color/background-color)

### Widgets

  Plain Text
  Pre-selected Color Boxes
  Simple Query Color
  (http://recursive-design.com/projects/jquery-simple-color/)

## ROAD MAP

1) Make this module a base that could be used by any color picker.
2) include http://www.eyecon.ro/colorpicker/
3) include http://www.dematte.at/colorPicker/
4) include http://acko.net/blog/farbtastic-jquery-color-picker-plug-in/

## INSTALLATION

Install as you would normally install a contributed Drupal module. See also
[Core Documentation](https://www.drupal.org/docs/8/extending-drupal-8/installing-modules)

### DEPENDENCIES
There are JavaScript libraries required for a couple of the field widgets. If
you are not actively using those field widgets, you can skip their installation
if desired. If you installed color field via
[Composer](https://getcomposer.org),the packages will have been suggested but
not automatically installed. To install them you will need to add
[Asset Packagist](https://asset-packagist.org) to your composer.json and run
```bash
composer require bower-asset/jquery-simple-color bower-asset/spectrum
``` 

If you are not using Composer, you will need to manually install them.

- [jQuery Simple Color](https://github.com/recurser/jquery-simple-color)
copy to `/libraries/jquery-simple-color` so that `jquery.simple-color.min.js`
is in that folder. Required for the Color Grid widget.
- [Spectrum](https://github.com/bgrins/spectrum) copy to `/libraries/spectrum`
so that `spectrum.js` exists in that folder. Required for the Spectrum widget.

## USAGE

Field
1. Add the field to an node/entity
2. Select the 'Color Field' field type
3. Select the 'Color' widget you want

## CREDIT

Original Creator: [targoo](https://www.drupal.org/u/targoo).

Maintainers:
  - [targoo](https://www.drupal.org/u/targoo)
  - [Nick Wilde](https://www.drupal.org/u/nickwilde)

Original development sponsored by Marique Calcus and written by Calcus David.
For professional support and development services contact targoo@gmail.com.

## More info

http://www.w3.org/TR/css3-color/#color
https://github.com/mikeemoo/ColorJizz-PHP
http://www.colorhexa.com/ff0000
https://github.com/PrimalPHP/Color/blob/master/lib/Primal/Color/Parser.php
https://github.com/matthewbaggett/php-color/blob/master/Color.php
