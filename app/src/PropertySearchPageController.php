<?php

namespace SilverStripe\Lessons;

use PageController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FormAction;
use SilverStripe\ORM\ArrayLib;
use SilverStripe\ORM\PaginatedList;

class PropertySearchPageController extends PageController
{
  public function index(HTTPRequest $request)
  {
    $properties = Property::get();

    // Filter based on keywords
    if ($search = $request->getVar('Keywords')) {
      $properties = $properties->filter(array(
        'Title:PartialMatch' => $search
      ));
    }

    // Filter based on start and end dates
    if ($arrival = $request->getVar('ArrivalDate')) {
      $arrivalStamp = strtotime($arrival);
      $nightAdder = '+' . $request->getVar('Nights') . ' days';
      $startDate = date('Y-m-d', $arrivalStamp);
      $endDate = date('Y-m-d', strtotime($nightAdder, $arrivalStamp));

      $properties = $properties->filter([
        'AvailableStart:GreaterThanOrEqual' => $startDate,
        'AvailableEnd:LessThanOrEqual' => $endDate
      ]);
    }

    // Other filters
    $filters = [
      ['Bedrooms', 'Bedrooms', 'GreaterThanOrEqual'],
      ['Bathrooms', 'Bathrooms', 'GreaterThanOrEqual'],
      ['MinPrice', 'PricePerNight', 'GreaterThanOrEqual'],
      ['MaxPrice', 'PricePerNight', 'LessThanOrEqual'],
    ];

    foreach ($filters as $filterKeys) {
      list($getVar, $field, $filter) = $filterKeys;
      if ($value = $request->getVar($getVar)) {
        $properties = $properties->filter([
          "{$field}:{$filter}" => $value
        ]);
      }
    }

    // Adds pagintation support to the filtered properties
    // Remember, the SQL query is lazy loaded, meaning it does not run until the page requests the data.
    $paginatedProperties = PaginatedList::create(
      $properties,
      $request
    )
      ->setPageLength(1)
      ->setPaginationGetVar('s');
    // Sets the amount of page numbers shown surrounding the pagination.
    // $paginatedProperties->PaginationSummary(1);

    return [
      'Results' => $paginatedProperties,
    ];
  }
  public function PropertySearchForm()
  {
    $nights = [];
    foreach (range(1, 14) as $i) {
      $nights[$i] = "$i night" . (($i > 1) ? 's' : '');
    }
    $prices = [];
    foreach (range(100, 1000, 50) as $i) {
      $prices[$i] = '$' . $i;
    }

    $form = Form::create(
      $this,
      __FUNCTION__,
      FieldList::create(
        TextField::create('Keywords')
          ->setAttribute('placeholder', 'City, State, Country, etc...')
          ->addExtraClass('form-control'),
        TextField::create('ArrivalDate', 'Arrive on...')
          ->setAttribute('data-datepicker', true)
          ->setAttribute('data-date-format', 'DD-MM-YYYY')
          ->addExtraClass('form-control'),
        DropdownField::create('Nights', 'Stay for...')
          ->setSource($nights)
          ->addExtraClass('form-control'),
        DropdownField::create('Bedrooms')
          ->setSource(ArrayLib::valuekey(range(1, 5)))
          ->addExtraClass('form-control'),
        DropdownField::create('Bathrooms')
          ->setSource(ArrayLib::valuekey(range(1, 5)))
          ->addExtraClass('form-control'),
        DropdownField::create('MinPrice', 'Min. price')
          ->setEmptyString('-- any --')
          ->setSource($prices)
          ->addExtraClass('form-control'),
        DropdownField::create('MaxPrice', 'Max. price')
          ->setEmptyString('-- any --')
          ->setSource($prices)
          ->addExtraClass('form-control')
      ),
      FieldList::create(
        FormAction::create('doPropertySearch', 'Search')
          ->addExtraClass('btn-lg btn-fullcolor')
      )
    );

    $form->setFormMethod('GET')
      ->setFormAction($this->Link())
      ->disableSecurityToken()
      ->loadDataFrom($this->request->getVars()); //We do this because otherwise the user couldn't share the search link!

    return $form;
  }
}
