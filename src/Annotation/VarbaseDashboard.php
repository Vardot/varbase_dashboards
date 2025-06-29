<?php

namespace Drupal\varbase_dashboards\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Dashboard item annotation object.
 *
 * @see \Drupal\varbase_dashboards\Plugin\VarbaseDashboardManager
 * @see plugin_api
 *
 * @Annotation
 */
class VarbaseDashboard extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * Category of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $category;

}
