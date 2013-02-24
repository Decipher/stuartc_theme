<div class="profile">
  <?php print theme('imagecache', 'biography_photo', $account->picture); ?>
  <dl>
    <dt>Name:</dt>
    <dd><?php print $account->name; ?></dd>
  
    <dt>Age:</dt>
    <dd><?php print $account->age; ?></dd>

    <dt>Location:</dt>
    <dd><?php print $account->profile_location; ?></dd>
  </dl>
</div>

<div class="biography">
  <?php print check_markup($account->profile_biography); ?>
</div>