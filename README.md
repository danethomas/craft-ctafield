# CTA field plugin for Craft

## Requirements

This plugin requires Craft CMS 3.0.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require danethomas/craft-cta-field

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Link Field.

## Templating

Link fields on your models will return an instance of `cta\models\Link`. Rendering a link
field directly within a template will return the url the field is pointing to.

```
<a href="{{ item.myLinkField }}">Link</a>
```

You can use the following accessors to get the different properties of the link:

```
{{ item.myLinkField.getElement() }}
{{ item.myLinkField.getTarget() }}
{{ item.myLinkField.getText() }}
{{ item.myLinkField.getUrl() }}
{{ item.myLinkField.hasElement() }}
{{ item.myLinkField.isEmpty() }}
```

Use the `getLink` utility function to render a full html link:

```
{{ item.myLinkField.getLink() }}
```

You can pass the desired content of the link as a string, e.g.
```
{{ entry.linkField.getLink('Imprint') }}
```

You may also pass an array of attributes. When doing this you can override
the default attributes `href` and `target`. The special attribute `text`
will be used as the link content.
```
{{ entry.linkField.getLink({
  class: 'my-link-class',
  target: '_blank',
  text: 'Imprint',
}) }}
```

To get the CTA as a span (eg to use in clickable blocks), you can use the `getSpan()` function.
```
{{ entry.linkField.getSpan({
  class: 'my-link-class',
  text: 'Imprint',
}) }}
```

## Configuration
### Custom classes
Add custum classes to the dropdown menu.

1. Add cta.php to /config
2. Declare your classes:
```
<?php

return [
    'classes' => [
        'btn'                   => 'Primary',
        'btn btn--secondary'    => 'Secondary'
    ]
];
```

### Linking between sites
By default, linking to entries from another site is not enabled. To make this work, you can use this config setting:

```
<?php

return [
    'crossSiteLinking' => true
];
```

## Credits

Heavily inspired by [sebastian-lenz/craft-linkfield](https://github.com/sebastian-lenz/craft-linkfield/blob/master/README.md).
