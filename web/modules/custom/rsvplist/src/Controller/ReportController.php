<?php

/**
 * @file
 * Provide site administrators with a list of all the RSVP list signups
 * so they know who is attending their events
 *
 */

 namespace Drupal\rsvplist\Controller;

 use Drupal\Core\Controller\ControllerBase;
 use Drupal\Core\Database\Database;

 class ReportController extends ControllerBase {
  /**
   * Gets and returns all RSVPs for nodes.
   * These are returned as an assosiative arry, with each row
   * cotaining the username, the node title and email of rsvp
   *
   * @return array|null
   */
  protected function load(){
    try {
      // Queries/introduction-to-dynamic-queries
      $database = \Drupal::database();
      $select_query = $database->select('rsvplist', 'r');

      // Join the user table, so we can get the entry creator's username.
      $select_query->join('users_field_data', 'u', 'r.uid = u.uid');
      //join the node table,so we can get the events name
      $select_query->join('node_field_data', 'n', 'r.nid = n.nid');

      // Select these specific fields for the output
      $select_query->addField('u', 'name', 'username');
      $select_query->addField('n', 'title');
      $select_query->addField('r', 'mail');

      $entries = $select_query->execute()->fetchAll(\PDO::FETCH_ASSOC);

      // Return the associative array
      return $entries;


    }
      catch (\Exception $e){
      // Dsplay a user-friendly error
      \Drupal::messenger()->addStatus($this->t('Unable to accesse the database at this time. Please try agai llater.'));
      \Drupal::logger('rsvplist')->debug('Load method executed.');
      return NULL;
    }
  }
  /**
   *
   * @return array
   *  Render array for the RSVPList report output
   */
  public function report() {
    $content = [];

    $content['message'] = [
      '#markup' => t('below is a list of all events RSVPs including username mail and atendee'),
    ];

    $headers = [
      t('Username'),
      t('Event'),
      t('Email'),
    ];
    $table_rows = $this->load();

    // Create the render array for rendering an HTML table
    $content['table'] = [
      '#type' => 'table',
      '#header' => $headers,
      '#rows' => $table_rows,
      '#empty' => t('No entries available')
    ];

    $content['#cache']['max-age'] = 0;

    return $content;

  }
 }
