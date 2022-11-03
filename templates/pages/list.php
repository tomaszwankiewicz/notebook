<div class="list">
  <section>

  <div class="message">
      <?php 
      if(!empty($params['error'])) 
        {
          switch($params['error']){
            case 'missingNotId':
              echo 'Niepoprawny identyfikator notatki !';
            break;
            case 'noteNotFound':
              echo 'Notatka nie została znaleziona !';
            break;
          }
        }
      ?>
    </div>

    <div class="message">
      <?php 
      if(!empty($params['before'])) 
        {
          switch($params['before']){
            case 'created':
              echo 'Notatka została utworzona !';
            break;
          }
        }
      ?>
    </div>

    <div class="tbl-header">
      <table cellpadding="0" cellpadding="0" border="0">
        <thead>
          <tr>
            <th>Id</th>
            <th>Tytuł</th>
            <th>Data</th>
            <th>Opcje</th>
          </tr>
        </thead>
      </table>
    </div>

    <div class="tbl-content">
      <table cellpadding="0" cellpadding="0" border="0">
        <tbody>
          <?php foreach ($params['notes'] ?? [] as $note): ?> <?php //?? [] - jesli nie bedzie nic to przekaze pusta tablice?> 
            <tr>
              <td><?php echo (int) $note['id'] ?></td> <?php //zrzutujemy id na int-a -to rodzaj zabezpiecznia?>
              <td><?php echo htmlentities($note['title']) ?></td> <?php //eskejowanie - zabezpiecznie?>
              <td><?php echo htmlentities($note['created']) ?></td>
              <td>
                <a href="/?action=show&id=<?php echo (int) $note['id'] ?>">
                <button>Szczegóły</button>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
