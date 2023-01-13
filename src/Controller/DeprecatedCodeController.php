<?php

namespace Drupal\deprecation_checker\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DeprecatedCodeController extends ControllerBase {

  protected const PAGE_COUNT = 20;

  /**
   * The dependency injection container.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected $container;

  /**
   * Constructs a new instance of DeprecatedCodeController.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The dependency injection container.
   */
  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container);
  }

  public function list() {

    $form = \Drupal::formBuilder()
      ->getForm('Drupal\deprecated_code_finder\Form\FilterForm');
    $selected_module = $this->container->get('request_stack')
      ->getCurrentRequest()
      ->get('module_name');
    // Get all modules
    $modules = $this->container->get('module_handler')->getModuleList();
    $deprecated_code = [];

    // Iterate through each module
    foreach ($modules as $module => $module_data) {
      if (!$selected_module || $module === $selected_module) {
        // Get the path of the module
        $module_path = $this->container->get('module_handler')
          ->getModule($module)
          ->getPath();

        // Search for deprecated code
        $output = shell_exec("grep -r --include='*.php' --include='*.module' --include='*.install' 'deprecated' $module_path");

        // If deprecated code is found, add it to the list
        if ($output) {
          $lines = explode(PHP_EOL, $output);
          foreach ($lines as $line) {
            if (!empty($line)) {
              $datum = explode(':', $line);
              $module_path = $datum[0] ?? $module_path;
              $deprecated_code[] = [
                'module' => $module,
                'code' => $line,
                'file_path' => $module_path,
              ];
            }
          }
        }
      }
    }
    $header = [
      $this->t('Deprecated code'),
      $this->t('Module / feature'),
      $this->t('File path'),
    ];
    $table = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $deprecated_code,
    ];
    $pager = $this->container->get('pager.manager')
      ->createPager(count($deprecated_code), self::PAGE_COUNT);
    $table['#rows'] = array_slice($deprecated_code, $pager->getCurrentPage() * self::PAGE_COUNT, self::PAGE_COUNT);
    return [
      'form' => $form,
      'table' => $table,
      'pager' => ['#type' => 'pager'],
    ];
  }

}
