=== Custom Fields Permalink ===

Contributors: toiplan
Tags: customfields, permalink
Requires at least: 2.6
Tested up to: 0.1.0.1
Stable tag: 0.1.0.1

This plugin enable to make a permalink from custom field's value.

== Description ==

This plugin enable to make a permalink from custom field's value.

== Installation ==

1. Upload the Custom Fields Permalink to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Save a custom permalink structure again after this plugin is enabled.


== Usage ==

 > Custom permalink tags:
 > 
 >  - %cfp_a_customfield_name%
 >  - %cfp_a_customfield_name_or_page_id%
 >  - %cfp_a_customfield_name_or_pagename%
 > 
 > "a_customfield_name" can be replaced with an arbitrary field name.
 > 
 > Examples:
 > 
 > When a specification is as follows.
 > 
 >  - /%cfp_a_customfield_name%
 > 
 >  a entry has custom fields named "a_customfield_name":
 >   > /a_value_of_customfield (a value of "a_customfield_name")
 > 
 >  a entry has not custom fields named "a_customfield_name":
 >   > error!
 > 
 > 
 > When a specification is as follows.
 > 
 >  - /%cfp_a_customfield_name_or_page_id%
 > 
 >  a entry has custom fields named "a_customfield_name":
 >   > /a_value_of_customfield (a value of "a_customfield_name")
 > 
 >  a entry has not custom fields named "a_customfield_name":
 >   > /1 (page_id)
 > 
 > 
 > When a specification is as follows
 > 
 >  - /%cfp_a_customfield_name_or_pagename%
 > 
 >  a entry has custom fields named "a_customfield_name":
 >   > /a_value_of_customfield (a value of "a_customfield_name")
 > 
 >  a entry has not custom fields named "a_customfield_name":
 >   > /as_page_title (pagename)
