=== Search & Filter Pro ===
Contributors: DesignsAndCode
Donate link: 
Tags: posts, custom posts, products, category, filter, taxonomy, post meta, custom fields, search, wordpress, post type, post date, author
Requires at least: 3.5
Tested up to: 3.9.1
Stable tag: 1.1.8

Search and Filtering for posts, products and custom posts. Allow your users to Search & Filter by taxonomies, custom fields and more.

== Description ==

Search & Filter Pro is a advanced search and filtering plugin for WordPress.  It allows you to Search & Filter your posts / custom posts / products by any number of parameters allowing your users to easily find what they are looking for on your site, whether it be a blog post, a product in an online shop and much more.

Users can filter by Categories, Tags, Taxonomies, Custom Fields, Post Meta, Post Dates, Post Types and Authors, or any combination of these easily.

Great for searching in your online shop, tested with: WooCommerce and WP eCommerce, Easy Digital Downloads


= Field types include: =

* dropdown selects
* checkboxes
* radio buttons
* multi selects
* range slider
* number range
* date picker
* single or multiselect comboboxes with autocomplete


== Installation ==


= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `search-filter-pro.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard


= Using FTP =

1. Download `search-filter-pro.zip`
2. Extract the `search-filter-pro` directory to your computer
3. Upload the `search-filter-pro` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard


== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

= 1.1.8 =
* New - add new way to modify the main search query for individual forms
* New - added a new JS init event

= 1.1.7 =
* New - *beta* - Auto count for taxonomies - when using tag, category and taxonomies only in a search form, you can now enable a live update of fields, which means as users make filter selections, unavailable combinations will be hidden (this is beta and would love feedback especially from users with high numbers of posts/taxonomies)
* New - date picker for custom fields / post meta - dates must be stored as YYYYMMDD or as timestamps in order to use this field
* New - added JS events to capture start / end of ajax loading so you can add in your own custom loaders
* Fix - prefixed taxonomy and meta field names properly - there were collisions on the set defaults function, for example if a tax and meta share the same key there would be a collision
* Fix - errors with number ranges & range slider
* Fix - an error with detecting if a meta value is serialized
* Fix - scope issue with date fields auto submitting correctly


= 1.1.6 =
* **Notice** - dropped - `.postform` css class  this was redundant and left in by error - any users using this should update their CSS to use the new and improved options provided:
* New - class names added to all field list items for easy CSS styling + added classes to all options for form inputs for easy targeting of specific field values
* New - added a `<span class="sf-count">` wrapper to all fields where a count was being shown for easy styling
* Fix - removed all reference to `__DIR__` for PHP versions < 5.3
* Fix - Some general tweaks for WPML
* Fix - a bug when choosing all post types still adding "post_types" to the url

= 1.1.5 =
* **Notice** - this update breaks previous Sort Order fields, so make sure if you have a Sort Order Field to rebuild it once you've updated!
* New - Sort Order - in addition to sorting by Meta Value, users can now sort their results by ID, author, title, name, date, date modified, parent ID, random, comment count and menu order, users can also choose whether they they want only ASC or DESC directions - both are optional.
* New - Autocomplete Comboboxes - user friendly select boxes powered by Chosen - text input with auto-complete for selects and multiple selects - just tick the box when choosing a select or multiselect input type
* Fix - add a lower priority to `init` hook when parsing taxonomies - this helps ensure S&F runs after your custom taxonomies have been created
* Fix - add a lower priority to `pre_get_posts` - helps with modifying the main query after other plugins/custom code have run
* Fix - a problem with meta values having spaces

= 1.1.4 =
* New - Meta Suggestions - auto detect values for your custom fields / post meta
* Enhancement - improved Post Meta UI (admin)
* Fix - an error with displaying templates (there was a PHP error being thrown in some environments)
* Fix - an error where ajax enabled search forms were causing a refresh loop on some mobile browsers

= 1.1.3 =
* New - display meta data as dropdowns, checkboxes, radio buttons and multi selects
* New - added date formats to date field
* fix - auto submit & date picker issues
* fix - widget titles not displaying
* fix - missed a history.pushstate check for AJAX enabled search forms
* fix - dashboard menu conflict with other plugins
* fix - submit label was not updating
* fix - post count for authors was showing only for posts - now works with all post types
* compat - add fallback for `array_replace` for <= PHP 5.3 users

= 1.1.2 =
* New - customsise results URL - add a slug for your search results to display on (eg yousite.com/product-search)
* fix - js error when Ajax pagination links are undefined
* fix - date picker dom structure updated to match that of all other fields
* fix - scope issue when using auto submit on Ajax search forms

= 1.1.1 =
* fix - fixed an error where JS would hide the submit button :/
* fix - fixed an error where parent categories/taxonomies weren't showing their results

= 1.1.0 =
* New - AJAX - searches can be performed using Ajax
* fix - removed redundant js/css calls

= 1.0.0 =
* Initial release


== Upgrade Notice ==

= 1.0 =
Initial release

