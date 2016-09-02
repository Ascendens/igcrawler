# Crawler for calculating amount of "img" tags on site

Sample crawler designed to calculate amount of "img" tags on whole site. Created for testing purposes only.

## Requirements

* PHP >= 5.5
* [Composer](https://getcomposer.org/)

## Installation

Clone repository:

```bash
git clone https://github.com/Ascendens/igcrawler.git igcrawler
```

Go to the project directory and run composer installation:

```bash
cd igcrawler
composer install
```

## Usage

There are predefined entry point "index.php" designed to be used from console:

```bash
php index.php http://www.example.com [full_path_to_report_file]
```

Absolute domain URL should be used as argument. Crawling would be produced on domain, so no path is needed.

Result of execution will be HTML table saved to file, given as second CLI parameter. By default, filename is 
report_d.m.Y.html and it will be located in directory where script was executed.

## License

The code is distributed under the terms of the MIT license (see [LICENSE](https://github.com/Ascendens/igcrawler/blob/master/LICENSE)).