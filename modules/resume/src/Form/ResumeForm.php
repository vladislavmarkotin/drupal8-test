<?php
/*
 * @file
 * Contains \Drupal\resume\Form\ResumeForm.
 */
namespace Drupal\resume\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Serialization\Json as jsonSerializer;

class ResumeForm extends FormBase {

    private $log_flag = 0;
    private $email;
    private $first_name;
    private $last_name;
    private $subject;
    private $comment;

  private function toHubSpot($post_json){
      $hapikey = "02ae4b40-b305-4424-8d08-25274c7b94b6";
      $endpoint = 'https://api.hubapi.com/contacts/v1/contact?hapikey=' . $hapikey;
      $ch = @curl_init();
      @curl_setopt($ch, CURLOPT_POST, true);
      @curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
      @curl_setopt($ch, CURLOPT_URL, $endpoint);
      @curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $response = @curl_exec($ch);
      $status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $curl_errors = curl_error($ch);
      @curl_close($ch);
      echo "curl Errors: " . $curl_errors;
      echo "\nStatus code: " . $status_code;
      echo "\nResponse: " . $response;

  }

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

      $json_form = json_encode($form_state->getValues());
      $this->email = $form_state->getValue('email');
      $this->first_name = $form_state->getValue('first_name');
      $this->last_name = $form_state->getValue('last_name');
      $this->subject = $form_state->getValue('subject');
      $this->comment = $form_state->getValue('message');
      $test_json = "{
        \"properties\": [
          {
            \"property\": \"email\",
            \"value\": \"$this->email\"
          },
          {
            \"property\": \"firstname\",
            \"value\": \"$this->first_name\"
          },
          {
            \"property\": \"lastname\",
            \"value\": \"$this->last_name\"
          },
          {
            \"property\": \"website\",
            \"value\": \"http://hubspot.com\"
          },
          {
            \"property\": \"company\",
            \"value\": \"HubSpot2\"
          },
          {
            \"property\": \"phone\",
            \"value\": \"555-222-2323\"
          },
          {
            \"property\": \"address\",
            \"value\": \"25 First Street\"
          },
          {
            \"property\": \"city\",
            \"value\": \"Cambridge\"
          },
          {
            \"property\": \"state\",
            \"value\": \"MA\"
          },
          {
            \"property\": \"zip\",
            \"value\": \"02139\"
          }
        ]
      }";

      $this->toHubSpot($test_json);

      //$test_json2 = new jsonSerializer();
      //$test_json2->encode($json_form);
      //die($test_json);
      /*foreach ($form_state->getValues() as $key => $value) {
        drupal_set_message($key . ': ' . $value);
      }*/

    //Должно пройти 5 проверок
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