<?php namespace Dynamedia\Posts\Updates;

use Backend\Models\User;
use Winter\Storm\Database\Updates\Seeder;
use Carbon\Carbon;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Category;
use Dynamedia\Posts\Models\Tag;
use Dynamedia\Posts\Models\Profile;

class SeedAllTables extends Seeder
{

    public function run()
    {
        Category::create([
            'name' => 'Uncategorized',
            'slug' => 'uncategorized',
            'excerpt' => 'This is the excerpt for the \'Uncategorized\' category. You should probably write a nice introduction here.',
            'body' => [
                [
                    'block' => [
                        'sId' => 'first',
                        'heading' => 'A content block',
                        'content'=> 'Construct your category pages using as many or as few blocks as you like!',
                    ],
                    '_group' => 'section'
                ]
            ],
        ]);

        Tag::create([
            'name' => 'BlogPost',
            'slug' => 'blog-post',
            'is_approved' => true,
            'excerpt' => 'This is the excerpt for the \'BlogPost\' tag. You should probably write a nice introduction here.',
            'body' => [
                [
                    'block' => [
                        'sId' => 'first',
                        'heading' => 'A content block',
                        'content'=> 'Construct your tag pages using as many or as few blocks as you like!',
                    ],
                    '_group' => 'section'
                ]
            ],
        ]);

        $posts = [
            'First',
            'Second',
            'Third',
            'Fourth',
            'Fifth',
            'Sixth',
            'Seventh'
        ];
        foreach ($posts as $item) {
            $post = Post::create([
                'title' => "$item Blog Post",
                'slug' => strtolower($item) . "-blog-post",
                'excerpt' => 'This is the excerpt for your ' . strtolower($item) . ' post. You should probably write a nice introduction here.',
                'is_published' => true,
                'author_id' => User::first()->id,
                'editor_id' => User::first()->id,
                'show_contents' => true,
                'primary_category_id' => Category::first()->id,
                'body' => [
                    [
                        'block' => [
                            'sId' => 'first',
                            'in_contents' => true,
                            'heading' => 'A content block',
                            'content' => 'Construct your posts using as many or as few blocks as you like!',
                        ],
                        '_group' => 'section'
                    ],
                    [
                        'block' => [
                            'sId' => 'second',
                            'in_contents' => true,
                            'heading' => 'Another content block',
                            'content' => 'Construct your posts using as many or as few blocks as you like!',
                        ],
                        '_group' => 'section'
                    ]
                ],
            ]);
            $post->tags()->attach(Tag::first()->id);
        }
    }
}
