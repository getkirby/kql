<?php

use Kirby\Cms\Page;

/**
 * Page models outside the `Kirby` namespace, whose
 * class names KQL cannot map to a built-in interceptor
 */
class BasePage extends Page
{
}

class NewsPage extends BasePage
{
}
