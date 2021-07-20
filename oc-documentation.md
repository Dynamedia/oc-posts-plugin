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

  - **Publisher Name** - Should contain the name of the organization or individual who is publishing the content, for example Dynamedia Limited.

  - **Publisher Type** - Dropdown to select either Individual or Organisation.

  - **Publisher URL** - The URL of the publisher. This may or may not be the same as the current website URL.

  - **Publisher Logo** - The logo of the publisher.

##### Posts Tab
This section is for configuring the CMS pages used to display post content.

  - **Post Display Page (With Categories)** - Select the CMS page which
  contains the *displayPost* component. It is recommended to select a page which also has
  the displayCategory component attached although this isn't required if you prefer
  a /categories & /posts URL structure.

  - **Post Display Page (No Category)** - Select the CMS page with the displayPost
  component when not following the above advice. You can choose a separate CMS page
  for displaying uncategorized posts but it is advised against.

  Further information is available in the component section of this documentation.

##### Categories Tab
This section is for configuring the CMS pages used to display category content and
the associated posts list.

  - **Category Display Page** - Select the CMS page which contains the *displayCategory*
  component. As above, it is recommended to utilise both the *displayPost* and *displayCategory*
  components in the same CMS page.

  - **Default Posts Sort Order** - Choose a default sorting order for posts in the category.
  This value can be overridden in the settings section of each individual category.

  - **List Posts from Sub-Categories** - Choose whether the posts list for
  this category should contain posts from sub categories.
  This value can be overridden in the settings section of each individual category.

  - **Posts Per Page** - Used to select how many posts should be shown per page.
  This value can be overridden in the settings section of each individual category.
  The values for this dropdown are derived from *config/config.php*.

##### Tags Tab
This section is for configuring the CMS page used to display tag content and
the associated posts list.

  - **Default Posts Sort Order** - Defines a default sorting order for posts in the category.
  This value can be overridden in the settings section of each individual category.

  - **Posts Per Page** - Used to select how many posts should be shown per page.
  This value can be overridden in the settings section of each individual category.
  The values for this dropdown are derived from *config/config.php*.

##### Users Tab
This section is for configuring the CMS page used to display user profiles and
the associated posts list.

  - **Default Posts Sort Order** - Defines a default sorting order for posts by the user.
  This value can be overridden in the settings section of each individual category.

  - **Posts Per Page** - Used to select how many posts should be shown per page.
  The values for this dropdown are derived from *config/config.php*.

##### CMS Layouts Tab
The plugin allows for on-the-fly theme layout changing so you are not restricted to
a single layout file per CMS page (post, category, tag). This gives great
flexibility when designing themes. An example use case for this is when designing a
large website which covers several different topics. You may want to have a different
appearance for different sections. Naturally, the options available in this section will
depend entirely on how many layouts have been made available in the theme.

- **Default Post Layout** - The default for posts where the category or post has not specified a
separate layout. Posts can specify a layout file or can inherit from their primary category.

- **Default Category Layout** - The default for categories where the category has not specified a
separate layout.

- **Default Tag Layout** - The default for tags where the tag has not specified a
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

The following snippet demonstrates how you might implement this component, standalone
on a CMS page with the full category path as part of the URL.

~~~
title = "Posts Page"
url = "/posts/:postsCategoryPath*/:postsPostSlug"

[displayPost]
==
{% component 'displayPost' %}
~~~

If you want to implement without showing the category you might do
as follows

~~~
title = "Posts Page Without Category"
url = "/posts/:postsPostSlug"

[displayPost]
==
{% component 'displayPost' %}
~~~

displayPost will inject the following variables:

 - **post** - The post object


#### displayCategory

This component is for displaying the contents of a category and its associated
list of posts. The component has no
configurable options and should be present on a CMS page which contains either of the two
available URL parameters.

`:postsCategorySlug` represents the category slug.

`:postsCategoryPath*` represents the category part of the path, eg. /a-category or
/a-category/a-sub-category

`:postsFullPath*` represents the category and post, eg /a-category/a-subcategory/my-post
although this can also match a category slug or a post without categories when used
alongside the displayCategory component.

The following snippet demonstrates how you might implement this component, standalone
on a CMS page with the full category path as part of the URL.

~~~
title = "Category Display Page"
url = "/posts/category/:postsCategoryPath*"

[displayCategory]
includeSubcategories = 1
postsPerPage = 10
==
{% component 'displayCategory' %}
~~~

Or with just the category slug but without the full path

~~~
title = "Category Display Page"
url = "/posts/category/:postsCategorySlug"

