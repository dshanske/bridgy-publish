# Bridgy Publish #
**Contributors:** dshanske  
**Tags:** indieweb, POSSE, bridgy  
**Stable tag:** 1.1.0  
**Requires at least:** 4.0  
**Tested up to:** 4.6.2  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

Bridgy Publish Support for your Site

## Description ##

People often post something on their web site, then post a copy elsewhere so that other people will see it. The IndieWeb community calls this POSSE.

[Bridgy Publish](https://www.brid.gy/about#publishing) adds a hidden link to your post, and sends a
webmention to Bridgy, which will create a post on the appropriate site. It also stores the link for
display by [Syndication Links](https://wordpress.org/plugins/syndication-links/). 

The plugin does nothing on its own and requires the [webmention](https://wordpress.org/plugins/webmention/) plugin and will not
work without it. Development/Issues are done on [Github](https://github.com/dshanske/bridgy-publish).

## Changelog ##

### Version 1.1.0 ###
	* Remove Instagram setting as deprecated
	* Add Flickr setting as now supported
	* Change webmention support notice to activate on send_webmention per request
	* Comply with WordPress Standards

### Version 1.0.1 ###
	* Bug Fixes
	* If Indieweb Plugin is installed, move settings under that menu
	* If WP_DEBUG is set - send Publish entries to the error log

### Version 1.0.0 ###
	* Initial release
	* Takes over Syndication Links storage of Bridgy Publish links

