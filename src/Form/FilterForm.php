<?php

namespace Drupal\deprecated_code_finder\Form;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Http\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FilterForm extends FormBase {

  /**
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * @var \Drupal\Core\Http\RequestStack
   */
  protected $requestStack;

  public function __construct(ModuleHandlerInterface $module_handler, RequestStack $request) {
    $this->moduleHandler = $module_handler;
    $this->requestStack = $request;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('module_handler'),
      $container->get('request_stack')
    );
  }

  public function getFormId() {
    return 'deprecated_code_finder_filter_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $options = array_combine(array_keys($this->moduleHandler->getModuleList()), array_keys($this->moduleHandler->getModuleList()));
    ksort($options);
    $form['module_name'] = [
      '#type' => 'select',
      '#title' => $this->t('Module'),
      '#options' => $options,
      '#empty_option' => $this->t('- All modules -'),
      '#attributes' => ['class' => ['form-control']],
      '#default_value' => $this->requestStack->getCurrentRequest()->get('module_name'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Filter'),
      '#attributes' => ['class' => ['button--primary']],
    ];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $selected_module = $form_state->getValue('module_name');
    $form_state->setRedirect('deprecated_code.list', ['module_name' => $selected_module]);
  }

}
