<?php

namespace SilverStripe\Lessons;

use SilverStripe\Forms\DateField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Assets\Image;
use SilverStripe\Assets\File;
use SilverStripe\AssetAdmin\Forms\UploadField;

use Page;
use SilverStripe\Forms\CheckboxSetField;

class ArticlePage extends Page
{
  // Dissallow adding this page as a root page.
  private static $can_be_root = false;

  // Adds fields to the Database.
  private static $table_name = 'ArticlePage';
  private static $db = [
    'Date' => 'Date',
    'Teaser' => 'Text',
    'ArticleAuthor' => 'Varchar', // Author seems like a reserved keyword so added "Article"
  ];

  private static $has_one = [
    'PhotoA' => Image::class,
    'Brochure' => File::class,
  ];

  private static $has_many = [ // DO not forget this
    'Comments' => ArticleComment::class,
  ];

  private static $many_many = [ // Ensuresthat the page can use the ArticleCategory as a manymany relationship.
    'Categories' => ArticleCategory::class,
  ];

  // Ensures that the Photo and Brochure respect the publication state of the page.
  // Without this, the files would need to be published manually.
  private static $owns = [
    'PhotoA',
    'Brochure',
  ];

  // The fields shown on the CMS page.
  public function getCMSFields()
  {
    $fields = parent::getCMSFields(); // Gets all fields from the parent class. Without this the fields list would be empty by default.

    $fields->addFieldsToTab('Root.Main', DateField::create('Date', 'Date of article'), 'Content'); // Field is inserted before the 'content' field.
    $fields->addFieldToTab('Root.Main', TextareaField::create('Teaser')
      ->setDescription('This is the summary that appears on the article list page.'), 'Content');
    $fields->addFieldToTab('Root.Main', TextField::create('ArticleAuthor', 'Author of article'), 'Content');

    // File uploads.
    $fields->addFieldToTab('Root.Attachments', $photo = UploadField::create('PhotoA'));
    $fields->addFieldToTab('Root.Attachments', $brochure = UploadField::create('Brochure', 'Travel brochure, optional (PDF only)'));

    $photo->setFolderName('travel-photos');
    $brochure
      ->setFolderName('travel-brochures')
      ->getValidator()->setAllowedExtensions(['pdf']);


    $fields->addFieldsToTab('Root.Categories', CheckboxSetField::create( //You may use ListBoxField when there are lots of items.
      'Categories',
      'Selected categories', //Label for the checkboxes
      $this->Parent()->Categories()->map('ID', 'Title') // Gets the Categories from ArticleHolder (parent), and maps the ID and Title to the checkboxes.
    ));

    return $fields;
  }

  public function CategoriesList()
  {
    if ($this->Categories()->exists()) {
      return implode(', ', $this->Categories()->column('Title'));
    }

    return null;
  }
}
