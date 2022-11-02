# Changelog
All notable changes to this project will be documented in this file.

## [Unreleased]

## [1.10.0] 2020-09-24
* Enabled PHP `7.4` support in `composer.json`

## [1.9.0] 2020-09-07
* Enabled Magento `2.4` support in `composer.json`

## [1.8.0] 2020-08-28
* Added license file.

## [1.7.0] 2020-05-11
* Fixed: blocking store emulation if no `store_id` parameter is passed through search criteria filters.

## [1.6.0] 2019-10-25
* Added compatibility with PHP 7.3.x in composer file (thanks to @WJdeBaas)

## [1.5.0] 2019-01-21
* Added compatibility with PHP 7.2.x in composer file

## [1.4.0] 2018-12-31
* Added compatibility with Magento 2.3.0 in composer file

## [1.3.1] 2018-11-20
* Fixed missing page and block id in search endpoint responses

## [1.3.0] 2018-10-15
* If searchCriteria contains only one store_id as filter, the content for blocks/pages will be loaded for the given store id. 

## [1.2.0] 2018-10-03
* Widgets and content variables are parsed correctly
* CMS Block/Pages List with store_id provided

## [1.1.0] 2018-07-23
* Added new API endpoints to get pages/blocks by their identifier

## [1.0.0] 2018-06-25
* Init files
