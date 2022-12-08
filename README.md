[![Stand With Ukraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner2-direct.svg)](https://vshymanskyy.github.io/StandWithUkraine/)

# Laravel Nova Single Resource

Create resources for "single" resources (key-value database structure), such as a settings table.

![index](https://raw.githubusercontent.com/Muetze42/asset-repo/main/nova-single-resource/images/index.png)
![detail](https://raw.githubusercontent.com/Muetze42/asset-repo/main/nova-single-resource/images/detail.png)

## Install

`composer require norman-huth/nova-single-resource`

## Usage

The following description refers to the Model Settings as an example....

You can create a resource with `php artisan nova:single-resource Setting`.  
The table still requires a primary ID and this package is designed to allow the Value column to be nullable.

```php
class Setting extends Resource
{
    use ResourceTrait;  // required

    public function __construct($resource = null)
    {
        $this->bootResourceTrait(); // Required
        parent::__construct($resource);
    }

    protected static function sections(): array
    {
        return [
            'general-settings' => 
            [
                'name'   => __('General Settings'),
                'icon'   => 'eye',
            ],
            'system' => 
            [
                'name'   => __('System'),
                'faIcon' => 'fa-brands fa-laravel fa-fw',
            ],
        ];
    }

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = \App\Models\Setting::class;

    public function getGeneralSettingsFields(NovaRequest $request): array
    {
        return [
            Currency::make('Price')->currency('EUR'),
            Text::make(__('Text'), 'text'),
            Boolean::make(__('Boolean'), 'boolean'),
        ];
    }

    public function getSystemFields(NovaRequest $request): array
    {
        return [
            Date::make(__('Date'), 'Date'),
            DateTime::make(__('DateTime'), 'DateTime'),
        ];
    }
}
```

#### Define the sections in `sections()`

```php
'general-settings' => // Required: unique slug
[
    'name'   => 'My Settings' // Required: Display section name
    'icon'   => 'eye', // Optional: Heroicon icon https://heroicons.com/
    'faIcon' => 'fa-brands fa-laravel fa-fw', // Optional: FontAwesome Icon (not included!) https://fontawesome.com/
],
```

And add for every section fields.  
Format: `get'.Str::studly($slug).'Fields`: `getGeneralSettingsFields(NovaRequest $request)`

### Columns

By default, the columns key and value are used in the database.

If you want to use others. You must specify them in the model:

```php
class Setting extends Model
{
    public static string $keyColumn = 'key';
    public static string $valueColumn = 'value';
```

### Change cast of a field

```php
Text::make(__('SMTP Password'), 'smtp_password')
    ->cast('encrypted'),
```

### Single Resource Fields

In this resource must be used adjusted fields.

The following fields are already included:

#### Nova

| Original                                                                                   | Single Resource                               |
|--------------------------------------------------------------------------------------------|-----------------------------------------------|
| [Boolean](https://nova.laravel.com/docs/4.0/resources/fields.html#boolean-field)           | NormanHuth\SingleResource\Fields\Boolean      |
| [BooleanGroup](https://nova.laravel.com/docs/4.0/resources/fields.html#booleangroup-field) | NormanHuth\SingleResource\Fields\BooleanGroup |
| [Color](https://nova.laravel.com/docs/4.0/resources/fields.html#color-field)               | NormanHuth\SingleResource\Fields\Color        |
| [Country](https://nova.laravel.com/docs/4.0/resources/fields.html#country-field)           | NormanHuth\SingleResource\Fields\Country      |
| [Currency](https://nova.laravel.com/docs/4.0/resources/fields.html#currency-field)         | NormanHuth\SingleResource\Fields\Currency     |
| [Date](https://nova.laravel.com/docs/4.0/resources/fields.html#date-field)                 | NormanHuth\SingleResource\Fields\Date         |
| [DateTime](https://nova.laravel.com/docs/4.0/resources/fields.html#datetime-field)         | NormanHuth\SingleResource\Fields\DateTime     |
| [KeyValue](https://nova.laravel.com/docs/4.0/resources/fields.html#keyvalue-field)         | NormanHuth\SingleResource\Fields\KeyValue     |
| [Markdown](https://nova.laravel.com/docs/4.0/resources/fields.html#markdown-field)         | NormanHuth\SingleResource\Fields\Markdown     |
| [MultiSelect](https://nova.laravel.com/docs/4.0/resources/fields.html#multiselect-field)   | NormanHuth\SingleResource\Fields\MultiSelect  |
| [Number](https://nova.laravel.com/docs/4.0/resources/fields.html#number-field)             | NormanHuth\SingleResource\Fields\Number       |
| [Select](https://nova.laravel.com/docs/4.0/resources/fields.html#select-field)             | NormanHuth\SingleResource\Fields\Select       |
| [Text](https://nova.laravel.com/docs/4.0/resources/fields.html#text-field)                 | NormanHuth\SingleResource\Fields\Text         |
| [Textarea](https://nova.laravel.com/docs/4.0/resources/fields.html#textarea-field)         | NormanHuth\SingleResource\Fields\Textarea     |
| [Timezone](https://nova.laravel.com/docs/4.0/resources/fields.html#timezone-field)         | NormanHuth\SingleResource\Fields\Timezone     |
| [Trix](https://nova.laravel.com/docs/4.0/resources/fields.html#trix-field)                 | NormanHuth\SingleResource\Fields\Trix         |

#### File Fields

| Original                                                                          | Single Resource                         |
|-----------------------------------------------------------------------------------|-----------------------------------------|
| [File](https://nova.laravel.com/docs/4.0/resources/fields.html#boolean-field)     | NormanHuth\SingleResource\Fields\File   |
| [Image](https://nova.laravel.com/docs/4.0/resources/file-fields.html#images)      | NormanHuth\SingleResource\Fields\Image  |
| [Avatar](https://nova.laravel.com/docs/4.0/resources/file-fields.html#avatars)    | NormanHuth\SingleResource\Fields\Avatar |

#### Package Fields (alphabetic)

| Package                                                                                    | Single Resource                                                                                      |
|--------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------|
| [flatroy/nova-progressbar-field](https://github.com/Flatroy/nova-progressbar-field)        | NormanHuth\SingleResource\Fields\Flatroy\FieldProgressbar                                            |
| [murdercode/seo-title](https://github.com/murdercode/Nova4-SeoTitle)                       | NormanHuth\SingleResource\Fields\Murdercode\SeoTitle                                                 |
| [murdercode/nova4-seo-description](https://github.com/murdercode/Nova4-SeoTitle)           | NormanHuth\SingleResource\Fields\Murdercode\SeoDescription                                           |
| [murdercode/nova4-tinymce-editor](https://github.com/murdercode/Nova4-TinymceEditor)       | NormanHuth\SingleResource\Fields\Murdercode\TinymceEditor                                            |
| [norman-huth/nova-bbcode-textarea](https://github.com/Muetze42/nova-bbcode-textarea)       | NormanHuth\SingleResource\Fields\NormanHuth\BBCode<br>NormanHuth\SingleResource\Fields\NormanHuth\BB |
| [norman-huth/nova-iframe-popup](https://github.com/Muetze42/norman-huth/nova-iframe-popup) | NormanHuth\SingleResource\Fields\NormanHuth\IframePopup                                              |
| [norman-huth/nova-secret-field](https://github.com/Muetze42/norman-huth/nova-secret-field) | NormanHuth\SingleResource\Fields\NormanHuth\SecretField                                              |
| [norman-huth/nova-values-field](https://github.com/Muetze42/nova-values-field)             | NormanHuth\SingleResource\Fields\NormanHuth\Values                                                   |

### Field Development Notices

* Try this [trait](src/Traits/FieldTrait.php)
* Cast with `protected string $cast`. Example [here](src/Fields/Boolean.php). See `protected function castValue` in the trait

## Todos

* Custom `ResourceUpdateController` & `Update` component to be able to use slugs in url
* [ebess/advanced-nova-media-library](https://github.com/ebess/advanced-nova-media-library)
* ???

---
[![More Laravel Nova Packages](https://raw.githubusercontent.com/Muetze42/asset-repo/main/svg/more-laravel-nova-packages.svg)](https://huth.it/nova-packages)
