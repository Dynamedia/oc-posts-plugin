# About Posts for October CMS

Posts is a powerful publishing platform for user generated content.

It's a plugin that can be used by content creators of all sizes, from individual bloggers
to large publishing houses. Posts is powerful, but it's also really simple to use.

It's a good idea to check out the [interactive demo website](https://playground.posts-plugin.dynamedia.uk).
You can also find more information about the plugin [here](https://posts-plugin.dynamedia.uk).

## Key Features at a Glance

- Automatic URL redirection when structures change
- Full SEO support including schema.org markup generation
- Multi-language support for content & URLs
- Clean, semantic URL's
- Multi-type content body options
- Structured post types defined by the active theme. Create anything!
- Per post theme layout with inheritance
- ACL's ensure multi-user safety
- RSS & sitemap generation

## Structure

A post is an article, such as a blog post or a news item. Posts are often grouped
by category and/or by tag, and they are associated with their author & editor.

In Posts, everything is content. Categories and tags are more than simple lists of posts.
Each category and tag can be built up with content in just the same way as a post.

## Automatic Redirects

Often, as a content-rich website evolves, it becomes necessary to add new categories and move posts
to more suitable categories. This can be a headache for developers and SEO's.

All Posts objects (posts, categories & tags) have a single, definitive URL which is based on the object's
type and slug identifier. In the case of a post, the URL is also derived from its primary category and
that category's parent categories (if any).

Posts tracks changes to object slugs and can detect when a stale link has been used, automatically
redirecting the visitor to the correct path thereby removing the need to set manual redirects and
eliminating duplicate content concerns.

## SEO & Schema.org Markup

Posts takes full responsibility for adding SEO related meta tags and schema markup to your posts pages.

Several sections are injected into the frontend theme which allow the site administrator to define
the site owner and publisher type.

Each post object has a section where further SEO details can be added including Opengraph and Twitter titles
along with SEO page title and meta descriptions. Where details are missing, sensible defaults will be
used as a fallback.

In the case of 'structured posts', which are defined by the active theme, it's possible to further
extend the Schema markup. Two examples are provided in the demo theme for product reviews and recipes.

## Multi-Language Support

Posts, categories & tags can all be fully translated into any language which has been defined in the
Rainlab Translate plugin. Content and URL's can all be translated and benefit from redirection as detailed
above.

A post may have its default language set as any of the available languages. This is useful if you have
content which should not be published in the default language/locale of the main webiste.

## Multi-Type Content Body

Currently there are four available content body types for posts and three for categories and tags.

##### Repeater Body

The repeater body allows users to create content in a methodical fashion, adding headings, content sections
and media part-by-part. CMS content files and partials can be added here.

##### Richeditor Body

Create content with the standard WYSIWYG editor

##### Markdown Body

Create content with the standard Markdown editor

##### Structured Posts (posts only)

Structured posts are defined not by the plugin, but by the active theme.

The theme should supply a yaml file containing a form definition and a partial file for rendering the
post.  A structured post could be very simple, or very complex depending on the theme developer's vision
for the structure.  The linked demo theme provides an example structured post for recipes and product reviews
including the relevant Schema.org markup for Google rich results.

## Per Post Theme Layout

Posts, categories and tags can specify a theme layout

The main plugin settings allow you to define a default layout, which can be either
inherited or overridden by a group of posts, or by an individual post as required.

This is useful for large sites that want to maintain
a familiar look, but whose categories, for example, might need to look quite different.

## User Permissions (ACLs)

Advanced permissions and user roles
can be granted so that site owners have full control of the website while allowing
for other users to simply login and write. Some users might be granted editorial
or management permission, but whichever way you choose to allocate permissions, you
can be confident that your users have all the privileges they need and none they don't.

## URL Configuration

Component settings have been kept as simple as possible,
with the majority of configuration options being available in the main plugin
settings. It is important to visit the settings to choose the CMS page to use for
displaying posts, categories, tags and user profile.

It is recommended to use the same URL for both the post display and category display pages.
The plugin handles the logic to resolve the correct page in the event of a URL clash.
This allows for semantic URL's which can even begin at the project root.

Post Display Page.
~~~
title = "Post Display Page"
url = "/:postsFullPath*"
layout = "default"
meta_title = "Post Display Page"
is_hidden = 0

[displayPost]
==
{% component 'displayPost' %}
~~~

Category Display Page
~~~
title = "Category Display Page"
url = "/:postsFullPath*"
layout = "default"
meta_title = "Category Display Page"
is_hidden = 0

[displayCategory]
includeSubcategories = 1
postsPerPage = 11
noPostsMessage = "No posts found"
sortOrder = "published_at desc"
==
{% component 'displayCategory' %}
~~~

By using the above recommended settings, the following would all be valid:
- /a-category
- /an-uncategorized-post
- /a-category/a-post-in-a-category
- /a-category/a-sub-category/a-post-in-the-subcategory

You can, of course refer to the documentation and define your site structure as you please.

