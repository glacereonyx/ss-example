<?php

namespace SilverStripe\Lessons;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Versioned\Versioned;

class Region extends DataObject
{

  private static $table_name = 'Region';
  private static $db = [
    'Title' => 'Varchar',
    'Description' => 'Text',
  ];

  private static $has_one = [
    'Photo' => Image::class,
    'RegionsPage' => RegionsPage::class,
  ];

  private static $owns = [
    'Photo',
  ];

  private static $extensions = [
    Versioned::class,
  ];

  private static $summary_fields = [
    'GridThumbnail' => '',
    'Title' => 'Title',
    'Description' => 'Description'
  ];

  // Generates a custom thumbnail for the gridfield view
  public function getGridThumbnail()
  {
    if ($this->Photo()->exists()) {
      return $this->Photo()->ScaleWidth(100);
    }

    return "(no image)";
  }

  // private static $versioned_gridfield_extensions = false; // Adds version control to the element

  // Adds extra fields to the CMS
  public function getCMSFields()
  {
    $fields = FieldList::create(
      TextField::create('Title'),
      TextareaField::create('Description'),
      $uploader = UploadField::create('Photo')
    );

    $uploader->setFolderName('region-photos');
    $uploader->getValidator()->setAllowedExtensions(['png', 'gif', 'jpeg', 'jpg']);

    return $fields;
  }
}
