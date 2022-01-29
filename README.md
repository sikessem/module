# Library Manager for PHP
Organizer facilitates the import/export of PHP modules from a root directory.

## Installation
Use Composer to install the library with this command:
`composer require organizer/organizer`
Or [click here to download it directly in ZIP format ](https://github.com/SIKessEm/Organizer/archive/refs/heads/main.zip)

## Usage
```php
<?php
$root =  __DIR__;
$organizer_root = "$root/vendor/organizer/organizer";
$organizer = require_once "$organizer_root/bootstrap.php";

$organizer->organize($root);  // You can use the function organize() if it not exist in your project

// Search and require my.module or my.module.php or my/module or my/module.php from $root and $organizer_root
$organizer->import('my.module'); // You can use the function import() if it not exist in your project
```

## Requirements
PHP 8 or above (at least 8.0 recommended to avoid potential bugs)

## Author
[Website](https://about.sikessem.com) | [E-mail](mailto:developer@sikessem.com) | [LinkedIn](https://linkedin.com/in/SIKessEm) | [GitLab](https://gitlab.com/SIKessEm) | [GitHub](https://github.com/SIKessEm) | [npm](https://npmjs.org/~sikessem) | [Composer - Packagist](https://packagist.org/users/SIKessEm) | [Twitter](https://twitter.com/Ske_SIKessEm)

## Security Reports
Please send any sensitive issue to [report@sikessem.com](mailto:report@sikessem.com). Thanks!

## License
Organizer is licensed under the Apache 2.0 License - see the [LICENSE](./LICENSE) file for details.

## Contribution
For any contribution, please follow these steps:
1. Clone the repository with `git clone https://github.com/SIKessEm/Organizer` or `git remote add origin https://github.com/SIKessEm/Organizer` then `git branch -M main`
2. Create a new branch. Example: `git checkout -b my_contribution`
3. Make your changes and send them with `git push -u origin main`
You will be informed of the rest.
