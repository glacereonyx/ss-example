<?php

namespace SilverStripe\Lessons;

use PageController;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\PaginatedList;

class ArticleHolderController extends PageController
{
  private static $allowed_actions = [
    'category',
    'region',
    'date'
  ];

  protected $articleList;

  protected function init()
  {
    parent::init();

    $this->articleList = ArticlePage::get()->filter([
      'ParentID' => $this->ID
    ])->sort('Date DESC');
  }

  public function PaginatedArticles($num = 1)
  {
    return PaginatedList::create(
      $this->articleList,
      $this->getRequest()
    )->setPageLength($num);
  }

  public function category(HTTPRequest $r)
  {
    // Get a category with the id from the request
    $category = ArticleCategory::get()->byID(
      $r->param('ID')
    );

    // If there's no category, return an error
    if (!$category) {
      return $this->httpError(404, 'That category was not found');
    }

    // Filter the article list by the category id
    $this->articleList = $this->articleList->filter([
      'Categories.ID' => $category->ID
    ]);

    // Return the selected category to the template
    return [
      'SelectedCategory' => $category
    ];
  }

  public function region(HTTPRequest $r)
  {
    $region = Region::get()->byID(
      $r->param('ID')
    );

    if (!$region) {
      return $this->httpError(404, 'That region was not found');
    }

    $this->articleList = $this->articleList->filter([
      'RegionID' => $region->ID
    ]);

    return [
      'SelectedRegion' => $region
    ];
  }
}
