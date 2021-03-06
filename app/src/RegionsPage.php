<?php

namespace SilverStripe\Lessons;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use Page;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;

class RegionsPage extends Page
{
  private static $has_many = [
    'Regions' => Region::class
  ];

  private static $owns = [
    'Regions',
  ];

  public function getCMSFields()
  {
    $fields = parent::getCMSFields();
    $fields->addFieldToTab('Root.Regions', GridField::create(
      'Regions',
      'Regions on this page',
      $this->Regions(),
      GridFieldConfig_RecordEditor::create()  // Creates the grid view of "regions"
    ));

    return $fields;
  }
}
