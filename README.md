[![](https://img.shields.io/packagist/v/inspiredminds/contao-crawler-authenticator.svg)](https://packagist.org/packages/inspiredminds/contao-crawler-authenticator)
[![](https://img.shields.io/packagist/dt/inspiredminds/contao-crawler-authenticator.svg)](https://packagist.org/packages/inspiredminds/contao-crawler-authenticator)

Contao Crawler Authenticator
============================

Allows the Contao Crawler to be logged in as a front end member via Basic Authentication in Contao **4.13** in order to index protected pages.

## Usage

1. Install the extension (zero configuration necessary in the Contao Managed Edition).
2. Create a front end member specifically for the Contao Crawler.
3. Assign the new member to the appropriate member groups.
4. Now choose one of the following options (replace `<username>` and `<password>` with the member's credentials):
    * Pass the member's username and password for every `contao:crawl` execution:
        ```
        CRAWLER_AUTH=<username>:<password> vendor/bin/contao-console contao:crawl --subscribers=search-index --max-depth=3
        ```
    * Define the member's username and password in the `.env.local`:
        ```ini
        # .env.local
        CRAWLER_AUTH=<username>:<password>
        ```
        These will then get used automatically every time you execute `contao:crawl` (unless overridden by the former command).
