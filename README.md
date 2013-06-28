Categories Widget with Descriptions PRO Version
===========================

This is a simple WordPress plugin that lets you list your categories with description in your blog sidebar. I'm not really sure why this isn't already an option, but I couldn't find it out there so I built it.

Very little styling has been applied so that the theme designer, developer or use can easily customize the appearance.


Installation
------------
To install, download the plugins zip file. Within your WordPress admin, navigation to Plugins > Add New and click "Upload." Browse to the zip file and upload it, then click to activate it.

You'll then see a new widget in your Appearance > Widgets screen. Add the widget to your sidebar and configure and style as desired.


Using the Shortcode
----------------
In the editor, you can use the shortcode like this:

    [neatly_categories]

In a theme, you can use it like this:

    <?php echo do_shortcode('[neatly_categories]'); ?>

There are a few options/ attributes you can choose to modify:

###Title Text

The default title for the recent posts block is "Categories" but you can change it by defining `title_text=` within the shortcode (any string will do), e.g.:

    [neatly_categories title_text="Read More Posts On..."]


###Order

You can determine which attribute (name, ID, or number of posts in the category) is used to sort the list, and you can define whether the categories should be displayed in ascending (default) or descending order:

**OrderBy**

Available values:
* ID
* name _(default)_
* slug
* count

```
[neatly_categories orderby=""]
```


**Order**

    [neatly_categories orderby="ASC"]

Or

    [neatly_categories orderby="DESC"]
    

###Hide/ Show Empty Categories

By default, only categories with posts assigned to them are displayed. If you want to also show empty categories, you can do that like so:

    [neatly_categories hide_empty="false"]


Changelog
------------
* 1.0 Release with taxonomy and child_of features