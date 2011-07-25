=== CleanCodeNZ Exclude Pages Plugin  ===
Contributors: cleancodenz
Donate link: http://www.cleancode.co.nz/
Tags: wp_list_pages_excludes, navigation,search, menu, exclude pages, hide pages, custom fields,parent,child
Requires at least: Not tested
Tested up to: 3.0
Stable tag: 2.0.0


This is a plugin to hide pages from navigation and/or search results using custom fields, parent and child pages are supported too 

== Description ==

This plugin allows you to define the custom field to be attached to a page to be excluded from navigation and(or) search results.

As the exclusion is done through highly optimized cached general wordpress query, and queried once for even hierarchical data,  this would provide best performance and scalability.
It is using custom fields without creating field or table, it is done in the most unobtrusive way.
Pages that have parent and child relationships are fully supported without any hassle.      


Any issues: [contact me](http://www.cleancode.co.nz/contact).


== Installation ==

1. Upload this plugin to your plugins directory. It will create a 'wp-content/plugins/cleancodenzexlp/' directory
2. WordPress users then can go to their Plugins page and activate "CleanCode NZ Exclude Pages Plugin".
3. Now go to Settings->CleanCodeNZEXLP, type in custom field name and value pair for pages to be excluded.
4. That is all it needed for it to work

== Frequently Asked Questions ==

= What happens for parent and child pages =

1. When parent is excluded, child page is excluded, ==> neither appears
2. When parent is excluded, child page is not excluded, ==> only child page will appear
3. When parent is not excluded, child page is excluded, ==> only parent page will appear
4. When parent is not excluded, child page is not excluded, ==> both parent and child page will appear

= What do the options mean =

1.  Exclude from search too  =1 , Exclude from search only =1  ==> Exclude from search results only
2.  Exclude from search too  =1 , Exclude from search only =0  ==> Exclude from both navigation and search results  
3.  Exclude from search too  =0 , Exclude from search only =1  ==> Exclude from search results only
4.  Exclude from search too  =0 , Exclude from search only =0  ==> Exclude from navigation only

It is obvious not the smartest way to configure two exclusions, but has to be done like this to make it backward compatible.

= Where can I find help for this plugin =

You can find more information of this plugin from [CleanCodeNz Exclude Pages WordPress Plugin](http://www.cleancode.co.nz/cleancodenz-exclude-pages-wordpress-plugin "Using custom fields to hide pages")


== Screenshots ==
Screenshots at: [CleanCodeNz Exclude Pages WordPress Plugin](http://www.cleancode.co.nz/cleancodenz-exclude-pages-wordpress-plugin "Using custom fields to hide pages")

== Changelog ==

1.0.0 : First release


1.1.0 : Exclude from search feature added.


1.2.0 : Parent and child pages support added.

2.0.0 : Exclude from search results only is added, so now this plugin can be used for exclude from navigation or exclude from search results or both.


== Upgrade Notice ==

None


