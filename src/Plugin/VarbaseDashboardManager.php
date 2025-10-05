<?php

namespace Drupal\varbase_dashboards\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides the Varbase Dashboard plugin manager.
 */
class VarbaseDashboardManager extends DefaultPluginManager {

  /**
   * Constructs a new DashboardManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/VarbaseDashboard', $namespaces, $module_handler, 'Drupal\varbase_dashboards\Plugin\VarbaseDashboardInterface', 'Drupal\varbase_dashboards\Annotation\VarbaseDashboard');

    $this->alterInfo('varbase_dashboard_info');
    $this->setCacheBackend($cache_backend, 'varbase_dashboard_plugins');
  }

}
