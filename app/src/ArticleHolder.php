<?php

namespace SilverStripe\Lessons;

use Page;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;

class ArticleHolder extends Page
{
  private static $has_many = [
    'Categories' => ArticleCategory::class,
  ];

  private static $allowed_children = [
    ArticlePage::class
  ];

  public function Regions()
  {
    $page = RegionsPage::get()->first();

    if ($page) {
      return $page->Regions();
    }
  }

  public function getCMSFields()
  {
    $fields = parent::getCMSFields();
    $fields->addFieldToTab('Root.Categoies', GridField::create(
      'Categories',
      'Article categories',
      $this->Categories(),
      GridFieldConfig_RecordEditor::create()
    ));

    return $fields;
  }
}
