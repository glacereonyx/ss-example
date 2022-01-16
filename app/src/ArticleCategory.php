<?php

namespace SilverStripe\Lessons;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;

class ArticleCategory extends DataObject
{

  private static $table_name = 'ArticleCategory';
  private static $db = [
    'Title' => 'Varchar',
  ];

  private static $has_one = [
    'ArticleHolder' => ArticleHolder::class,
  ];

  private static $belongs_many_many = [ // Not required but will provide a method that allows us to get a list of all parents (all article pages that have this category)
    'Articles' => ArticlePage::class, 
  ];

  public function getCMSFields()
  {
    return FieldList::create(
      TextField::create('Title')
    );
  }
}