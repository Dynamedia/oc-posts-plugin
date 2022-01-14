<?php

return [
    'plugin' => [
        'name'          => 'Posts',
        'description'   => 'Posts for October CMS v2+.',
    ],
    'posts' => [
        'titles' => [
            'post'      => 'Post',
            'manage'    => 'Manage Posts',
        ],
        'tabs'  => [
            'categories'        => 'Categories',
            'tags'              => 'Tags',
        ],
        'labels' => [
            'author'            => 'Author',
            'editor'            => 'Editor',
            'is_published'      => 'Published',
            'published_at'      => 'Published At',
            'publishing_time'   => 'Published Time',
            'publishing_date'   => 'Publishing Date',
            'published_until'   => 'Published Until',
            'hide_published'    => 'Hide Published',
            'show_contents'     => 'Show Contents?',
            'primary_category'  => 'Primary Category',
            'category_list'     => 'Category List',
            'tags'              => 'Tags',
        ],
        'comments' => [
            'primary_category'  => 'The most relevant category',
            'published_at'      => 'Default to now if published',
            'published_until'   => 'Optional post expiry time',
        ],
        'buttons'   => [
            'new'           => 'New Post',
        ],
    ],

    'postslugs' => [
        'titles' => [
            'postslug'  => 'Post Slug',
            'manage'    => 'Manage Post Slugs',
        ],
        'buttons'   => [
            'new'           => 'New Post Slug',
        ],
    ],

    'posttranslations' => [
        'titles' => [
            'posttranslation'   => 'Post Translation',
            'manage'            => 'Manage Post Translations',
        ],
        'buttons'   => [
            'new'   => 'New Post Translation',
        ],
    ],

    'categories' => [
        'titles' => [
            'category'  => 'Category',
            'manage'    => 'Manage Categories',
            'reorder'   => 'Re-Order Categories',
        ],
        'buttons'   => [
            'new'   => 'New Category',
        ],
    ],

    'categoryslugs' => [
        'titles' => [
            'categoryslug'   => 'Category Slug',
            'manage'         => 'Manage Category Slugs',
        ],
        'buttons'   => [
            'new'           => 'New Category Slug',
        ],
    ],

    'categorytranslations' => [
        'titles' => [
            'categorytranslation'   => 'Category Translation',
            'manage'                => 'Manage Category Translations',
        ],
        'buttons'   => [
            'new'           => 'New Category Translation',
        ],
    ],

    'tags' => [
        'titles' => [
            'tag'       => 'Tag',
            'manage'        => 'Manage Tags',
        ],
        'labels' => [
            'is_approved' => 'Approved?',
        ],
        'buttons'   => [
            'new'       => 'New Tag',
            'approve'   => 'Approve selected'
        ],
        'messages'  => [
            'approve_selected_confirm'   => 'Are you sure you want to approve the selected tags?'
        ],
    ],

    'tagslugs' => [
        'titles' => [
            'tagslug'       => 'Tag Slug',
            'manage'        => 'Manage Tag Slugs',
        ],
        'buttons'   => [
            'new'           => 'New Tag Slug',
        ],
    ],

    'tagtranslations' => [
        'titles' => [
            'tagtranslation'    => 'Tag Translation',
            'manage'            => 'Manage Tag Translations',
        ],
        'buttons'   => [
            'new'   => 'New Tag Translation',
        ],
    ],

    'blocks' => [
        'names' => [
            'richeditor_block'    => 'Richeditor Block',
            'markdown_block'      => 'Markdown Block',
            'html_block'          => 'HTML Block',
            'image_block'         => 'Image Block',
            'cms_content_block'   => 'CMS Content',
            'cms_partial_block'   => 'CMS Partial',
            'page_break'          => 'Page Break',
            'heading_block'       => 'Heading Block'
        ],
        'descriptions' => [
            'richeditor_block'  => 'Add a content section (Richeditor)',
            'markdown_block'    => 'Add a content section (Markdown)',
            'html_block'        => 'Add a block of HTML',
            'image_block'       => 'Add an image block',
            'cms_content_block' => 'Add CMS content',
            'cms_partial_block' => 'Add a CMS partial',
            'page_break'        => 'Add a page break',
            'heading_block'     => 'Add a heading',
        ],
        'tabs'  => [
            'main'      => 'Main',
            'data'      => 'Data'
        ],
        'labels' => [
            'cms_content_block'     => 'CMS Content',
            'cms_partial_block'     => 'CMS Partial',
            'heading_type'          => 'Heading Type',
            'heading_content'       => 'Heading Content',
            'html_block'            => 'HTML Block',
            'in_contents'           => 'Include in contents?',
            'contents_title'        => 'Title for Contents',
            'block_id'              => 'Section ID',
            'partial_data'          => 'Pass variables to the partial',
            'partial_data_key'      => 'Key',
            'partial_data_value'    => 'Value',
        ],
        'empty' => [
            'cms_content_block'   => 'Select the content',
            'cms_partial_block'   => 'Select the partial'
        ]
    ],

    'common' => [
        'titles' => [
            'reorder' => 'Re-Order',
            'previewing'            => 'Previewing \':name\'',
            'editing'               => 'Editing \':name\''
        ],
        'tabs' => [
            'detail'        => 'Detail',
            'images'        => 'Images',
            'image'         => 'Image',
            'body'          => 'Body',
            'seo'           => 'SEO',
            'posts'         => 'Posts',
            'settings'      => 'Settings',
            'main'          => 'Main',
            'translations'  => 'Translations',
            'slugs'         => 'Related Slugs',
            'preview'       => 'Preview'
        ],
        'labels' => [
            'id'                    => 'ID',
            'name'                  => 'Name',
            'title'                 => 'Title',
            'description'           => 'Description',
            'slug'                  => 'Slug',
            'slugs'                 => 'Slugs',
            'excerpt'               => 'Excerpt',
            'seo'                   => 'SEO',
            'post'                  => 'Post',
            'posts'                 => 'Posts',
            'new_post'              => 'New Post',
            'postslugs'             => 'Post Slugs',
            'posttranslations'      => 'Post Translations',
            'category'              => 'Category',
            'categories'            => 'Categories',
            'categoryslugs'         => 'Category Slugs',
            'categorytranslations'  => 'Category Translations',
            'tag'                   => 'Tag',
            'tags'                  => 'Tags',
            'tagslugs'              => 'Tag Slugs',
            'tagtranslations'       => 'Tag Translations',
            'cms_layout'            => 'CMS Layout',
            'author'                => 'Author',
            'editor'                => 'Editor',
            'block_heading'         => 'Section Heading',
            'block_id'              => 'Section ID',
            'block_content'         => 'Section Content',
            'empty_option'          => 'Please Select',
            'approved'              => 'Approved',
            'locale'                => 'Locale',
            'refresh'               => 'Refresh',
            'body_type'             => 'Body Type',
            'link'                  => 'Link',
            'published_posts'       => 'Published Posts',
            'unpublished_posts'     => 'Unpublished Posts',
            'active'                => 'Active',
            'locale'                => 'Locale',
            'translations'          => 'Translations',
            'primary_locale'        => 'Primary Locale',
            'populate_from'         => 'Populate From',
            'live_view'             => 'Live View'
        ],
        'dropdown' => [
            'empty_option'          => 'Please Select',
            'newest_first'          => 'Newest First',
            'oldest_first'          => 'Oldest First',
            'recent_update'         => 'Recently Updated',
            'random'                => 'Random',
            'yes'                   => 'Yes',
            'no'                    => 'No',
            'none'                  => 'None',
            'inherit'               => 'Inherit',
            'locale_placeholder'    => '--- Select from available locales ---',
            'body_type' => [
                'repeater'          => 'Repeater Blocks',
                'richeditor'        => 'Rich Editor',
                'markdown'          => 'Markdown Editor',
                'theme_template'    => 'Choose from Theme Templates'
            ],
        ],
        'buttons' => [
            'reorder'               => 'Re-Order',
        ],
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
            'caption'       => 'Caption',
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
            'above'         => 'Above',
            'below'         => 'Below',
            'inline_left'   => 'Inline Left',
            'inline_right'  => 'Inline Right',
        ]
    ],
    'seo' => [
        'tabs' => [
            'general'   => 'General',
            'search'    => 'Search',
            'opengraph' => 'Facebook/Opengraph',
            'twitter'   => 'Twitter Cards',
            'schema'    => 'Schema'
        ],
        'labels' => [
            'post_type'         => 'Content Type',
            'about'             => 'Content is About',
            'keywords'          => 'Keywords',
            'page_title'        => 'Page Title',
            'meta_description'  => 'Meta Description',

        ],
        // Not set in config as these are schema defined and don't need altering
        'dropdown' => [
            'article'       => 'Article',
            'blogPosting'   => 'Blog Post',
            'newsArticle'   => 'News Article',
        ],
        'schema'    => [
            'names' => [
                'thing' => 'Thing',
            ],
            'descriptions' => [
                'thing' => 'The most basic of schema types',
            ],
            'tabs' => [
                'about'     => 'About',
                'mentions'  => 'Mentions',
            ],
            'labels' => [
                'thing_name'        => 'Thing Name',
                'thing_description' => 'Thing Description',
                'thing_image'       => 'Thing Image',
                'thing_same_as'     => 'Thing Same As'
            ],
            'comments' => [
                'thing_same_as'     => 'A wikipedia link makes sense here',
            ],
        ],
    ],

    'settings' => [
        'tabs' => [
            'publisher'     => 'Publisher',
            'posts'         => 'Posts',
            'categories'    => 'Categories',
            'tags'          => 'Tags',
            'users'         => 'Users',
            'cms_layouts'   => 'CMS Layouts',
            'translation'   => 'Translation',
            'rss'           => 'RSS',
        ],
        'labels' => [
            'settings'                  => 'Settings',
            'page_label'                => 'Dynamedia Posts Settings',
            'page_description'          => 'Manage settings for the posts.',
            'publisher_name'            => 'Publisher Name',
            'publisher_type'            => 'Publisher Type',
            'publisher_url'             => 'Publisher URL',
            'publisher_logo'            => 'Publisher Logo',
            'posts_sort_order'          => 'Posts Sort Order',
            'default_posts_sort_order'  => 'Default Posts Sort Order',
            'posts_per_page'            => 'Posts Per Page',
            'include_subcategories'     => 'List Posts from Sub-Categories',
            'post_page'                 => 'Post Display Page',
            'category_page'             => 'Category Display Page',
            'tag_page'                  => 'Tag Display Page',
            'user_page'                 => 'User Display Page',
            'default_post_layout'       => 'Default Post Layout',
            'default_category_layout'   => 'Default Category Layout',
            'default_tag_layout'        => 'Default Tag Layout',
            'rss_title'                 => 'Main RSS Feed Title',
            'rss_description'           => 'Main RSS Feed Description',
            'rss_posts_per_feed'        => 'Posts per Feed',

        ],
        'comments' => [
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
            'properties'    => [
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
                'sort_order' => [
                    'title'         => 'Sort Order',
                    'description'   => 'Sort the fetched posts',
                ]
            ],
        ],
        'main_rss'  => [
            'name'          => 'MainRss Component',
            'description'   => 'Provides a method to dictate the url for the main rss feed',
        ],
        'main_sitemap'  => [
            'name'          => 'MainSitemap Component',
            'description'   => 'Provides a method to dictate the url for the main sitemap',
        ],
    ],

    // Access Control
    'acl'   => [
        'permissions_settings' => [
            'tabs' => [
                'posts'     => 'Posts'
            ],
            'labels' => [
                'access_plugin'                 => 'Access Posts Plugin',
                'create_posts'                  => 'Create Posts',
                'categorize_posts'              => 'Categorize Posts',
                'tag_posts'                     => 'Tag Posts',
                'set_post_layout'               => 'Set Post Layout',
                'publish_own_posts'             => 'Publish Own Posts',
                'unpublish_own_posts'           => 'Unpublish Own Posts',
                'edit_own_published_posts'      => 'Edit Own Published Posts',
                'delete_own_unpublished_posts'  => 'Delete Own Unpublished Posts',
                'delete_own_published_posts'    => 'Delete Own Published Posts',
                'publish_all_posts'             => 'Publish All Posts',
                'unpublish_all_posts'           => 'Unpublish All Posts',
                'edit_all_unpublished_posts'    => 'Edit All Unpublished Posts',
                'edit_all_published_posts'      => 'Edit All Published Posts',
                'delete_all_unpublished_posts'  => 'Delete All Unpublished Posts',
                'delete_all_published_posts'    => 'Delete All Published Posts',
                'assign_posts'                  => 'Assign Posts to User',
                'view_categories'               => 'View Categories',
                'manage_categories'             => 'Manage Categories',
                'view_tags'                     => 'View Tags',
                'manage_tags'                   => 'Manage Tags',
                'manage_translations'           => 'Manage Translations',
                'manage_slugs'                  => 'Manage Slugs',
                'view_settings'                 => 'View Settings',
                'manage_settings'               => 'Manage Setttings',
            ],
        ],
        'error' => [
            'manage_categories' => 'Insufficient permissions to manage categories',
            'edit_post'         => 'Insufficient permissions to edit :post',
            'publish_post'      => 'Insufficient permissions to publish :post',
            'unpublish_post'    => 'Insufficient permissions to unpublish :post',
            'delete_post'       => 'Insufficient permissions to delete :post',
            'edit_tag'          => 'Insufficient permissions to edit :tag',
            'delete_tag'        => 'Insufficient permissions to publish :tag',
            'manage_settings'   => 'Insufficient permissions to manage settings',
        ]
    ],

    'validation' => [
        'slug_unavailable'  => 'The slug \':slug\' is not available'
    ],

    'static_pages' => [
        'tabs' => [
            'banner_image'   => 'Banner Image',
            'social_images'  => 'Social Images',
            'seo'            => 'SEO'
        ],
        'labels' => [
            'default'        => 'Default Image',
            'responsive'     => 'Responsive Images',
            'facebook_image' => 'Facebook Sharing Image',
            'twitter_image'  => 'Twitter Sharing Image',
            'seo_page_about' => 'Page is about',
            'seo_keywords'   => 'Keywords',
            'seo_search_description' => 'Description for search engines',
            'seo_opengraph_title' => 'Title for Facebook & Open Graph sharing',
            'seo_opengraph_description' => 'Description for Facebook & Open Graph sharing',
            'seo_twitter_title' => 'Title for Twitter sharing',
            'seo_twitter_description' => 'Description for Twitter sharing',
        ],
        'menu_types' => [
            'category'          => 'Posts: A Single Category',
            'all_categories'    => 'Posts: All Categories',
            'post'              => 'Posts: A Single Post',
            'all_posts'         => 'Posts: All Posts',
            'tag'               => 'Posts: A Single Tag',
            'all_tags'          => 'Posts: All Tags',
            'category_posts'    => 'Posts: Posts From Category',
            'tag_posts'         => 'Posts: Posts With Tag'
        ]
    ],

    'backend_user' => [
        'tabs' => [
            'profile'   => 'Profile',
            'biography' => 'Biography',
        ],
        'labels' => [
            'username'          => 'Username',
            'twitter_handle'    => 'Twitter Username',
            'instagram_handle'  => 'Instagram Username',
            'facebook_handle'   => 'Facebook Username',
            'website_url'       => 'Website URL',
            'mini_biography'    => 'Mini Biography',
            'full_biography'    => 'Full Biography',
        ],
        'placeholders' => [
            'handle'    => 'YourUsername',
            'at_handle' => '@YourUsername',
            'website'   => 'https://yourwebsite.com',
        ],
    ],

    'theme_form_config' => [
        'tabs' => [
            'branding'      => 'Branding',
            'social'        => 'Social',
            'images'        => 'Images',
            'site_operator' => 'Site Operator',
            'detail'        => 'Detail',
            'address'       => 'Address',
            'contact'       => 'Contact',
        ],
        'labels' => [
            'site_brand'        => 'Site Brand',
            'site_name'         => 'Site Name',
            'site_description'  => 'Site Description',
            'append_to_title'   => 'Append to Title',
            'facebook_url'      => 'Facebook URL',
            'facebook_app_id'   => 'Facebook App ID',
            'twitter_handle'    => 'Twitter Username',
            'operator_type'     => 'Operator Type',
            'name'              => 'Name',
            'logo'              => 'Logo',
            'address_street'    => 'Street Address',
            'address_city'      => 'City',
            'address_region'    => 'Region',
            'address_postcode'  => 'Postal code',
            'address_country'   => 'Country',
            'address_latitude'  => 'Latitude',
            'address_longitude' => 'Longitude',
            'contact_telephone' => 'Telephone',
            'contact_email'     => 'Email',
            'contact_fax'       => 'Fax',

        ],
        'placeholders' => [
            'facebook_url'      => 'https://facebook.com/YourUsername',
            'facebook_app_id'   => '0123456789',
            'twitter_handle'    => '@YourUsername',
        ],
        'options' => [
            'append_title_none'         => 'None',
            'append_title_name'         => 'Site Name',
            'append_title_brand'        => 'Site Brand',
            'append_title_name_brand'   => 'Site Name & Brand',
            'person'                    => 'Person / Individual',
            'organization'              => 'Organization'
        ],
    ],
    'aboutposts' => [
        'page_title'    => 'About Posts',
    ]
];
