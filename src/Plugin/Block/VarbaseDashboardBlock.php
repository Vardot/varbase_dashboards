<?php

namespace Drupal\varbase_dashboards\Plugin\Block;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a varbase dashboard block.
 *
 * @Block(
 *   id = "dashboards_block",
 *   admin_label = @Translation("Dashboard block"),
 *   category = @Translation("Dashboard"),
 *   deriver = "Drupal\varbase_dashboards\Plugin\Derivative\VarbaseDashboardBlock"
 * )
 */
class VarbaseDashboardBlock extends BlockBase implements ContainerFactoryPluginInterface, BlockPluginInterface {

  /**
   * Base plugin.
   *
   * @var \Drupal\varbase_dashboards\Plugin\VarbaseDashboardBase
   */
  protected $basePlugin;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    PluginManagerInterface $plugin_manager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $id = explode(':', $this->getDerivativeId())[1];
    $this->basePlugin = $plugin_manager->createInstance($id);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.varbase_dashboard')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    $this->basePlugin->validateForm($form, $form_state, $this->getConfiguration());
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    return $this->basePlugin->buildSettingsForm($form, $form_state, $this->getConfiguration());
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $configuration = $this->getConfiguration();
    parent::blockSubmit($form, $form_state);
    $this->basePlugin->massageFormValues($form, $form_state, $configuration);
    $values = $form_state->getValues();
    $this->configuration = array_merge($configuration, $values);
  }

  /**
   * Set if this block is in preview.
   */
  public function build() {
    $renderArray = [];
    $contentRenderArray = $this->basePlugin->buildRenderArray($this->getConfiguration());

    if ($contentRenderArray !== []) {
      $renderArray = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['varbase-dashboard-component'],
        ],
        'content' => $contentRenderArray,
      ];
    }

    return $renderArray;
  }

}
