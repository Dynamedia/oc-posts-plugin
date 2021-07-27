# About Posts for October CMS

Posts is for users who want to create great content without hassle.

It's a plugin that can be used by content creators of all sizes, from individual bloggers
to large publishing houses. Posts is powerful, but it's also really simple to use.

It's a good idea to check out the [live demo website](https://oc-posts.dynamedia.uk).
You can log in to the [backend](https://oc-posts.dynamedia.uk/backend) with the following
details.
~~~
username: demo
password: demo123
~~~

The above user has 'guest posting' privileges and is able to look around and write posts
but cannot publish them.

## Structure

A post is an article, such as a blog post or a news item. Posts are often grouped
by category and/or by tag, and they are associated with their author.

In Posts, everything is content. Categories and tags are more than simple lists of posts.
Each category and tag can be built up with content in just the same way as a post.

## Creating a Post

The following video (external link to YouTube) gives a brief overview of the interface and shows how to
construct a post, displaying it on the frontend with the posts demo theme.

[![Image of youtube video](https://oc-posts.dynamedia.uk/storage/app/media/documentation-images/youtube-post-create.png)](https://www.youtube.com/watch?v=k8hgjIfIx6Y)


## User Permissions

Posts was created so that it can be used by both individuals
and organizations of any size.  Advanced permissions and user roles
can be granted so that site owners have full control of the website while allowing
for other users to simply login and write. Other users might be granted editorial
or management permission, but however you choose to allocate permission, you
can be confident that your users have all the privileges they need and none they don't.

## Grouping Posts

Posts can be grouped by category, tag and author (backend user) which allows you
to build your site the way you want it.

The plugin extends the Backend User model to allow your users to have a
profile including their social media links, website links and biographical details.

Both categories and tags also have a body which can be built up using
blocks of content. This allows you to use the categorization pages as much more
than a simple list of posts
(if you want - Again, you are free to use as much or as little content as you please).

## URL's & Theming

The plugin is involved very early in the page lifecycle, so it is possible for
posts and categorization pages to define which theme layout should be used.
The main plugin settings allow you to define a default layout, which can be either
inherited or overridden by a group of posts, or by an individual post as required.
This is useful for large sites that want to maintain
a familiar look, but whose categories, for example, might need to look quite different.

Component settings have been kept as simple as possible,
with the majority of configuration options being made available in the main plugin
settings. It is important to visit the settings to choose the CMS page to use for
displaying posts, categories, tags and user profile.

It is recommended to use a single CMS page to display both posts and categories so you
would have the postDisplay and categoryDisplay components in the same place.
This allows for semantic URL's which can even begin at the project root.

For example, if you have a 'Widgets' category and a post titled 'Blue Widgets',
you could have your category listing available at /widgets and your post at
/widgets/blue-widgets.
Sub categories are supported and automatic redirection occurs if you change your
site structure,  Of course, you are free to define your site structure as you wish.
