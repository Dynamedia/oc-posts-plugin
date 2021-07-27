<?php namespace Dynamedia\Posts\Updates;

use Backend\Models\User;
use Backend\Models\UserRole;
use Seeder;
use Dynamedia\Posts\Models\Post;
use Dynamedia\Posts\Models\Category;
use Dynamedia\Posts\Models\Tag;
use Dynamedia\Posts\Models\Profile;

class SeedAllTables extends Seeder
{

    public function run()
    {
        // Create non-system user roles - These should not automatically inherit access to CMS capabilities
        // Use developer and publisher roles for full access
        $writer = UserRole::updateOrCreate([
            'name' => 'Post Writer',
            'code' => 'post-writer',
            'description' => 'Create and edit own posts',
        ]);
        $writer->permissions = [
                'dynamedia.posts.access_plugin' => 1,
                'dynamedia.posts.create_posts' => 1,
                'dynamedia.posts.categorize_posts' => 1,
                'dynamedia.posts.tag_posts' => 1,
                'dynamedia.posts.set_layout' => 1,
                'dynamedia.posts.publish_own_posts' => 1,
                'dynamedia.posts.unpublish_own_posts' => 1,
                'dynamedia.posts.edit_own_published_posts' => 1,
                'dynamedia.posts.delete_own_unpublished_posts' => 1,
                'dynamedia.posts.delete_own_published_posts' => 1,
                'dynamedia.posts.publish_all_posts' => 1,
                'dynamedia.posts.unpublish_all_posts' => 1,
                'dynamedia.posts.edit_all_unpublished_posts' => 1,
                'dynamedia.posts.edit_all_published_posts' => 1,
                'dynamedia.posts.delete_all_unpublished_posts' => 1,
                'dynamedia.posts.delete_all_published_posts' => 1,
                'dynamedia.posts.assign_posts' => 1,
                'dynamedia.posts.view_categories' => 1,
                'dynamedia.posts.manage_categories' => 1,
                'dynamedia.posts.view_tags' => 1,
                'dynamedia.posts.manage_tags' => 1,
                'dynamedia.posts.view_settings' => 1,
                'dynamedia.posts.manage_settings' => 1,
            ];
        $writer->save();

        $guest = UserRole::updateOrCreate([
            'name' => 'Guest Post Writer',
            'code' => 'guest-post-writer',
            'description' => 'Create posts',
        ]);
        $guest->permissions = [
            'dynamedia.posts.access_plugin' => 1,
            'dynamedia.posts.create_posts' => 1,
            'dynamedia.posts.categorize_posts' => 1,
            'dynamedia.posts.tag_posts' => 1,
            'dynamedia.posts.delete_own_unpublished_posts' => 1,
            'dynamedia.posts.view_categories' => 1,
            'dynamedia.posts.view_tags' => 1,
        ];
        $guest->save();

        $editor = UserRole::updateOrCreate([
            'name' => 'Posts Editor',
            'code' => 'post-editor',
            'description' => 'Edit and publish posts',
        ]);
        $editor->permissions = [
            'dynamedia.posts.access_plugin' => 1,
            'dynamedia.posts.create_posts' => 1,
            'dynamedia.posts.categorize_posts' => 1,
            'dynamedia.posts.tag_posts' => 1,
            'dynamedia.posts.set_layout' => 1,
            'dynamedia.posts.publish_own_posts' => 1,
            'dynamedia.posts.unpublish_own_posts' => 1,
            'dynamedia.posts.edit_own_published_posts' => 1,
            'dynamedia.posts.delete_own_unpublished_posts' => 1,
            'dynamedia.posts.delete_own_published_posts' => 1,
            'dynamedia.posts.publish_all_posts' => 1,
            'dynamedia.posts.unpublish_all_posts' => 1,
            'dynamedia.posts.edit_all_unpublished_posts' => 1,
            'dynamedia.posts.edit_all_published_posts' => 1,
            'dynamedia.posts.assign_posts' => 1,
            'dynamedia.posts.view_categories' => 1,
            'dynamedia.posts.manage_categories' => 1,
            'dynamedia.posts.view_tags' => 1,
            'dynamedia.posts.manage_tags' => 1,
        ];
        $editor->save();


        // Create profiles for users
        foreach (User::all() as $user) {
            Profile::getFromUser($user);
        }

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
        ];
        foreach ($posts as $item) {
            $post = Post::create([
                'title' => "$item Blog Post",
                'slug' => strtolower($item) . "-blog-post",
                'excerpt' => 'This is the excerpt for your ' . strtolower($item) . ' post. You should probably write a nice introduction here.',
                'is_published' => true,
                'author_id' => User::first()->id,
                'editor_id' => User::first()->id,
                'show_contents' => false,
                'primary_category_id' => Category::first()->id,
                'body' => [
                    [
                        'block' => [
                            'sId' => 'first',
                            'in_contents' => true,
                            'heading' => 'A content block',
                            'content' => '
                                <p>Construct your posts using as many or as few blocks as you like!</p>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                            ',
                        ],
                        '_group' => 'section'
                    ],
                    [
                        'block' => [
                            'sId' => 'second',
                            'in_contents' => true,
                            'heading' => 'Another content block',
                            'content' => '
                                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>
                            ',
                        ],
                        '_group' => 'section'
                    ]
                ],
            ]);
            if ($post->slug == 'first-blog-post') {
                $extendBody = [
                    [
                        '_group' => 'pagebreak',
                    ],
                    [
                        'block' => [
                            'sId' => 'third',
                            'in_contents' => true,
                            'heading' => 'New page content',
                            'content' => '
                                <p>Posts can, if you want, be written over multiple pages. It\'s all handled internally so just add a pagebreak block. Easy!</p>
                            ',
                        ],
                        '_group' => 'section'
                    ]
                ];
                $post->show_contents = true;
                $post->body = array_merge($post->body, $extendBody);
                $post->save();
            }
            $post->tags()->attach(Tag::first()->id);
        }
    }
}
