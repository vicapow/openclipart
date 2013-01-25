<?php

$app->get("/", function() use($app){
  
  echo $app->render('landing', array(
      'editable' => false // librarian functions
      , 'clipart_list' => $app->popular_clipart()
      , 'new_clipart' => $app->new_clipart()
      , 'user' => $app->user()
      , 'tags' => array_map(function($tag) {
          return array(
              'name' => $tag['name']
              , 'size' => $tag['downloads']
          );
      }, $app->tags_by_downloads())
  ));
});

?>