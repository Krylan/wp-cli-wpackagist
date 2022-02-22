# wp-cli-wpackagist
Extension for WP CLI to generate composer.json with list of used plugins and themes from wpackagist

## Installation
If you have WP CLI already installed, use command:
```
wp package install https://github.com/Krylan/wp-cli-wpackagist.git
```

Otherwise, you need to install WP CLI first: https://wp-cli.org/

## Usage

To generate composer.json, go into root folder of your Wordpress project and use command:
```
wp wpackagist generate
```
This will check all your plugins and themes used in this Wordpress project and check if active plugins/themes are available in Wordpress repository (through its API).
All of active and existing plugins and themes will be listed in generated composer.json file.
Note, that this command will only print out generated composer.json – you would need to copy the output or its part to your composer.json by yourself (alternatively, use arguments shown below).

## Parameters
For `wp wpackagist generate` command there are following parameters:
* `--themesonly` – it will check only themes, skipping plugins
* `--pluginsonly` – it will check only plugins, skipping themes
* `--overwrite` – it will save composer.json file in root dir (it will overwrite file if exists!)

## Disclaimer
COVERED CODE IS PROVIDED UNDER THIS LICENSE ON AN "AS IS" BASIS, WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING, WITHOUT LIMITATION, WARRANTIES THAT THE COVERED CODE IS FREE OF DEFECTS, MERCHANTABLE, FIT FOR A PARTICULAR PURPOSE OR NON-INFRINGING. THE ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE COVERED CODE IS WITH YOU. SHOULD ANY COVERED CODE PROVE DEFECTIVE IN ANY RESPECT, YOU (NOT THE INITIAL DEVELOPER OR ANY OTHER CONTRIBUTOR) ASSUME THE COST OF ANY NECESSARY SERVICING, REPAIR OR CORRECTION. THIS DISCLAIMER OF WARRANTY CONSTITUTES AN ESSENTIAL PART OF THIS LICENSE. NO USE OF ANY COVERED CODE IS AUTHORIZED HEREUNDER EXCEPT UNDER THIS DISCLAIMER.
