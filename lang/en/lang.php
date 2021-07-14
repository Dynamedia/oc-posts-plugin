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
            'is_published'      => 'Published?',
            'published_at'      => 'Published At',
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
            'slug'          => 'Slug',
            'excerpt'       => 'Excerpt',
            'seo'           => 'SEO',
            'posts'         => 'Posts',
            'cms_layout'    => 'CMS Layout',
            'author'        => 'Author',
            'editor'        => 'Editor',
            'block_heading' => 'Section Heading',
            'block_id'      => 'Section ID',
            'block_content' => 'Section Content'
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

        ],
        // Not set in config as these are schema defined and don't need altering
        'dropdown' => [
            'article'       => 'dynamedia.posts::lang.seo.dropdown.article',
            'blogPosting'   => 'dynamedia.posts::lang.seo.dropdown.blogPosting',
            'newsArticle'   => 'dynamedia.posts::lang.seo.dropdown.newsArticle',
        ]
    ]
];
