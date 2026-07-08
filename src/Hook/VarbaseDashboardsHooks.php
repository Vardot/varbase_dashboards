<?php

namespace Drupal\varbase_dashboards\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Theme\ThemeManagerInterface;

/**
 * Hook implementations for the Varbase Dashboards module.
 */
class VarbaseDashboardsHooks {

  /**
   * Constructs a VarbaseDashboardsHooks object.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $currentRouteMatch
   *   The current route match.
   * @param \Drupal\Core\Theme\ThemeManagerInterface $themeManager
   *   The theme manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration factory.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   */
  public function __construct(
    protected RouteMatchInterface $currentRouteMatch,
    protected ThemeManagerInterface $themeManager,
    protected ConfigFactoryInterface $configFactory,
    protected ModuleHandlerInterface $moduleHandler,
  ) {}

  /**
   * Implements hook_page_attachments().
   */
  #[Hook('page_attachments')]
  public function pageAttachments(array &$attachments): void {
    $routers = [
      'dashboard',
      'entity.dashboard.canonical',
      'layout_builder.dashboard.view',
      'entity.dashboard.preview',
    ];

    if (in_array($this->currentRouteMatch->getRouteName(), $routers)) {
      // Attach our extra CSS for Varbase dashboard.
      $attachments['#attached']['library'][] = 'varbase_dashboards/style';
    }
  }

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme($existing, $type, $theme, $path): array {
    $theme = [
      'block__varbase_dashboard_user' => [
        'base hook' => 'block',
      ],
    ];

    if ($this->themeManager->getActiveTheme()->getName() === $this->configFactory->get('system.theme')->get('admin')) {
      $active_theme = $this->themeManager->getActiveTheme()->getName();
      $base_theme_extensions = $this->themeManager->getActiveTheme()->getBaseThemeExtensions();

      if ($active_theme === 'gin' || in_array('gin', array_keys($base_theme_extensions))) {
        foreach ($this->backEndLayoutThemes() as $layout_name) {
          $theme[$layout_name] = [
            'template' => str_replace('_', '-', $layout_name),
            'render element' => 'content',
            'base hook' => 'layout',
            'path' => $this->moduleHandler->getModule('varbase_dashboards')->getPath() . '/templates/layouts/gin',
          ];
        }
      }
      elseif ($active_theme === 'claro' || in_array('claro', array_keys($base_theme_extensions))) {
        foreach ($this->backEndLayoutThemes() as $layout_name) {
          $theme[$layout_name] = [
            'template' => str_replace('_', '-', $layout_name),
            'render element' => 'content',
            'base hook' => 'layout',
            'path' => $this->moduleHandler->getModule('varbase_dashboards')->getPath() . '/templates/layouts/claro',
          ];
        }
      }
    }

    $theme = [
      'varbase_dashboards_admin_list' => [
        'variables' => [
          'attributes' => new Attribute(),
          'list' => [],
        ],
      ],
    ];

    return $theme;
  }

  /**
   * Implements hook_theme_registry_alter().
   */
  #[Hook('theme_registry_alter')]
  public function themeRegistryAlter(&$info): void {
    if ($this->themeManager->getActiveTheme()->getName() === $this->configFactory->get('system.theme')->get('admin')) {
      $active_theme = $this->themeManager->getActiveTheme()->getName();
      $base_theme_extensions = $this->themeManager->getActiveTheme()->getBaseThemeExtensions();

      if ($active_theme === 'gin' || in_array('gin', array_keys($base_theme_extensions))) {
        foreach ($this->backEndLayoutThemes() as $layout_id) {
          $info[$layout_id]['theme path'] = $this->moduleHandler->getModule('varbase_dashboards')->getPath();
          $info[$layout_id]['path'] = $this->moduleHandler->getModule('varbase_dashboards')->getPath() . '/templates/layouts/gin';
        }
      }
      elseif ($active_theme === 'claro' || in_array('claro', array_keys($base_theme_extensions))) {
        foreach ($this->backEndLayoutThemes() as $layout_id) {
          $info[$layout_id]['theme path'] = $this->moduleHandler->getModule('varbase_dashboards')->getPath();
          $info[$layout_id]['path'] = $this->moduleHandler->getModule('varbase_dashboards')->getPath() . '/templates/layouts/claro';
        }
      }
    }

    if (
      $this->themeManager->getActiveTheme()->getName() == 'gin'
      || in_array('gin', array_keys($this->themeManager->getActiveTheme()->getBaseThemeExtensions()))
    ) {
      $info['varbase_dashboards_admin_list']['theme path'] = $this->moduleHandler->getModule('varbase_dashboards')->getPath();
      $info['varbase_dashboards_admin_list']['path'] = $this->moduleHandler->getModule('varbase_dashboards')->getPath() . '/templates/gin';
    }
  }

  /**
   * Implements hook_preprocess_HOOK() for varbase_dashboards_admin_list.
   */
  #[Hook('preprocess_varbase_dashboards_admin_list')]
  public function preprocessVarbaseDashboardsAdminList(&$variables): void {
    foreach ($variables['list'] as $key => $item) {
      $variables['list'][$key]['link_attributes'] = new Attribute([
        'href' => $variables['list'][$key]['url']->toString(),
        'title' => $variables['list'][$key]['title'],
      ]);
    }
  }

  /**
   * Checks whether a layout ID is a back-end supported layout.
   *
   * @param string $layout_id
   *   The layout plugin ID.
   *
   * @return bool
   *   TRUE if the layout is in the preset of back-end supported layouts.
   */
  protected function isInBackEndSupportedLayouts($layout_id): bool {
    return in_array($layout_id, $this->backEndSupportedLayouts());
  }

  /**
   * Back-End supported layouts.
   *
   * @return string[]
   *   The list of back-end supported layout plugin IDs.
   */
  protected function backEndSupportedLayouts(): array {
    return [
      'layout_onecol',
      'layout_twocol',
      'layout_twocol_bricks',
      'layout_threecol_25_50_25',
      'layout_threecol_33_34_33',
      'layout_twocol_section',
      'layout_threecol_section',
      'layout_fourcol_section',
      'layout_1',
      'layout_2',
      'layout_3',
    ];
  }

  /**
   * Back-End supported layout themes.
   *
   * @return string[]
   *   The list of back-end supported layout theme hook names.
   */
  protected function backEndLayoutThemes(): array {
    return [
      'layout__onecol',
      'layout__twocol',
      'layout__twocol_bricks',
      'layout__threecol_25_50_25',
      'layout__threecol_33_34_33',
      'layout__twocol_section',
      'layout__threecol_section',
      'layout__fourcol_section',
      'layouts__1',
      'layouts__2',
      'layouts__3',
    ];
  }

}
