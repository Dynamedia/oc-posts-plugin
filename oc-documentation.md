## Introduction

This plugin provides functionality to write posts (blog posts, news articles etc). To facilitate this, you will need several CMS pages containing relevant plugin components.

To properly implement this plugin into your website, you will likely want to be able to display the content of your posts and have a way to list posts according to various criteria, perhaps by date posted or by the associated category, tag or author.

A demonstration theme has been made available at  https://github.com/Dynamedia/oc-posts-demo-theme which is a useful resource for developers looking to get a quick start.

## On Installation

On installation a seeding script will create some example posts. Additionally,
several administrator roles will be created to help with permissions management.

Backend Users will have a 'profile' created where additional information can be entered.
Each user will have a configurable username generated which is used in the frontend to avoid exposing
the login name.

## Configuration

The majority of the plugin configuration can be found in the **backend settings** area in the **Dynamedia Posts Settings** section.  Some settings are absolute, whereas others allow you to define a default which will either be inherited or overridden by a post or group (category, tag) of posts.

##### Publisher Tab
These settings are to assist with Search Engine Optimization and are required for the plugin to generate the correct outputs in conjunction with the theme.

  - **Publisher Name** should contain the name of the organization or individual who is publishing the content, for example Dynamedia Limited.

  - **Publisher Type** dropdown to select either Individual or Organisation.

  - **Publisher URL** is the URL of the publisher. This may or may not be the same as the current website URL.

  - **Publisher Logo** is the logo of the publisher.

##### Posts Tab
This section is for configuring the CMS pages used to display post content.

  - **Post Display Page (With Categories)** allows you to select the CMS page which
  contains the *displayPost* component. It is recommended to select a page which also has
  the displayCategory component attached although this isn't required if you prefer
  a /categories & /posts URL structure.

  - **Post Display Page (No Category)** is for a CMS page with the displayPost
  component when not following the above advice. You can choose a separate CMS page
  for displaying uncategorized posts but it is advised against.

  Further information is available in the component section of this documentation.

##### Categories Tab
This section is for configuring the CMS pages used to display category content and
the associated posts list.

  - **Category Display Page** is for selecting the CMS page which contains the *displayCategory*
  component. As above, it is recommended to utilise both the *displayPost* and *displayCategory*
  components in the same CMS page.

  - **Default Posts Sort Order** defines a default sorting order for posts in the category.
  This value can be overridden in the settings section of each individual category.

  - **List Posts from Sub-Categories** allows you to choose whether the posts list for
  this category should contain posts from sub categories.
  This value can be overridden in the settings section of each individual category.

  - **Posts Per Page** is used to select how many posts should be shown per page.
  This value can be overridden in the settings section of each individual category.
  The values for this dropdown are derived from *config/config.php*.

##### Tags Tab
This section is for configuring the CMS page used to display tag content and
the associated posts list.

  - **Default Posts Sort Order** defines a default sorting order for posts in the category.
  This value can be overridden in the settings section of each individual category.

  - **Posts Per Page** is used to select how many posts should be shown per page.
  This value can be overridden in the settings section of each individual category.
  The values for this dropdown are derived from *config/config.php*.

##### Users Tab
This section is for configuring the CMS page used to display user profiles and
the associated posts list.

  - **Default Posts Sort Order** defines a default sorting order for posts by the user.
  This value can be overridden in the settings section of each individual category.

  - **Posts Per Page** is used to select how many posts should be shown per page.
  The values for this dropdown are derived from *config/config.php*.

##### CMS Layouts Tab
The plugin allows for on-the-fly theme layout changing so you are not restricted to
a single layout file per CMS page (post, category, tag). This gives great
flexibility when designing themes. An example use case for this is when designing a
large website which covers several different topics. You may want to have a different
appearance for different sections. Naturally, the options available in this section will
depend entirely on how many layouts have been made available in the theme.

- **Default Post Layout** the default for posts where the category or post has not specified a
separate layout. Posts can specify a layout file or can inherit from their primary category.

- **Default Category Layout** the default for categories where the category has not specified a
separate layout.

- **Default Tag Layout** the default for tags where the tag has not specified a
separate layout.

## Components

Several components are provided with the plugin.

#### displayPost

This component is for displaying the contents of a post. The component has no
configurable options and should be present on a CMS page which contains either of the two
available URL parameters.

Both of the available parameters ultimately fetch the post from the post slug but several
are provided to help with defining the ideal URL structure.

`:postsPostSlug` represents only the slug, eg /my-post

`:postsFullPath*` represents the category and post, eg /a-category/a-subcategory/my-post
although this can also match a category slug or a post without categories when used
alongside the displayCategory component.

#### displayCategory

This component is for displaying the contents of a category and its associated
list of posts. The component has no
configurable options and should be present on a CMS page which contains either of the two
available URL parameters.

`:postsCategoryPath*` represents the category part of the path, eg. /a-category or
/a-category/a-sub-category

`:postsFullPath*` represents the category and post, eg /a-category/a-subcategory/my-post
although this can also match a category slug or a post without categories when used
alongside the displayCategory component.

##### Combining displayPost and displayCategory

It is recommended to place both of the components on a single CMS page
using the `:postsFullPath*` parameter in the url. The plugin will check whether the
last part of the URL path is either a category slug or a post slug and load the
relevant data and partial accordingly.

This has the added benefit that if your category structure changes, all of your URL's
will still work as long as the slug is the same as users will be directed to the new URL.

Of course, as a developer you are free to use the components as you see fit.


#### displayTag

This component is for displaying the contents of a tag and its associated
list of posts. The component has no configurable options.

The tag will be identified using the `:postsTagSlug` URL parameter.


#### displayUser

This component is responsible for displayng a user profile and the posts
of the user.

The user will be identified using the `:postsUsername` URL parameter.

It is important to note that the username is **not** the backend login name.
A username is generated by the Profile model for each user, but this can be
changed in the user admin settings.





