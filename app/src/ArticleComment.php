<?php

namespace SilverStripe\Lessons;

use SilverStripe\ORM\DataObject;

class ArticleComment extends DataObject
{
  private static $table_name = 'ArticleComment';
  private static $db = [
    'Name' => 'Varchar',
    'Email' => 'Varchar',
    'Comment' => 'Text'
  ];

  private static $has_one = [ // Don't forget the $has_many relationship in the ArticlePage
    'ArticlePage' => ArticlePage::class,
  ];
}
