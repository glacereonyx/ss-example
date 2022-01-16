<?php

namespace SilverStripe\Lessons;

use PageController;

class HomePageController extends PageController
{

  public function LatestArticles(int $count = 3)
  {
    return ArticlePage::get()
      ->sort('Created', 'DESC')
      ->limit($count);
  }

  public function FeaturedProperties()
  {
    return Property::get()
      ->filter([
        'FeaturedOnHomepage' => true
      ])
      ->limit(6);
  }
}
