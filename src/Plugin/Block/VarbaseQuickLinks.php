<?php

namespace Drupal\varbase_dashboards\Plugin\Block;

use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides a 'Varbase Quick Links'.
 *
 * @Block(
 * id = "varbase_quick_links",
 * admin_label = @Translation("Quick Links"),
 * category = @Translation("Dashboard")
 * )
 */
class VarbaseQuickLinks extends BlockBase implements BlockPluginInterface, ContainerFactoryPluginInterface {

  /**
   * The renderer service.
   *
   * @var Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The module manager service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Creates a VarbaseQuickLinks block instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RendererInterface $renderer, ModuleHandlerInterface $module_handler) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->renderer = $renderer;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('renderer'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    if ($this->moduleHandler->moduleExists('menu_ui')) {
      $links[] = Link::fromTextAndUrl($this->t('Manage menus'), Url::fromRoute('entity.menu.collection'));
    }
    if ($this->moduleHandler->moduleExists('taxonomy')) {
      $links[] = Link::fromTextAndUrl($this->t('Manage taxonomy'), Url::fromRoute('entity.taxonomy_vocabulary.collection'));
    }

    $links[] = Link::fromTextAndUrl($this->t('Manage users'), Url::fromRoute('entity.user.collection'));

    $body_data = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#items' => $links,
    ];

    $markup_data = $this->renderer->render($body_data);

    return [
      '#type' => 'markup',
      '#markup' => $markup_data,
    ];
  }

}