[displayCategory]
includeSubcategories = 1
postsPerPage = 10
==
{% component 'displayCategory' %}
~~~

##### Combining displayPost and displayCategory

It is recommended to place both of the components on a single CMS page
using the `:postsFullPath*` parameter in the url. The plugin will check whether the
last part of the URL path is either a category slug or a post slug and load the
relevant data and partial accordingly.

In this case, you could use the following snippet.

~~~
title = "Posts and Category Display Page"
url = "/:postsFullPath*"

[displayCategory]
includeSubcategories = 1
postsPerPage = 10
sortOrder = "published_at desc"

[displayPost]
==
{% component 'displayCategory' %}
{% component 'displayPost' %}
~~~

This would match, for example:

`https://example.org/my-uncategorized-post`

`https://example.org/a-category`

`https://example.org/a-category/my-categorized-post`

It would, however, not match:

`https://example.org/my-static-page`

Of course, you do not need to implement from the URL root,
you could just as easily use `url = '/posts/:postsFullPath*\'`

Note: URL's

The above components are both used in URL generation. When you have created
your preferred structure and selected the relevant pages in the plugin
settings, post and category objects will have the url attribute
available. You can access these at `{{ post.url }}` and `{{ category.url }}`

If your category structure changes, users will always be redirected to
the correct URL.

As an example, if you have a post with the slug 'my-interesting-post',
and it has no category, its URL might be
`https://example.org/posts/my-interesting-post`

You may later add a category called 'my-stuff' and choose to add the post
so it's URL now becomes
`https://example.org/posts/my-stuff/my-interesting-post`

In this case, `https://example.org/posts/my-interesting-post` will redirect
to `https://example.org/posts/my-stuff/my-interesting-post`.


#### displayTag

This component is for displaying the contents of a tag and its associated
list of posts. The component has no configurable options.

The tag will be identified using the `:postsTagSlug` URL parameter.

Example implementation

~~~
title = "Tags"
url = "/tags/:postsTagSlug"

[displayTag]
postsPerPage = 10
==
{% component 'displayTag' %}
~~~


#### displayUser

This component is responsible for displayng a user profile and the posts
of the user.

The user will be identified using the `:postsUsername` URL parameter.

It is important to note that the username is **not** the backend login name.
A username will be generated by the Profile model for each user, but this can be
changed in the user admin settings.

Example implementation

~~~
title = "User Posts"
url = "/user/:postsUsername"

[displayUser]
==
{% component 'displayUser' %}

~~~

#### searchPosts

This component is responsible for displayng search results for Posts.

The search query should be passed with the `q` URL parameter, for example

`https://example.org/search?q=keyword`

This component requires some configuration

- **postsLimit** - The total number of posts that can be fetched.

- **postsPerPage** - When this is greater than postsLimit or postsLimit is not
set, the results will be paginated.

- **sortOrder** - Dropdown to choose the order in which results should be returned.

Example implementation

~~~
title = "Search"
url = "/search"

[searchPosts]
postsPerPage = 10
==
{% component 'searchPosts' %}
~~~

### listPosts

This component is responsible for displayng a customised list of posts.

This component requires some configuration

- **categoryFilter** - Posts from the selected category.

- **includeSubcategories** - Include posts from the selected category's subcategories.

- **tagFilter** - Posts tagged with the selected tag.

- **postIds** - Return only posts with the given IDs, in the order specified.

- **notPostIds** - Exclude posts with the given IDs.

- **notCategoryIds** - Exclude posts from categories with the given IDs.

- **notTagIds** - Exclude posts with tags matching with the given IDs.

- **postsLimit** - The total number of posts that can be fetched.

- **postsPerPage** - When this is greater than postsLimit or postsLimit is not
set, the results will be paginated.

- **sortOrder** - Choose the order in which results should be returned.

Example implementation, perhaps suitable for a website homepage

~~~
title = "Home"
url = "/"

[listPosts featuredPosts]
postIds = "1,2"
postsLimit = 2
postsPerPage = 2
sortOrder = "published_at desc"

[listPosts latestPosts]
notPostIds = "1,2"
notCategoryIds = 4
notTagIds = 1
postsLimit = 5
postsPerPage = 5
sortOrder = "published_at desc"
==

<div class="content">
    <div class="row">
        <div class="col-12">
            <h2 class="mt-4 mb-4">Featured Posts</h2>
            {% component 'featuredPosts' %}

            <h2 class="mt-4 mb-4">Latest posts</h2>
            {% component 'latestPosts' %}
        </div>
    </div>
</div>
~~~
