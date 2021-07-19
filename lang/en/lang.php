<?php

return [
    'plugin' => [
        'name'          => 'Posts',
        'description'   => 'Posts for October CMS.'
    ],
    'posts' => [
        'tabs'  => [
            'categories'        => 'Categories',
            'tags'              => 'Tags',
        ],
        'labels' => [
            'author'            => 'Author',
            'editor'            => 'Editor',
            'is_published'      => 'Published?',
            'published_at'      => 'Published At',
            'publishing_time'   => 'Published Time',
            'published_until'   => 'Published Until',
            'show_contents'     => 'Show Contents?',
            'primary_category'  => 'Primary Category',
            'category_list'     => 'Category List',
            'tags'              => 'Tags',
        ],
        'comments' => [
            'primary_category'  => 'The most relevant category',
            'published_at'      => 'Default to now if published',
            'published_until'   => 'Optional post expiry time',
        ]
    ],

    'categories' => [

    ],

    'tags' => [
        'labels' => [
            'is_approved' => 'Approved?',
        ]
    ],

    'blocks' => [
        'names' => [
          'section'     => 'Section Block',
          'html'        => 'HTML Block',
          'image'       => 'Image Block',
          'cms_content' => 'CMS Content',
          'cms_partial' => 'CMS Partial',
          'page_break'  => 'Page Break',
        ],
        'descriptions' => [
            'section'     => 'Add a content section',
            'html'        => 'Add a block of HTML',
            'image'       => 'Add an image block',
            'cms_content' => 'Add CMS content',
            'cms_partial' => 'Add a CMS partial',
            'page_break'  => 'Add a page break'
        ],
        'tabs'  => [
            'main'  => 'Main',
        ],
        'labels' => [
            'cms_content'       => 'CMS Content',
            'cms_partial'       => 'CMS Partial',
            'html'              => 'HTML Block',
            'section_heading'   => 'Section Heading',
            'section_id'        => 'Section ID',
            'section_content'   => 'Section Content',
            'in_contents'       => 'Include in contents?',
        ]
    ],

    'common' => [
        'tabs' => [
            'detail'        => 'Detail',
            'images'        => 'Images',
            'image'         => 'Image',
            'body'          => 'Body',
            'seo'           => 'SEO',
            'posts'         => 'Posts',
            'settings'      => 'Settings',
            'main'          => 'Main',
        ],
        'labels' => [
            'name'          => 'Name',
            'title'         => 'Title',
            'description'   => 'Description',
            'slug'          => 'Slug',
            'excerpt'       => 'Excerpt',
            'seo'           => 'SEO',
            'posts'         => 'Posts',
            'cms_layout'    => 'CMS Layout',
            'author'        => 'Author',
            'editor'        => 'Editor',
            'block_heading' => 'Section Heading',
            'block_id'      => 'Section ID',
            'block_content' => 'Section Content',
            'empty_option'  => 'Please Select',
            'approved'      => 'Approved',
        ],
        'dropdown' => [
            'empty_option'  => 'Please Select',
            'newest_first'  => 'Newest First',
            'oldest_first'  => 'Oldest First',
            'recent_update' => 'Recently Updated',
            'random'        => 'Random',
            'yes'           => 'Yes',
            'no'            => 'No',
        ]
    ],
    'images' => [
        'tabs' => [
            'list'          => 'List Image',
            'banner'        => 'Banner Image',
            'default'       => 'Default',
            'responsive'    => 'Responsive',
            'social'        => 'Social Images',
            'facebook'      => 'Facebook',
            'twitter'       => 'Twitter',
        ],
        'labels' => [
            'image_select'  => 'Choose an Image',
            'alt_text'      => 'Image Alt Text',
            'css_class'     => 'Image CSS Class',
            'image_style'   => 'Image Style',
            'media_query'   => 'Media Query',
            'screen_width'  => 'Screen Width',
        ],
        'comments' => [
            'facebook' => 'Social sharing image for Facebook',
            'twitter'  => 'Social sharing image for Twitter',
        ],
        'options' => [
            'min_width'     => 'Min Width',
            'max_width'     => 'Max Width',
        ]
    ],
    'seo' => [
        'tabs' => [
            'general'   => 'General',
            'search'    => 'Search',
            'opengraph' => 'Facebook/Opengraph',
            'twitter'   => 'Twitter Cards',
        ],
        'labels' => [
            'post_type'         => 'Post Type',
            'about'             => 'Post is About',
            'keywords'          => 'Keywords',
            'page_title'        => 'Page Title',
            'meta_description'  => 'Meta Description',

        ],
        // Not set in config as these are schema defined and don't need altering
        'dropdown' => [
            'article'       => 'Article',
            'blogPosting'   => 'Blog Post',
            'newsArticle'   => 'News Article',
        ]
    ],

    'settings' => [
        'tabs' => [
            'publisher'     => 'Publisher',
            'posts'         => 'Posts',
            'categories'    => 'Categories',
            'tags'          => 'Tags',
            'users'         => 'Users',
            'cms_layouts'   => 'CMS Layouts',
        ],
        'labels' => [
            'publisher_name'            => 'Publisher Name',
            'publisher_type'            => 'Publisher Type',
            'publisher_url'             => 'Publisher URL',
            'publisher_logo'            => 'Publisher Logo',
            'posts_sort_order'          => 'Posts Sort Order',
            'default_posts_sort_order'  => 'Default Posts Sort Order',
            'posts_per_page'            => 'Posts Per Page',
            'include_subcategories'     => 'List Posts from Sub-Categories',
            'post_page'                 => 'Post Display Page (With Categories)',
            'post_page_no_category'     => 'Post Display Page (No Category)',
            'category_page'             => 'Category Display Page',
            'tag_page'                  => 'Tag Display Page',
            'user_page'                 => 'User Display Page',
            'default_post_layout'       => 'Default Post Layout',
            'default_category_layout'   => 'Default Category Layout',
            'default_tag_layout'        => 'Default Tag Layout',
        ],
        'comments' => [
          'post_page' => 'It is recommended to use a CMS page capable of handling both posts and categories',
          'post_page_no_category' => 'Only used where using separate pages for post and category and post has no primary category',
          'category_page' => 'It is recommended to use a CMS page capable of handling both posts and categories',
        ],
        'dropdown' => [
          'organization'    => 'Organization',
          'person'          => 'Person',
        ],
    ],
    'components' => [
        'common' => [
        ],
        'display_post' => [
            'name'          => 'Display Post',
            'description'   => 'Show post content',
        ],
        'display_category'  => [
            'name'          => 'Display Category',
            'description'   => 'Show category content with posts',
        ],
        'display_tag'  => [
            'name'          => 'Display Tag',
            'description'   => 'Show tag content with posts',
        ],
        'display_user'  => [
            'name'          => 'Display User',
            'description'   => 'Show user profile with authored posts',
        ],
        'list_posts'  => [
            'name'          => 'List Posts',
            'description'   => 'Configurable posts list',
            'properties'    => [
                'category_filter' => [
                    'title'         => 'Category Filter',
                    'description'   => 'Restrict results to this category',
                ],
                'include_subcategories' => [
                    'title'         => 'Include Subcategories',
                    'description'   => 'List posts from subcategories of selected category',
                ],
                'tag_filter' => [
                    'title'         => 'Tag Filter',
                    'description'   => 'Restrict results to this tag',
                ],
                'post_ids' => [
                    'title'         => 'Post Filter',
                    'description'   => 'Restrict results to these post ID\'s',
                    'validation'    => 'Please enter a comma separated list of post ID\'s'
                ],
                'not_post_ids' => [
                    'title'         => 'Exclude Posts',
                    'description'   => 'Exclude these post ID\'s',
                    'validation'    => 'Please enter a comma separated list of post ID\'s'
                ],
                'not_category_ids' => [
                    'title'         => 'Exclude Posts from Category',
                    'description'   => 'Exclude these category ID\'s',
                    'validation'    => 'Please enter a comma separated list of category ID\'s'
                ],
                'not_tag_ids' => [
                    'title'         => 'Exclude Posts from Tag',
                    'description'   => 'Exclude these tag ID\'s',
                    'validation'    => 'Please enter a comma separated list of tag ID\'s'
                ],
                'posts_limit' => [
                    'title'         => 'Total Posts',
                    'description'   => 'Limit the number of posts to fetch',
                    'validation'    => 'Please enter a positive whole number or leave blank'
                ],
                'posts_per_page' => [
                    'title'         => 'Posts per Page',
                    'description'   => 'Limit the number of posts per page',
                    'validation'    => 'Please enter a positive whole number'
                ],
                'no_posts_message' => [
                    'title'         => 'No Posts Message',
                    'description'   => 'Message to display when no posts are found',
                    'default'       => 'No posts found',
                ],
                'sort_order' => [
                    'title'         => 'Sort Order',
                    'description'   => 'Sort the fetched posts',
                ],
            ],
        ],
        'search_posts'  => [
            'name'          => 'Search Posts',
            'description'   => 'Show search results',
        ],
    ]
];
