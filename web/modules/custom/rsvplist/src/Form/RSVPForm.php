<?php

/**
 * @file
 * A form to collect an email address for RSVP details
 */

 namespace Drupal\rsvplist\Form;

 use Drupal\Core\Form\FormBase;
 use Drupal\Core\Form\FormStateInterface;

 class RSVPForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rsvplist_email_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    //get fully loaded node object of the viewed page
    $node = \Drupal::routeMatch()->getParameter('node');

    //get node id and set null where node is not available
    if(!(is_null($node)) ){
      $nid = $node->id();
    }
    else{
      //set default to 0 if not loaded
      $nid = 0;
    }

    //Establish the $form render array
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => t('Email address'),
      '#size' => 25,
      '#description' => t("We will send update to the email you provide"),
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('RSVP'),
    ];
    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];

    return $form;
  }
  /**
   * {@inheritdoc}
   */
public  function validateForm(array &$form, FormStateInterface $form_state) {
  $value = $form_state->getValue('email');
  if(!(\Drupal::service('email.validator')->isValid($value)) ){
    $form_state->setErrorByName('email',
      $this->t('It appears that %mail is not a valid email. Please try again', ['%mail' => $value]));
  }
}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state ) {
    // $submitted_email = $form_state->getValue('email');
    // $this->messenger()->addMessage(t("My form works, You entered @entry.", ['@entry' => $submitted_email]));
    try {
      $uid = \Drupal::currentUser()->id();

      //to load a full user object entity we use the following
      $full_user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

      //to obtain values as entered in the form
      $nid = $form_state->getValue('nid');
      $email = $form_state->getValue('email');

      $current_time = \Drupal::time()->getRequestTime();

      // Saving the values in my database
      // Start to build a query builder object $query
      $query = \Drupal::database()->insert('rsvplist');

      //specify the fields that the query will insert into
      $query->fields([
        'uid',
        'nid',
        'mail',
        'created',
      ]);

      // Set the values of the fields we selected
      $query->values([
        $uid,
        $nid,
        $email,
        $current_time,
      ]);

      // Execute the query
  
      // Display sucess messege
      \Drupal::messenger()->addMessage(t('Thank you for your RSVP, you are on the event list'));

    } catch (\Exception $e) {
      \Drupal::messenger()->addError($this->t('An error occurred: @message', ['@message' => $e->getMessage()]));

    }
  }
 }


