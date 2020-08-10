<?php

namespace Drupal\form\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Psr\Log\LoggerInterface;
use Drupal\file\Entity\File;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "example_get_rest_resource",
 *   label = @Translation("Example get rest resource"),
 *   uri_paths = {
 *     "canonical" = "/example-rest/{date}"
 *   }
 * )
 */
 class ExampleGetRestResource extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a Drupal\rest\Plugin\ResourceBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('form'),
      $container->get('current_user')
    );
  }

 /**
   * Responds to GET requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
 public function get($pub = NULL) {
    $json_array = array(
      'data' => array()
    );
    if ($date) {
    	
    $nids = \Drupal::entityQuery('node')->condition('type','api')->condition('created', $date, '<=')->execute();
    $nodes =  \Drupal\node\Entity\Node::loadMultiple($nids);

    $data = array();
    
    foreach ($nodes as $node) {
  
      $fid = ($node->get('field_image_api')->isEmpty() ? 0 : $node->get('field_image_api')->getValue()[0]['target_id']);
         $data[] = [
        'fid'=> $fid,
        'type' => $node->get('type')->target_id,
        'id' => $node->get('nid')->value,
        'attributes' => [
          'title' =>  $node->get('title')->value,
          'body' => $node->get('body')->value,
          'date' => $node->get('field_date')->value,
          'boolean' => $node->get('field_bool')->value,
          'description' => $node->get('field_description')->value,
          'link' => $node->get('field_link')->uri,
          'email' => $node->get('field_email')->value,
          'list' => $node->get('field_list')->value,
          'number' => $node->get('field_number')->value,
          'taxonomy' => $node->get('field_taxo')->target_id,
          'changes' => $node->getChangedTime(),
      ],
      ];
      $file = File::load($fid);
          if (is_object($file)) {
          $field_image = $file->getFileUri();
         
          }
           $data =  $data + [
            'image_uri'=> $field_image,
           ];
    }
    }
      
      // $nids = \Drupal::entityQuery('node')->condition('type','api')->execute();
      // if($nids){
      // 	$date = $nids->getChangedTime();
      // 	dsm($date);
      //   $nodes =  \Drupal\node\Entity\Node::loadMultiple($nids);
      //   foreach ($nodes as $key => $value) {
      //     $data[] = ['id' => $value->id(),'title' => $value->getTitle()];
      //   }
      // }
 

   $response = new ResourceResponse($data);
   $response->addCacheableDependency($data);
  	return $response;
  }
}
