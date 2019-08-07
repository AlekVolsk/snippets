# Snippets

![Version](https://img.shields.io/badge/version-1.0.0-28A5F5.svg?style=for-the-badge)
![Last Update](https://img.shields.io/badge/last_update-2019.08.06-28A5F5.svg?style=for-the-badge)
![Joomla](https://img.shields.io/badge/joomla-3.9+-1A3867.svg?style=for-the-badge)
![Php](https://img.shields.io/badge/php-5.6+-8892BF.svg?style=for-the-badge)

_description in Russian [here](README.ru.md)_

**An extension package that provides the use of shortcodes in the content of the site with the ability to import data through external plugins.**

## Use in site content

`{snippet [name|id]}`, where:

- **snippet** — reserved
- **name** — snippet name
- **id** — snippet ID

## Description of the main components of the extension package

The snippet component allows you to take into account snippets and their content, to perform operations on impex (import and export) of data. Each snippet consists of a name and its content. Snippet has publication status.

The snippet name must consist of lowercase Latin characters, numbers, and the underscore character `_`. If the name does not match, it will be automatically converted when the snippet is saved or when it is imported.

Snippet content supports full HTML.

The component supports snippet search by its id and/or name.

The system snippets plugin provides replacement of shortcodes with their content. Raw shortcodes are cut out of content. **Unpublished snippets not processed!**

Plugin – a button for the editor allows you to select a snippet when editing content and paste ready shortcode into the selected location.

## Data Impex

Impex data is produced through the plugins of the `snippetimpex` group. The base package includes plugins for impex with CSV and JSON formats. The component displays only published plugins. All data is exported, including unpublished.

The component has 2 settings for impex plugins to work, affecting only export.

**Save file on server** — indicates whether to store files with exported data on the server permanently, the default value is _yes_.

**Save folder** — allows you to select the folder where the data export files will be stored, by default it is the `/files` folder in the root of the site — it is created automatically when the package is installed, with standard rights 0755. If the folder is not specified, the Joomla! temporary files folder is used. If the specified folder is not found, the Joomla! temporary files folder is used. If the **Save file on server** parameter is _no_, the Joomla! temporary files folder is used, and the file is deleted after downloading the generated file.

When importing data files, they are temporarily saved in the Joomla! temporary files folder and after successful import are deleted from the server.

---

### For developers

Each plugin implements 2 events.

#### Data structure

The data sent to the plugin for export and received from the plugin for import have a complete finished form in the form of an associative arra, where the key is the name of the snippet, the key value is the content, which is a string. Nested arrays and objects in the form of content are not allowed. Matching keys are not allowed by the definition of associated arrays.

The component controller is involved in transforming the snippet name into the form necessary for the system to work, the plugin does not need to take care of this, but it is recommended.

When exporting data the plugin is engaged in escaping of content independently.

When importing data if the content contains html tags, the plugin independently decodes them if necessary.

#### Export

```php
public function onSnippetDataExport((string)$type, (string)$path, (array)$data): (object)stdClass
```

`(string)$type` — the data type selected for export matches the name of the plugin; if there is a mismatch, the plugin should not process the data.

`(string)$path` — the full path to the folder on the server where you want to write the file; if there is no path save to the Joomla! temporary files folder; does not contain the name of the file to be saved — the plugin must generate it yourself.

`(array)$data)` — an array of data intended for export.

The plugin returns the structure as a standard object, where the following fields should have the following values:

- `(bool)$result` – plugin success indicator
- `(string)$type` — the type of data the plugin works with always matches the name of the plugin
- `(string)$message` — message about the operation of the plugin, if empty, means that the plugin did not perform any actions to export data
- `(string)$file` — full path to the resulting file, may be missing if the plugin fails

#### Import

```php
public function onSnippetDataImport((string)$file): (object)stdClass
```

`(string)$file` — the full path to the file on the server, where you need to read the data for further processing.

The plugin returns the structure as a standard object, where the following fields should have the following values:

- `(bool)$result` — plugin success indicator
- `(string)$type` — the type of data the plugin works with always matches the name of the plugin
- `(string)$message` — message about the operation of the plugin, if empty, means that the plugin did not perform any actions to export data
- `(array)$data` — data array for subsequent import (described above), may be absent if the plugin fails

#### The plugin does not undertake the work of reading snippet data from the database or writing snippet data to the database — the component controller does this work. All that the plugin does is read and write files, send and receive an array of data.
