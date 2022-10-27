<div>

  <div class="message">
    <?php 
    if(!empty($params['before'])) 
      {
        switch($params['before']){
          case 'created':
            echo 'Notatka została utworzona !!!';
          break;
        }
      }
    ?>
  </div>

  <h4>Lista notatek</h4>
  <b><?php echo $params['resultList'] ?? "" ?></b>
</div>