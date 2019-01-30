<?php
/*
 * @file
 * Contains \Drupal\resume\Form\ResumeForm.
 */
namespace Drupal\resume\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class ResumeForm extends FormBase {

  private $log_flag = 0;

  private function checkLength($arg, $size){
      if ( strlen($arg) > $size )
        return 1;
      return 0;
  }

  //------------------------------------------------------

  private function checkEmail($email, FormStateInterface $form_state){
    if (! (filter_var($email, FILTER_VALIDATE_EMAIL)  ) ) {
      $form_state->setErrorByName('email', $this->t('Email is wrong!'));

    }
    else $this->log_flag++;
  }

  private function checkName($name, FormStateInterface $form_state){
    if (! $this->checkLength($name, 2) )  {
      $form_state->setErrorByName('first_name', $this->t('Name is wrong!'));

    }
    else $this->log_flag++;
  }

  private function checkLastName($lname, FormStateInterface $form_state){
    if (! $this->checkLength($lname, 3) )  {
      $form_state->setErrorByName('last_name', $this->t('Last name is wrong!'));

    }
    else $this->log_flag++;
  }

  private function checkSubject($subject, FormStateInterface $form_state){
    if (! $this->checkLength($subject, 1) )  {
      $form_state->setErrorByName('subject', $this->t('Subject is wrong!'));

    }
    else $this->log_flag++;
  }

  private function checkMessage($text, FormStateInterface $form_state){
    if (! $this->checkLength($text, 1) )  {
      $form_state->setErrorByName('message', $this->t('Message is wrong!'));

    }
    else $this->log_flag++;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'resume_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['first_name'] = array(
      '#type' => 'textfield',
      '#title' => t('First Name:'),
      '#required' => TRUE,
    );

    $form['last_name'] = array(
        '#type' => 'textfield',
        '#title' => t('Last Name:'),
        '#required' => TRUE,
    );

    $form['email'] = array(
      '#type' => 'email',
      '#title' => t('Email:'),
      '#required' => TRUE,
    );

    $form['subject'] = array (
      '#type' => 'textfield',
      '#title' => t('Subject'),
    );

    $form['message'] = array (
      '#type' => 'textarea',
      '#title' => t('Message'),
      '#required' => TRUE,
    );

    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
    public function validateForm(array &$form, FormStateInterface $form_state) {

      //die("Test Stop");
      $this->checkEmail($form_state->getValue('email'),$form_state);
      $this->checkName($form_state->getValue('first_name'),$form_state);
      $this->checkLastName($form_state->getValue('last_name'), $form_state);
      $this->checkSubject($form_state->getValue('subject'), $form_state);
      $this->checkMessage($form_state->getValue('message'), $form_state);
    }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

      foreach ($form_state->getValues() as $key => $value) {
        drupal_set_message($key . ': ' . $value);
      }

      if ($this->log_flag == 5){

        $message = "Email: ".$form_state->getValue('email').
            " Message: ".$form_state->getValue('message');

        // Logs a notice
        \Drupal::logger('resume')->notice($message);
        // Logs an error
        \Drupal::logger('resume')->error($message);

      }

   }
}