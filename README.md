[![Latest Stable Version](https://poser.pugx.org/onedrop/form-hubspot/v/stable)](https://packagist.org/packages/onedrop/form-hubspot)
[![License](https://poser.pugx.org/onedrop/form-hubspot/license)](LICENSE)

# Hubspot forms for Neos

This packaged adds a new NodeType Hubspot Form, where editors can select a Hubspot Form in the inspector and it will be rendered inside Fusion.

**Attention: Currently OneDrop.AjaxForm requires all Neos.NodeTypes and they will be included automatically. If you wish to not allow editors to use them you need to set those to `abstract:true`.**

## Installation

Onedrop.Form.Hubspot is available via packagist. `"onedrop/form-hubspot" : "~1.0"` to the require section of the composer.json
or run:

```bash
composer require onedrop/form-hubspot
```

We use semantic-versioning so every breaking change will increase the major-version number.

## Configuration

Set your own Hubsport portal id and api key.

```
Onedrop:
  Form:
    Hubspot:
      api:
        hapikey: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
        portalId: xxxxxxx
```

## License

Licensed under MIT, see [LICENSE](LICENSE)

## Contribution

We will gladly accept contributions. Please send us pull requests.
